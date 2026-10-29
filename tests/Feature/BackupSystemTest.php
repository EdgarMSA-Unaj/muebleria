<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Configuracion;
use App\Models\Almacen;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class BackupSystemTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    public function test_unauthorized_users_cannot_access_backup_module(): void
    {
        // 1. Invitado no autenticado
        $this->get('/respaldos')->assertRedirect('/login');

        // 2. Vendedor no autorizado (debe retornar 403)
        $vendedor = User::role('Vendedor')->first();
        if (!$vendedor) {
            $vendedor = User::create([
                'name' => 'Vendedor Test',
                'email' => 'vendedor.backup@test.com',
                'password' => bcrypt('password'),
                'is_active' => true
            ]);
            $vendedor->assignRole('Vendedor');
        }

        $this->actingAs($vendedor)->get('/respaldos')->assertStatus(403);
    }

    public function test_admin_can_access_backup_module(): void
    {
        $admin = User::role('Administrador')->first() ?? User::first();

        $response = $this->actingAs($admin)->get('/respaldos');
        $response->assertStatus(200);
        $response->assertSee('Copias de Seguridad');
        $response->assertSee('Historial de Copias de Seguridad');
        $response->assertSee('Inicializar Nueva Empresa');
    }

    public function test_admin_can_create_sql_and_zip_backups(): void
    {
        $admin = User::role('Administrador')->first() ?? User::first();

        // 1. Crear backup SQL
        $responseSql = $this->actingAs($admin)->post('/respaldos/crear', [
            'tipo' => 'sql'
        ]);
        $responseSql->assertRedirect('/respaldos');
        $responseSql->assertSessionHas('success');

        // 2. Crear backup ZIP
        $responseZip = $this->actingAs($admin)->post('/respaldos/crear', [
            'tipo' => 'zip'
        ]);
        $responseZip->assertRedirect('/respaldos');
        $responseZip->assertSessionHas('success');

        // Verificar que existan archivos en storage/app/backups
        $backupFiles = File::files(storage_path('app/backups'));
        $this->assertNotEmpty($backupFiles);
    }

    public function test_admin_can_download_and_delete_backup(): void
    {
        $admin = User::role('Administrador')->first() ?? User::first();

        // Generar un respaldo
        $this->actingAs($admin)->post('/respaldos/crear', ['tipo' => 'sql']);

        $files = File::files(storage_path('app/backups'));
        $this->assertNotEmpty($files);
        $firstFile = $files[0]->getFilename();

        // Probar descarga
        $responseDownload = $this->actingAs($admin)->get("/respaldos/descargar/{$firstFile}");
        $responseDownload->assertStatus(200);
        $this->assertTrue($responseDownload->headers->has('content-disposition'));

        // Probar eliminación
        $responseDelete = $this->actingAs($admin)->delete("/respaldos/{$firstFile}");
        $responseDelete->assertRedirect('/respaldos');
        $responseDelete->assertSessionHas('success');
        $this->assertFalse(File::exists(storage_path("app/backups/{$firstFile}")));
    }

    public function test_transactional_reset_clears_operations_and_preserves_catalog(): void
    {
        $admin = User::role('Administrador')->first() ?? User::first();
        $admin->password = Hash::make('secret123');
        $admin->save();

        $productosAntes = Producto::count();
        $this->assertGreaterThan(0, $productosAntes);

        $response = $this->actingAs($admin)->post('/respaldos/resetear', [
            'modo' => 'transaccional',
            'confirm_phrase' => 'RESETEAR',
            'password' => 'secret123'
        ]);

        $response->assertRedirect('/respaldos');
        $response->assertSessionHas('success');

        // Verificar que las ventas se hayan limpiado
        $this->assertEquals(0, Venta::count());

        // Verificar que los productos se preserven pero su stock sea 0
        $this->assertEquals($productosAntes, Producto::count());
        $this->assertEquals(0, Producto::sum('stock_actual'));
    }

    public function test_full_reset_initializes_new_company(): void
    {
        $admin = User::role('Administrador')->first() ?? User::first();
        $admin->password = Hash::make('secret123');
        $admin->save();

        $response = $this->actingAs($admin)->post('/respaldos/resetear', [
            'modo' => 'nueva_empresa',
            'confirm_phrase' => 'RESETEAR',
            'password' => 'secret123',
            'nombre_empresa' => 'Maderas del Norte SAC',
            'ruc' => '20771122334',
            'direccion' => 'Av. Industrial 890, Chiclayo',
            'telefono' => '944332211',
            'email' => 'ventas@maderasdelnorte.pe',
            'sitio_web' => 'https://maderasdelnorte.pe',
            'moneda_simbolo' => 'S/.',
            'porcentaje_igv' => 18,
            'serie_boleta' => 'B001',
            'serie_factura' => 'F001',
            'serie_nota_venta' => 'NV01',
            'serie_cotizacion' => 'COT1',
            'terminos_garantia' => 'Garantía extendida de 24 meses.',
            'admin_name' => 'Gerente Chiclayo',
            'admin_email' => $admin->email
        ]);

        $response->assertRedirect('/respaldos');
        $response->assertSessionHas('success');

        // Verificar nueva configuración
        $config = Configuracion::first();
        $this->assertEquals('Maderas del Norte SAC', $config->nombre_empresa);
        $this->assertEquals('20771122334', $config->ruc);
        $this->assertEquals('Av. Industrial 890, Chiclayo', $config->direccion);

        // Verificar que el catálogo comercial y ventas estén en 0
        $this->assertEquals(0, Producto::count());
        $this->assertEquals(0, Venta::count());

        // Verificar que el almacén principal por defecto se haya creado
        $this->assertEquals(1, Almacen::count());
        $this->assertEquals('Almacén Principal', Almacen::first()->nombre);
    }
}
