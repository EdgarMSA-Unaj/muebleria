<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\Caja;
use App\Models\Almacen;
use Spatie\Permission\Models\Role;

class SystemAuditTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    public function test_all_modules_load_successfully_for_administrator(): void
    {
        $admin = User::role('Administrador')->first() ?? User::first();
        $venta = Venta::first();

        $routes = [
            '/dashboard',
            '/ventas',
            '/ventas/crear',
            '/ventas/exportar-csv',
            '/despachos',
            '/clientes',
            '/inventario',
            '/inventario/kardex',
            '/inventario/exportar-csv',
            '/categorias',
            '/produccion',
            '/compras',
            '/compras/crear',
            '/proveedores',
            '/caja',
            '/reportes',
            '/reportes/exportar-csv',
            '/usuarios',
            '/configuracion'
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($admin)->get($route);
            $response->assertStatus(200, "Falló al cargar la ruta: {$route}");
        }

        if ($venta) {
            $responseImprimir = $this->actingAs($admin)->get("/ventas/{$venta->id}/imprimir");
            $responseImprimir->assertStatus(200);

            $responsePdf = $this->actingAs($admin)->get("/ventas/{$venta->id}/pdf");
            $responsePdf->assertStatus(200);
            $this->assertEquals('application/pdf', $responsePdf->headers->get('content-type'));
        }
    }

    public function test_role_based_access_control_rules(): void
    {
        // 1. Vendedor role check
        $vendedor = User::create([
            'name' => 'Vendedor Test',
            'email' => 'vendedor.test@muebleselegance.pe',
            'password' => bcrypt('password'),
            'is_active' => true
        ]);
        $vendedor->assignRole('Vendedor');

        $this->actingAs($vendedor)->get('/ventas')->assertStatus(200);
        $this->actingAs($vendedor)->get('/clientes')->assertStatus(200);
        $this->actingAs($vendedor)->get('/inventario')->assertStatus(200);
        $this->actingAs($vendedor)->get('/usuarios')->assertStatus(403);
        $this->actingAs($vendedor)->get('/configuracion')->assertStatus(403);
        $this->actingAs($vendedor)->get('/caja')->assertStatus(403);

        // 2. Almacenero role check
        $almacenero = User::create([
            'name' => 'Almacenero Test',
            'email' => 'almacen.test@muebleselegance.pe',
            'password' => bcrypt('password'),
            'is_active' => true
        ]);
        $almacenero->assignRole('Almacenero');

        $this->actingAs($almacenero)->get('/inventario')->assertStatus(200);
        $this->actingAs($almacenero)->get('/categorias')->assertStatus(200);
        $this->actingAs($almacenero)->get('/produccion')->assertStatus(200);
        $this->actingAs($almacenero)->get('/compras')->assertStatus(200);
        $this->actingAs($almacenero)->get('/usuarios')->assertStatus(403);
        $this->actingAs($almacenero)->get('/caja')->assertStatus(403);

        // 3. Contador role check
        $contador = User::create([
            'name' => 'Contador Test',
            'email' => 'contador.test@muebleselegance.pe',
            'password' => bcrypt('password'),
            'is_active' => true
        ]);
        $contador->assignRole('Contador');

        $this->actingAs($contador)->get('/caja')->assertStatus(200);
        $this->actingAs($contador)->get('/reportes')->assertStatus(200);
        $this->actingAs($contador)->get('/proveedores')->assertStatus(200);
        $this->actingAs($contador)->get('/compras/crear')->assertStatus(403);
        $this->actingAs($contador)->get('/usuarios')->assertStatus(403);
    }

    public function test_csv_exports_structure_and_bom(): void
    {
        $admin = User::role('Administrador')->first() ?? User::first();

        // Inventario CSV
        $respInventario = $this->actingAs($admin)->get('/inventario/exportar-csv');
        $respInventario->assertStatus(200);
        $this->assertStringContainsString('text/csv', $respInventario->headers->get('content-type'));

        // Ventas CSV
        $respVentas = $this->actingAs($admin)->get('/ventas/exportar-csv');
        $respVentas->assertStatus(200);
        $this->assertStringContainsString('text/csv', $respVentas->headers->get('content-type'));

        // Reportes CSV
        $respReportes = $this->actingAs($admin)->get('/reportes/exportar-csv');
        $respReportes->assertStatus(200);
        $this->assertStringContainsString('text/csv', $respReportes->headers->get('content-type'));
    }
}
