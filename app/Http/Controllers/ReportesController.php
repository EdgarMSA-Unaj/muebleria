<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Gasto;
use App\Models\Categoria;
use App\Models\Configuracion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    public function index(Request $request)
    {
        // Rango de fechas: Por defecto el mes en curso
        $fechaDesde = $request->input('fecha_desde', Carbon::now()->startOfMonth()->toDateString());
        $fechaHasta = $request->input('fecha_hasta', Carbon::now()->toDateString());

        // 1. Ingresos por ventas en el período
        $ventasQuery = Venta::where('estado', '!=', 'anulada')
            ->where('estado', '!=', 'cotizacion')
            ->whereBetween('fecha_emision', [$fechaDesde, $fechaHasta]);

        $totalVentas = (clone $ventasQuery)->sum('total');
        $totalPedidos = (clone $ventasQuery)->count();

        // 2. Costo de mercancía / muebles vendidos en el período
        $totalCostos = Venta::join('venta_detalles', 'ventas.id', '=', 'venta_detalles.venta_id')
            ->join('productos', 'venta_detalles.producto_id', '=', 'productos.id')
            ->where('ventas.estado', '!=', 'anulada')
            ->where('ventas.estado', '!=', 'cotizacion')
            ->whereBetween('ventas.fecha_emision', [$fechaDesde, $fechaHasta])
            ->selectRaw('SUM(venta_detalles.cantidad * productos.precio_compra) as total_costo')
            ->value('total_costo') ?? 0;

        // 3. Gastos operativos en el período
        $totalGastos = Gasto::whereBetween('fecha_gasto', [$fechaDesde, $fechaHasta])->sum('monto');

        // 4. Rentabilidad
        $utilidadBruta = $totalVentas - $totalCostos;
        $utilidadNeta = $utilidadBruta - $totalGastos;
        $margenBruto = $totalVentas > 0 ? round(($utilidadBruta / $totalVentas) * 100, 1) : 0;
        $margenNeto = $totalVentas > 0 ? round(($utilidadNeta / $totalVentas) * 100, 1) : 0;

        // 5. Ranking de Muebles Más Vendidos en el período
        $topProductos = Producto::with('categoria')
            ->withCount(['ventaDetalles' => function($q) use ($fechaDesde, $fechaHasta) {
                $q->whereHas('venta', function($v) use ($fechaDesde, $fechaHasta) {
                    $v->where('estado', '!=', 'anulada')
                      ->where('estado', '!=', 'cotizacion')
                      ->whereBetween('fecha_emision', [$fechaDesde, $fechaHasta]);
                });
            }])
            ->orderBy('venta_detalles_count', 'desc')
            ->take(5)
            ->get();

        // 6. Ranking de Clientes Principales en el período
        $topClientes = Cliente::withCount(['ventas' => function($q) use ($fechaDesde, $fechaHasta) {
                $q->where('estado', '!=', 'anulada')
                  ->where('estado', '!=', 'cotizacion')
                  ->whereBetween('fecha_emision', [$fechaDesde, $fechaHasta]);
            }])
            ->orderBy('ventas_count', 'desc')
            ->take(5)
            ->get();

        // 7. Desglose de gastos por categoría en el período
        $gastosPorCategoria = Gasto::whereBetween('fecha_gasto', [$fechaDesde, $fechaHasta])
            ->selectRaw('categoria_gasto, SUM(monto) as total')
            ->groupBy('categoria_gasto')
            ->orderBy('total', 'desc')
            ->get();

        return view('reportes.index', compact(
            'totalVentas',
            'totalPedidos',
            'totalCostos',
            'totalGastos',
            'utilidadBruta',
            'utilidadNeta',
            'margenBruto',
            'margenNeto',
            'topProductos',
            'topClientes',
            'gastosPorCategoria',
            'fechaDesde',
            'fechaHasta'
        ));
    }

    public function exportarCsv(Request $request)
    {
        $fechaDesde = $request->input('fecha_desde', Carbon::now()->startOfMonth()->toDateString());
        $fechaHasta = $request->input('fecha_hasta', Carbon::now()->toDateString());

        $ventas = Venta::with(['cliente', 'usuario', 'detalles.producto'])
            ->where('estado', '!=', 'anulada')
            ->where('estado', '!=', 'cotizacion')
            ->whereBetween('fecha_emision', [$fechaDesde, $fechaHasta])
            ->orderBy('fecha_emision', 'asc')
            ->get();

        $totalVentas = $ventas->sum('total');

        $totalCostos = Venta::join('venta_detalles', 'ventas.id', '=', 'venta_detalles.venta_id')
            ->join('productos', 'venta_detalles.producto_id', '=', 'productos.id')
            ->where('ventas.estado', '!=', 'anulada')
            ->where('ventas.estado', '!=', 'cotizacion')
            ->whereBetween('ventas.fecha_emision', [$fechaDesde, $fechaHasta])
            ->selectRaw('SUM(venta_detalles.cantidad * productos.precio_compra) as total_costo')
            ->value('total_costo') ?? 0;

        $totalGastos = Gasto::whereBetween('fecha_gasto', [$fechaDesde, $fechaHasta])->sum('monto');
        $utilidadBruta = $totalVentas - $totalCostos;
        $utilidadNeta = $utilidadBruta - $totalGastos;

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=reporte_financiero_muebleria_{$fechaDesde}_{$fechaHasta}.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($fechaDesde, $fechaHasta, $totalVentas, $totalCostos, $totalGastos, $utilidadBruta, $utilidadNeta, $ventas) {
            $file = fopen('php://output', 'w');
            // BOM UTF-8 para visualización en Excel sin caracteres extraños
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Título
            fputcsv($file, ['REPORTE FINANCIERO Y ESTADO DE RESULTADOS - MUEBLERÍA ELEGANCE']);
            fputcsv($file, ['Período:', "Del {$fechaDesde} al {$fechaHasta}"]);
            fputcsv($file, ['Fecha de Exportación:', date('d/m/Y H:i:s')]);
            fputcsv($file, []);

            // Balance
            fputcsv($file, ['RESUMEN EJECUTIVO']);
            fputcsv($file, ['Concepto', 'Monto (S/.)']);
            fputcsv($file, ['Ingresos Totales por Ventas', number_format($totalVentas, 2, '.', '')]);
            fputcsv($file, ['Costo de Adquisición / Mercadería', number_format($totalCostos, 2, '.', '')]);
            fputcsv($file, ['Margen / Utilidad Bruta', number_format($utilidadBruta, 2, '.', '')]);
            fputcsv($file, ['Gastos Operativos & Taller', number_format($totalGastos, 2, '.', '')]);
            fputcsv($file, ['Utilidad Neta Real', number_format($utilidadNeta, 2, '.', '')]);
            fputcsv($file, []);

            // Detalle de Ventas
            fputcsv($file, ['DETALLE DE VENTAS DEL PERÍODO']);
            fputcsv($file, ['Comprobante', 'Tipo', 'Fecha', 'Cliente', 'Documento', 'Subtotal (S/.)', 'IGV (S/.)', 'Total (S/.)', 'Condición Pago', 'Estado']);

            foreach ($ventas as $v) {
                fputcsv($file, [
                    $v->numero_comprobante,
                    ucfirst($v->tipo_comprobante),
                    $v->fecha_emision ? $v->fecha_emision->format('d/m/Y') : '',
                    $v->cliente->nombre_completo ?? 'Cliente Mostrador',
                    $v->cliente->documento_numero ?? '',
                    number_format($v->subtotal, 2, '.', ''),
                    number_format($v->igv, 2, '.', ''),
                    number_format($v->total, 2, '.', ''),
                    ucfirst(str_replace('_', ' ', $v->condicion_pago)),
                    ucfirst($v->estado)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
