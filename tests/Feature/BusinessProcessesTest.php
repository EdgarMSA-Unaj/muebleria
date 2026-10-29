<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Almacen;
use App\Models\Caja;
use App\Models\Venta;
use App\Models\OrdenProduccion;
use App\Models\MovimientoStock;
use App\Models\Configuracion;
use Spatie\Permission\Models\Role;

class BusinessProcessesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    public function test_cash_sale_increments_open_cash_box(): void
    {
        $admin = User::first();
        $cliente = Cliente::first();
        $producto = Producto::first();
        $almacen = Almacen::first();

        // Ensure an open caja
        Caja::where('estado', 'abierta')->update(['estado' => 'cerrada']);
        $caja = Caja::create([
            'user_id' => $admin->id,
            'nombre_caja' => 'Caja Test',
            'fecha_apertura' => now(),
            'monto_inicial' => 200,
            'monto_ventas_efectivo' => 0,
            'monto_ventas_digital' => 0,
            'monto_gastos' => 0,
            'monto_final_esperado' => 200,
            'estado' => 'abierta'
        ]);

        $response = $this->actingAs($admin)->post('/ventas', [
            'cliente_id' => $cliente->id,
            'tipo_comprobante' => 'boleta',
            'condicion_pago' => 'contado',
            'productos' => [
                [
                    'producto_id' => $producto->id,
                    'cantidad' => 2,
                    'precio_unitario' => 500
                ]
            ]
        ]);

        $response->assertRedirect('/ventas');

        $caja->refresh();
        // Subtotal = 1000, IGV 18% = 180, Total = 1180
        $this->assertEquals(1180, $caja->monto_ventas_efectivo);
        $this->assertEquals(1380, $caja->monto_final_esperado);
    }

    public function test_sale_cancellation_restores_stock_and_kardex(): void
    {
        $admin = User::first();
        $venta = Venta::with('detalles')->first();
        $this->assertNotNull($venta);

        $detalle = $venta->detalles->first();
        $producto = Producto::find($detalle->producto_id);
        $stockAntes = $producto->stock_actual;

        $response = $this->actingAs($admin)->post("/ventas/{$venta->id}/cambiar-estado", [
            'estado' => 'anulada'
        ]);

        $response->assertSessionHasNoErrors();
        $producto->refresh();
        $this->assertEquals($stockAntes + $detalle->cantidad, $producto->stock_actual);

        $this->assertDatabaseHas('movimientos_stock', [
            'producto_id' => $producto->id,
            'tipo_movimiento' => 'devolucion_cliente',
            'referencia_id' => $venta->id
        ]);
    }

    public function test_production_order_completion_creates_kardex_and_advances_sale(): void
    {
        $admin = User::first();
        $producto = Producto::first();
        $venta = Venta::where('estado', 'pendiente')->first() ?? Venta::first();
        $venta->update(['estado' => 'pendiente']);

        $orden = OrdenProduccion::create([
            'codigo' => 'OP-TEST-001',
            'producto_id' => $producto->id,
            'venta_id' => $venta->id,
            'fase' => 'control_calidad',
            'avance_porcentaje' => 95,
            'fecha_inicio' => now()->toDateString()
        ]);

        $response = $this->actingAs($admin)->post("/produccion/{$orden->id}/avanzar");
        $response->assertSessionHasNoErrors();

        $orden->refresh();
        $this->assertEquals('terminado', $orden->fase);

        $venta->refresh();
        $this->assertEquals('en_entrega', $venta->estado);

        $this->assertDatabaseHas('movimientos_stock', [
            'producto_id' => $producto->id,
            'tipo_movimiento' => 'ajuste_positivo',
            'referencia_id' => $orden->id
        ]);
    }

    public function test_user_update_and_active_toggle(): void
    {
        $admin = User::first();
        $user = User::where('id', '!=', $admin->id)->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Empleado Test',
                'email' => 'empleado@test.com',
                'password' => bcrypt('secret123'),
                'is_active' => true
            ]);
            $user->assignRole('Vendedor');
        }

        // Test update
        $response = $this->actingAs($admin)->put("/usuarios/{$user->id}", [
            'name' => 'Empleado Modificado',
            'email' => $user->email,
            'phone' => '999888777',
            'role' => 'Almacenero'
        ]);

        $response->assertRedirect('/usuarios');
        $user->refresh();
        $this->assertEquals('Empleado Modificado', $user->name);
        $this->assertTrue($user->hasRole('Almacenero'));

        // Test toggle
        $this->actingAs($admin)->post("/usuarios/{$user->id}/toggle");
        $user->refresh();
        $this->assertFalse((bool)$user->is_active);

        // Self-toggle prevention
        $selfToggle = $this->actingAs($admin)->post("/usuarios/{$admin->id}/toggle");
        $selfToggle->assertSessionHasErrors(['error']);
    }

    public function test_company_configuration_save(): void
    {
        $admin = User::first();

        $response = $this->actingAs($admin)->post('/configuracion', [
            'nombre_empresa' => 'Muebles Elegance Perú SAC',
            'ruc' => '20609999999',
            'direccion' => 'Av. El Sol 450, Lima',
            'telefono' => '987654321',
            'email' => 'contacto@muebleselegance.pe',
            'sitio_web' => 'https://muebleselegance.pe',
            'moneda_simbolo' => 'S/.',
            'porcentaje_igv' => 18,
            'serie_boleta' => 'B001',
            'serie_factura' => 'F001',
            'serie_nota_venta' => 'NV01',
            'serie_cotizacion' => 'COT1',
            'terminos_garantia' => 'Garantía legal de 12 meses contra defectos de fabricación.'
        ]);

        $response->assertRedirect('/configuracion');
        $this->assertDatabaseHas('configuracion', [
            'nombre_empresa' => 'Muebles Elegance Perú SAC',
            'ruc' => '20609999999'
        ]);
    }

    public function test_cliente_quick_registration_via_ajax(): void
    {
        $admin = User::first();

        // 1. Register natural person via JSON
        $response = $this->actingAs($admin)->postJson('/clientes', [
            'tipo' => 'persona_natural',
            'nombre' => 'María Elena',
            'apellidos' => 'Vargas Llosa',
            'documento_tipo' => 'DNI',
            'documento_numero' => '47891234',
            'celular' => '998877665',
            'direccion' => 'Av. Larco 742, Miraflores',
            'distrito' => 'Miraflores',
            'ciudad' => 'Lima'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'cliente' => [
                'nombre_completo' => 'María Elena Vargas Llosa',
                'documento_numero' => '47891234',
                'documento_tipo' => 'DNI'
            ]
        ]);

        // 2. Register enterprise client via JSON
        $responseEmpresa = $this->actingAs($admin)->postJson('/clientes', [
            'tipo' => 'empresa',
            'razon_social' => 'Inmobiliaria Horizonte SAC',
            'documento_tipo' => 'RUC',
            'documento_numero' => '20778899001',
            'celular' => '912345678',
            'direccion' => 'Av. Javier Prado Este 2400',
            'distrito' => 'San Borja',
            'ciudad' => 'Lima'
        ]);

        $responseEmpresa->assertStatus(200);
        $responseEmpresa->assertJson([
            'success' => true,
            'cliente' => [
                'nombre_completo' => 'Inmobiliaria Horizonte SAC',
                'documento_numero' => '20778899001',
                'documento_tipo' => 'RUC'
            ]
        ]);
    }
}

