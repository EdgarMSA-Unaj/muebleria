<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Almacen;
use App\Models\MovimientoStock;
use App\Models\Caja;
use App\Models\Configuracion;
use App\Models\Categoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VentasController extends Controller
{
    public function index(Request $request)
    {
        $query = Venta::with(['cliente', 'usuario', 'detalles.producto']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo_comprobante')) {
            $query->where('tipo_comprobante', $request->tipo_comprobante);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('numero_comprobante', 'like', "%{$s}%")
                  ->orWhereHas('cliente', function($c) use ($s) {
                      $c->where('nombre', 'like', "%{$s}%")
                        ->orWhere('razon_social', 'like', "%{$s}%")
                        ->orWhere('documento_numero', 'like', "%{$s}%");
                  });
            });
        }

        $ventas = $query->latest('fecha_emision')->paginate(10)->withQueryString();

        $totalRecaudado = Venta::where('estado', '!=', 'anulada')->where('estado', '!=', 'cotizacion')->sum('total');
        $pedidosPendientes = Venta::whereIn('estado', ['pendiente', 'en_entrega'])->count();
        $cotizacionesCount = Venta::where('estado', 'cotizacion')->count();

        return view('ventas.index', compact('ventas', 'totalRecaudado', 'pedidosPendientes', 'cotizacionesCount'));
    }

    public function create()
    {
        $clientes = Cliente::where('activo', true)->orderBy('nombre')->get();
        $productos = Producto::with('categoria')->where('activo', true)->where('stock_actual', '>', 0)->orderBy('nombre')->get();
        $categorias = Categoria::where('activo', true)->get();
        $config = Configuracion::first();

        return view('ventas.create', compact('clientes', 'productos', 'categorias', 'config'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_comprobante' => 'required|in:boleta,factura,nota_venta,cotizacion',
            'condicion_pago' => 'required|string',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
            'requiere_entrega' => 'nullable|boolean',
            'requiere_armado' => 'nullable|boolean',
            'direccion_entrega' => 'nullable|string',
            'fecha_entrega_programada' => 'nullable|date',
            'observaciones' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $config = Configuracion::first();
            $serie = match($validated['tipo_comprobante']) {
                'factura' => $config->serie_factura ?? 'F001',
                'boleta' => $config->serie_boleta ?? 'B001',
                'cotizacion' => $config->serie_cotizacion ?? 'COT1',
                default => $config->serie_nota_venta ?? 'NV01'
            };

            $correlativo = Venta::where('tipo_comprobante', $validated['tipo_comprobante'])->count() + 1;
            $numeroComprobante = $serie . '-' . str_pad($correlativo, 7, '0', STR_PAD_LEFT);

            $subtotal = 0;
            foreach ($validated['productos'] as $item) {
                $subtotal += $item['cantidad'] * $item['precio_unitario'];
            }

            $igv = round($subtotal * (($config->porcentaje_igv ?? 18) / 100), 2);
            $total = $subtotal + $igv;

            $esCotizacion = $validated['tipo_comprobante'] === 'cotizacion';
            $estado = $esCotizacion ? 'cotizacion' : ($request->filled('requiere_entrega') ? 'en_entrega' : 'completada');

            $venta = Venta::create([
                'numero_comprobante' => $numeroComprobante,
                'tipo_comprobante' => $validated['tipo_comprobante'],
                'cliente_id' => $validated['cliente_id'],
                'user_id' => Auth::id() ?? 1,
                'fecha_emision' => now()->format('Y-m-d'),
                'condicion_pago' => $validated['condicion_pago'],
                'estado' => $estado,
                'subtotal' => $subtotal,
                'igv' => $igv,
                'descuento' => 0,
                'total' => $total,
                'monto_pagado' => $esCotizacion ? 0 : $total,
                'saldo' => $esCotizacion ? $total : 0,
                'requiere_entrega' => $request->boolean('requiere_entrega'),
                'requiere_armado' => $request->boolean('requiere_armado'),
                'direccion_entrega' => $validated['direccion_entrega'] ?? null,
                'fecha_entrega_programada' => $validated['fecha_entrega_programada'] ?? null,
                'observaciones' => $validated['observaciones'] ?? null
            ]);

            $almacen = Almacen::where('principal', true)->first() ?? Almacen::first();

            foreach ($validated['productos'] as $item) {
                $subItem = $item['cantidad'] * $item['precio_unitario'];
                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item['producto_id'],
                    'almacen_id' => $almacen ? $almacen->id : 1,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $subItem,
                    'color_acabado' => $item['color_acabado'] ?? null
                ]);

                // Descontar inventario solo si no es cotización
                if (!$esCotizacion) {
                    $producto = Producto::find($item['producto_id']);
                    if ($producto) {
                        $stockAnt = $producto->stock_actual;
                        $stockNue = max(0, $stockAnt - $item['cantidad']);
                        $producto->update(['stock_actual' => $stockNue]);

                        MovimientoStock::create([
                            'producto_id' => $producto->id,
                            'almacen_id' => $almacen ? $almacen->id : 1,
                            'user_id' => Auth::id() ?? 1,
                            'tipo_movimiento' => 'salida_venta',
                            'cantidad' => $item['cantidad'],
                            'stock_anterior' => $stockAnt,
                            'stock_nuevo' => $stockNue,
                            'referencia_tipo' => 'Venta',
                            'referencia_id' => $venta->id,
                            'motivo' => "Venta {$venta->numero_comprobante}"
                        ]);
                    }
                }
            }

            // Integración financiera con Caja si no es cotización
            if (!$esCotizacion) {
                $cajaAbierta = Caja::where('estado', 'abierta')->latest()->first();
                if ($cajaAbierta) {
                    $esEfectivo = in_array(strtolower($validated['condicion_pago']), ['efectivo', 'contado']);
                    if ($esEfectivo) {
                        $cajaAbierta->increment('monto_ventas_efectivo', $total);
                        $cajaAbierta->increment('monto_final_esperado', $total);
                    } else {
                        $cajaAbierta->increment('monto_ventas_digital', $total);
                    }
                }
            }

            DB::commit();
            return redirect()->route('ventas.index')->with('success', "Venta / Pedido {$venta->numero_comprobante} registrado con éxito.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al procesar la venta: ' . $e->getMessage()]);
        }
    }

    public function cambiarEstado(Request $request, Venta $venta)
    {
        $request->validate(['estado' => 'required|in:pendiente,en_entrega,entregado,completada,anulada']);
        
        $estadoAnterior = $venta->estado;
        $nuevoEstado = $request->estado;

        if ($estadoAnterior === $nuevoEstado) {
            return back()->with('info', 'El estado actual ya es: ' . ucfirst(str_replace('_', ' ', $nuevoEstado)));
        }

        DB::beginTransaction();
        try {
            // Si pasa a anulada y antes no estaba anulada ni era cotización
            if ($nuevoEstado === 'anulada' && $estadoAnterior !== 'anulada' && $estadoAnterior !== 'cotizacion') {
                $almacen = Almacen::where('principal', true)->first() ?? Almacen::first();
                $almacenId = $almacen ? $almacen->id : 1;

                foreach ($venta->detalles as $det) {
                    $producto = Producto::find($det->producto_id);
                    if ($producto) {
                        $stockAnt = $producto->stock_actual;
                        $stockNue = $stockAnt + $det->cantidad;
                        $producto->update(['stock_actual' => $stockNue]);

                        MovimientoStock::create([
                            'producto_id' => $producto->id,
                            'almacen_id' => $det->almacen_id ?? $almacenId,
                            'user_id' => Auth::id() ?? 1,
                            'tipo_movimiento' => 'devolucion_cliente',
                            'cantidad' => $det->cantidad,
                            'stock_anterior' => $stockAnt,
                            'stock_nuevo' => $stockNue,
                            'referencia_tipo' => 'Venta',
                            'referencia_id' => $venta->id,
                            'motivo' => "Devolución por anulación de comprobante {$venta->numero_comprobante}"
                        ]);
                    }
                }

                // Si la venta fue cobrada y la caja está abierta, ajustar el saldo de la caja
                $cajaAbierta = Caja::where('estado', 'abierta')->latest()->first();
                if ($cajaAbierta) {
                    $esEfectivo = in_array(strtolower($venta->condicion_pago), ['efectivo', 'contado']);
                    if ($esEfectivo) {
                        $cajaAbierta->decrement('monto_ventas_efectivo', min($cajaAbierta->monto_ventas_efectivo, $venta->total));
                        $cajaAbierta->decrement('monto_final_esperado', min($cajaAbierta->monto_final_esperado, $venta->total));
                    } else {
                        $cajaAbierta->decrement('monto_ventas_digital', min($cajaAbierta->monto_ventas_digital, $venta->total));
                    }
                }
            }

            // Si estaba anulada y se reactiva (completada, en_entrega, pendiente)
            if ($estadoAnterior === 'anulada' && in_array($nuevoEstado, ['pendiente', 'en_entrega', 'entregado', 'completada'])) {
                $almacen = Almacen::where('principal', true)->first() ?? Almacen::first();
                $almacenId = $almacen ? $almacen->id : 1;

                foreach ($venta->detalles as $det) {
                    $producto = Producto::find($det->producto_id);
                    if ($producto) {
                        $stockAnt = $producto->stock_actual;
                        $stockNue = max(0, $stockAnt - $det->cantidad);
                        $producto->update(['stock_actual' => $stockNue]);

                        MovimientoStock::create([
                            'producto_id' => $producto->id,
                            'almacen_id' => $det->almacen_id ?? $almacenId,
                            'user_id' => Auth::id() ?? 1,
                            'tipo_movimiento' => 'salida_venta',
                            'cantidad' => $det->cantidad,
                            'stock_anterior' => $stockAnt,
                            'stock_nuevo' => $stockNue,
                            'referencia_tipo' => 'Venta',
                            'referencia_id' => $venta->id,
                            'motivo' => "Reactivación de comprobante {$venta->numero_comprobante}"
                        ]);
                    }
                }
            }

            $venta->update(['estado' => $nuevoEstado]);
            DB::commit();

            return back()->with('success', 'Estado del comprobante actualizado a: ' . ucfirst(str_replace('_', ' ', $nuevoEstado)));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar estado: ' . $e->getMessage()]);
        }
    }

    public function exportarCsv(Request $request)
    {
        $query = Venta::with(['cliente', 'usuario', 'detalles.producto']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo_comprobante')) {
            $query->where('tipo_comprobante', $request->tipo_comprobante);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('numero_comprobante', 'like', "%{$s}%")
                  ->orWhereHas('cliente', function($c) use ($s) {
                      $c->where('nombre', 'like', "%{$s}%")
                        ->orWhere('razon_social', 'like', "%{$s}%")
                        ->orWhere('documento_numero', 'like', "%{$s}%");
                  });
            });
        }

        $ventas = $query->latest('fecha_emision')->get();

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=ventas_pedidos_muebleria_" . date('Y-m-d') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($ventas) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'N° Comprobante', 'Tipo', 'Fecha Emisión', 'Cliente',
                'Documento', 'Teléfono', 'Condición Pago', 'Subtotal (S/.)',
                'IGV (S/.)', 'Total (S/.)', 'Saldo Pendiente (S/.)',
                'Estado', 'Requiere Entrega', 'Requiere Armado',
                'Dirección Entrega', 'Fecha Entrega Programada', 'Vendedor'
            ]);

            foreach ($ventas as $v) {
                fputcsv($file, [
                    $v->numero_comprobante,
                    ucfirst(str_replace('_', ' ', $v->tipo_comprobante)),
                    $v->fecha_emision ? $v->fecha_emision->format('d/m/Y') : '',
                    $v->cliente->nombre_completo ?? 'Cliente Mostrador',
                    $v->cliente->documento_numero ?? '',
                    $v->cliente->celular ?? '',
                    ucfirst(str_replace('_', ' ', $v->condicion_pago)),
                    number_format($v->subtotal, 2, '.', ''),
                    number_format($v->igv, 2, '.', ''),
                    number_format($v->total, 2, '.', ''),
                    number_format($v->saldo, 2, '.', ''),
                    ucfirst(str_replace('_', ' ', $v->estado)),
                    $v->requiere_entrega ? 'SÍ' : 'NO',
                    $v->requiere_armado ? 'SÍ' : 'NO',
                    $v->direccion_entrega ?? '',
                    $v->fecha_entrega_programada ? $v->fecha_entrega_programada->format('d/m/Y') : '',
                    $v->usuario->name ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function imprimir(Venta $venta)
    {
        $venta->load(['cliente', 'usuario', 'detalles.producto.categoria', 'detalles.almacen']);
        $config = Configuracion::first() ?? new Configuracion();
        return view('ventas.imprimir', compact('venta', 'config'));
    }

    public function descargarPdf(Venta $venta)
    {
        $venta->load(['cliente', 'usuario', 'detalles.producto.categoria', 'detalles.almacen']);
        $config = Configuracion::first() ?? new Configuracion();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ventas.pdf', compact('venta', 'config'));
        $pdf->setPaper('a4', 'portrait');

        $fileName = strtolower(str_replace('_', '-', $venta->tipo_comprobante)) . '-' . $venta->numero_comprobante . '.pdf';
        return $pdf->download($fileName);
    }
}
