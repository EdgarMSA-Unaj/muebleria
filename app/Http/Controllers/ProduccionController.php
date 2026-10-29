<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenProduccion;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Almacen;
use App\Models\MovimientoStock;
use Illuminate\Support\Facades\Auth;

class ProduccionController extends Controller
{
    public function index(Request $request)
    {
        $query = OrdenProduccion::with(['producto.categoria', 'venta.cliente']);

        if ($request->filled('fase')) {
            $query->where('fase', $request->fase);
        }

        $ordenes = $query->latest('fecha_inicio')->paginate(10)->withQueryString();
        $productos = Producto::orderBy('nombre')->get();
        $ventas = Venta::whereIn('estado', ['pendiente', 'en_entrega', 'cotizacion'])->get();

        // Estadísticas de Taller
        $enTallerTotal = OrdenProduccion::where('fase', '!=', 'terminado')->count();
        $enTapiceria = OrdenProduccion::where('fase', 'tapizado')->count();
        $terminadosRecientes = OrdenProduccion::where('fase', 'terminado')->count();

        return view('produccion.index', compact(
            'ordenes',
            'productos',
            'ventas',
            'enTallerTotal',
            'enTapiceria',
            'terminadosRecientes'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'venta_id' => 'nullable|exists:ventas,id',
            'maestro_carpintero' => 'nullable|string|max:100',
            'maestro_tapicero' => 'nullable|string|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_estimada_termino' => 'nullable|date',
            'especificaciones_acabado' => 'nullable|string|max:255',
            'observaciones_taller' => 'nullable|string'
        ]);

        $correlativo = OrdenProduccion::count() + 1;
        $validated['codigo'] = 'OP-' . date('Y') . '-' . str_pad($correlativo, 4, '0', STR_PAD_LEFT);
        $validated['fase'] = 'corte_madera';
        $validated['avance_porcentaje'] = 15;

        OrdenProduccion::create($validated);

        return redirect()->route('produccion.index')->with('success', 'Orden de fabricación enviada al taller.');
    }

    public function cambiarFase(Request $request, OrdenProduccion $orden)
    {
        $fases = [
            'corte_madera' => ['siguiente' => 'armado_esqueleto', 'avance' => 35],
            'armado_esqueleto' => ['siguiente' => 'laqueado_pintura', 'avance' => 60],
            'laqueado_pintura' => ['siguiente' => 'tapizado', 'avance' => 80],
            'tapizado' => ['siguiente' => 'control_calidad', 'avance' => 95],
            'control_calidad' => ['siguiente' => 'terminado', 'avance' => 100],
            'terminado' => ['siguiente' => 'terminado', 'avance' => 100]
        ];

        $current = $orden->fase;
        if (isset($fases[$current])) {
            $nuevoPaso = $fases[$current]['siguiente'];
            $nuevoAvance = $fases[$current]['avance'];

            $orden->update([
                'fase' => $nuevoPaso,
                'avance_porcentaje' => $nuevoAvance
            ]);

            // Al completarse la fabricación:
            if ($nuevoPaso === 'terminado') {
                $almacen = Almacen::where('principal', true)->first() ?? Almacen::first();
                $almacenId = $almacen ? $almacen->id : 1;

                if ($orden->venta_id) {
                    // Mueble fabricado a pedido para un cliente específico
                    $venta = Venta::find($orden->venta_id);
                    if ($venta && in_array($venta->estado, ['pendiente', 'cotizacion', 'en_produccion'])) {
                        // Cambiar automáticamente a en_entrega para que aparezca en el panel de Despachos
                        $venta->update(['estado' => 'en_entrega']);
                    }

                    // Registrar en Kardex la culminación de la pieza bajo pedido
                    MovimientoStock::create([
                        'producto_id' => $orden->producto_id,
                        'almacen_id' => $almacenId,
                        'user_id' => Auth::id() ?? 1,
                        'tipo_movimiento' => 'ajuste_positivo',
                        'cantidad' => 1,
                        'stock_anterior' => $orden->producto->stock_actual,
                        'stock_nuevo' => $orden->producto->stock_actual,
                        'referencia_tipo' => 'OrdenProduccion',
                        'referencia_id' => $orden->id,
                        'motivo' => "Fabricación concluida ({$orden->codigo}) para pedido {$venta?->numero_comprobante}"
                    ]);
                } else {
                    // Mueble fabricado para stock de tienda/exhibición
                    $stockAnt = $orden->producto->stock_actual;
                    $stockNue = $stockAnt + 1;
                    $orden->producto->update(['stock_actual' => $stockNue]);

                    MovimientoStock::create([
                        'producto_id' => $orden->producto_id,
                        'almacen_id' => $almacenId,
                        'user_id' => Auth::id() ?? 1,
                        'tipo_movimiento' => 'ajuste_positivo',
                        'cantidad' => 1,
                        'stock_anterior' => $stockAnt,
                        'stock_nuevo' => $stockNue,
                        'referencia_tipo' => 'OrdenProduccion',
                        'referencia_id' => $orden->id,
                        'motivo' => "Ingreso por fabricación en taller ({$orden->codigo}) - Carpintero: {$orden->maestro_carpintero}"
                    ]);
                }
            }
        }

        return back()->with('success', "Orden {$orden->codigo} avanzada a: " . $orden->fase_label);
    }
}
