<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrdenProduccion;
use App\Models\Producto;
use App\Models\Venta;

class ProduccionSeeder extends Seeder
{
    public function run(): void
    {
        $p1 = Producto::where('sku', 'SOF-ROB-001')->first();
        $p2 = Producto::where('sku', 'COM-CED-002')->first();
        $p3 = Producto::where('sku', 'DOR-KIN-003')->first();
        $v1 = Venta::first();

        if ($p1) {
            OrdenProduccion::create([
                'codigo' => 'OP-2026-0001',
                'producto_id' => $p1->id,
                'venta_id' => $v1 ? $v1->id : null,
                'maestro_carpintero' => 'Don Santos Huamán',
                'maestro_tapicero' => 'Ricardo Córdova',
                'fecha_inicio' => now()->subDays(5),
                'fecha_estimada_termino' => now()->addDays(3),
                'fase' => 'tapizado',
                'avance_porcentaje' => 80,
                'especificaciones_acabado' => 'Tapiz Lino Suizo Gris Perla, espuma Zebra 28kg alta densidad',
                'observaciones_taller' => 'Patas torneadas en roble oscuro terminadas y laqueadas.'
            ]);
        }

        if ($p2) {
            OrdenProduccion::create([
                'codigo' => 'OP-2026-0002',
                'producto_id' => $p2->id,
                'maestro_carpintero' => 'Marcos Benites',
                'maestro_tapicero' => 'Alberto Mendoza',
                'fecha_inicio' => now()->subDays(8),
                'fecha_estimada_termino' => now()->addDays(2),
                'fase' => 'laqueado_pintura',
                'avance_porcentaje' => 60,
                'especificaciones_acabado' => 'Madera cedro macizo acabado poliuretano satinado nogal',
                'observaciones_taller' => 'Mesa de 6 puestos armada, en proceso de segunda mano de laca.'
            ]);
        }

        if ($p3) {
            OrdenProduccion::create([
                'codigo' => 'OP-2026-0003',
                'producto_id' => $p3->id,
                'maestro_carpintero' => 'Don Santos Huamán',
                'fecha_inicio' => now()->subDays(2),
                'fecha_estimada_termino' => now()->addDays(6),
                'fase' => 'armado_esqueleto',
                'avance_porcentaje' => 35,
                'especificaciones_acabado' => 'Estructura King size con refuerzo metálico y tiras led',
                'observaciones_taller' => 'Esqueleto de fierro y madera tornillo terminado.'
            ]);
        }
    }
}
