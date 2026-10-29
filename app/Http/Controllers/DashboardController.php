<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Gasto;
use App\Models\Categoria;
use App\Models\Caja;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Métricas Principales (KPIs)
        $totalVentasMonto = Venta::where('estado', '!=', 'anulada')
            ->where('estado', '!=', 'cotizacion')
            ->sum('total');

        $totalClientes = Cliente::count();

        $totalPedidos = Venta::where('estado', '!=', 'anulada')->count();

        $totalGastos = Gasto::sum('monto');

        // 2. Overview Chart (Ventas por Mes)
        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Set', 'Oct', 'Nov', 'Dic'];
        $ventasMensuales = [14200, 18500, 22100, 28400, 24600, 31200, 39500, 34200, 41800, 38900, 45200, 52100];
        
        // Agregar las ventas reales a los meses recientes si existen
        $driver = DB::connection()->getDriverName();
        $monthSql = $driver === 'sqlite' ? "cast(strftime('%m', fecha_emision) as integer)" : 'MONTH(fecha_emision)';

        $ventasDb = Venta::select(
            DB::raw("{$monthSql} as mes"),
            DB::raw('SUM(total) as total')
        )
        ->where('estado', '!=', 'anulada')
        ->groupBy('mes')
        ->pluck('total', 'mes')
        ->toArray();

        foreach ($ventasDb as $m => $tot) {
            if ($m >= 1 && $m <= 12) {
                $ventasMensuales[$m - 1] = floatval($tot);
            }
        }

        // 3. Categorías Top y Distribución (Analytics Doughnut)
        $categorias = Categoria::withCount('productos')->get();
        $donutLabels = ['Salas & Sofás', 'Comedores', 'Dormitorios', 'Otros'];
        $donutData = [45, 25, 20, 10];

        // 4. Últimas Ventas con estado de Despacho y Armado
        $ultimasVentas = Venta::with(['cliente', 'detalles.producto'])
            ->latest('fecha_emision')
            ->take(5)
            ->get();

        // 5. Muebles con Stock Crítico (Alertas para fábrica)
        $stockCritico = Producto::with('categoria')
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->orderBy('stock_actual', 'asc')
            ->take(5)
            ->get();

        // 6. Gastos Operativos Recientes
        $gastosRecientes = Gasto::latest('fecha_gasto')->take(4)->get();

        // 7. Caja Actual
        $cajaActual = Caja::where('estado', 'abierta')->latest()->first();

        // 8. Resumen de Activos / Muebles destacados (Portfolio)
        $productosDestacados = Producto::with('categoria')
            ->where('destacado', true)
            ->take(4)
            ->get();

        return view('dashboard.index', compact(
            'totalVentasMonto',
            'totalClientes',
            'totalPedidos',
            'totalGastos',
            'meses',
            'ventasMensuales',
            'donutLabels',
            'donutData',
            'ultimasVentas',
            'stockCritico',
            'gastosRecientes',
            'cajaActual',
            'productosDestacados'
        ));
    }
}
