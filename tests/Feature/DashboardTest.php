<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardTest extends TestCase
{
    public function test_dashboard_renders_successfully_in_spanish(): void
    {
        $this->artisan('migrate');
        $this->artisan('db:seed');

        $user = User::first();
        $this->assertNotNull($user);
        if (!$user) {
            $this->markTestSkipped('No user found');
        }

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Ingresos Totales');
        $response->assertSee('Clientes Registrados');
        $response->assertSee('Pedidos y Ventas');
        $response->assertSee('Gastos Operativos');
        $response->assertSee('Resumen de Ventas');
        $response->assertSee('Distribución de Ventas');
        $response->assertSee('Muebles Más Vendidos');
        $response->assertSee('Rendimiento del Negocio');
        $response->assertSee('Balance de Muebles');
        $response->assertSee('S/.');
        $response->assertDontSee('Total Earnings');
        $response->assertDontSee('Total Customers');
        $response->assertDontSee('Total Orders');
        $response->assertDontSee('My portfolio');
    }
}
