<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\Almacen;
use App\Models\MovimientoStock;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComprasController extends Controller
{
    public function index(Request $request)
    {
        $query = Compra::with(['proveedor', 'usuario', 'detalles.producto']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('numero_orden', 'like', "%{$s}%")
                  ->orWhere('factura_proveedor', 'like', "%{$s}%")
                  ->orWhereHas('proveedor', function($p) use ($s) {
                      $p->where('razon_social', 'like', "%{$s}%")
                        ->orWhere('ruc', 'like', "%{$s}%");
                  });
            });
        }

        $compras = $query->latest('fecha_emision')->paginate(10)->withQueryString();
        $totalCompras = Compra::where('estado', '!=', 'anulado')->sum('total');
        $comprasPendientes = Compra::where('estado', 'solicitado')->count();

        return view('compras.index', compact('compras', 'totalCompras', 'comprasPendientes'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('activo', true)->orderBy('razon_social')->get();
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();
        $almacenes = Almacen::where('activo', true)->get();
        $config = Configuracion::first();

        return view('compras.create', compact('proveedores', 'productos', 'almacenes', 'config'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'factura_proveedor' => 'nullable|string|max:50',
            'almacen_id' => 'required|exists:almacenes,id',
            'fecha_emision' => 'required|date',
            'fecha_entrega_esperada' => 'nullable|date',
            'condicion_pago' => 'required|string',
            'estado' => 'required|in:solicitado,recibido',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.costo_unitario' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $correlativo = Compra::count() + 1;
            $numeroOrden = 'OC-' . date('Y') . '-' . str_pad($correlativo, 5, '0', STR_PAD_LEFT);

            $subtotal = 0;
            foreach ($validated['productos'] as $item) {
                $subtotal += $item['cantidad'] * $item['costo_unitario'];
            }

            $config = Configuracion::first();
            $igvPorc = ($config->porcentaje_igv ?? 18) / 100;
            $igv = round($subtotal * $igvPorc, 2);
            $total = $subtotal + $igv;

            $esRecibido = $validated['estado'] === 'recibido';

            $compra = Compra::create([
                'numero_orden' => $numeroOrden,
                'factura_proveedor' => $validated['factura_proveedor'] ?? null,
                'proveedor_id' => $validated['proveedor_id'],
                'user_id' => Auth::id() ?? 1,
                'almacen_id' => $validated['almacen_id'],
                'fecha_emision' => $validated['fecha_emision'],
                'fecha_entrega_esperada' => $validated['fecha_entrega_esperada'] ?? null,
                'condicion_pago' => $validated['condicion_pago'],
                'estado' => $validated['estado'],
                'subtotal' => $subtotal,
                'igv' => $igv,
                'total' => $total,
                'monto_pagado' => $validated['condicion_pago'] === 'contado' ? $total : 0,
                'observaciones' => $validated['observaciones'] ?? null
            ]);

            foreach ($validated['productos'] as $item) {
                $subItem = $item['cantidad'] * $item['costo_unitario'];
                CompraDetalle::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'cantidad_recibida' => $esRecibido ? $item['cantidad'] : 0,
                    'costo_unitario' => $item['costo_unitario'],
                    'subtotal' => $subItem
                ]);

                // Si se marca como recibido de inmediato, incrementar stock y registrar kardex
                if ($esRecibido) {
                    $prod = Producto::find($item['producto_id']);
                    if ($prod) {
                        $stockAnt = $prod->stock_actual;
                        $stockNue = $stockAnt + $item['cantidad'];
                        $prod->update([
                            'stock_actual' => $stockNue,
                            'precio_compra' => $item['costo_unitario'] // Actualiza último costo de compra
                        ]);

                        MovimientoStock::create([
                            'producto_id' => $prod->id,
                            'almacen_id' => $validated['almacen_id'],
                            'user_id' => Auth::id() ?? 1,
                            'tipo_movimiento' => 'entrada_compra',
                            'cantidad' => $item['cantidad'],
                            'stock_anterior' => $stockAnt,
                            'stock_nuevo' => $stockNue,
                            'referencia_tipo' => 'Compra',
                            'referencia_id' => $compra->id,
                            'motivo' => "Ingreso por Orden de Compra {$compra->numero_orden}"
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('compras.index')->with('success', "Orden de Compra {$compra->numero_orden} registrada correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar orden de compra: ' . $e->getMessage()]);
        }
    }

    public function recibir(Request $request, Compra $compra)
    {
        if ($compra->estado === 'recibido') {
            return back()->with('info', 'Esta orden ya fue recibida en almacén.');
        }

        DB::beginTransaction();
        try {
            foreach ($compra->detalles as $detalle) {
                $detalle->update(['cantidad_recibida' => $detalle->cantidad]);

                $prod = Producto::find($detalle->producto_id);
                if ($prod) {
                    $stockAnt = $prod->stock_actual;
                    $stockNue = $stockAnt + $detalle->cantidad;
                    $prod->update([
                        'stock_actual' => $stockNue,
                        'precio_compra' => $detalle->costo_unitario
                    ]);

                    MovimientoStock::create([
                        'producto_id' => $prod->id,
                        'almacen_id' => $compra->almacen_id ?? 1,
                        'user_id' => Auth::id() ?? 1,
                        'tipo_movimiento' => 'entrada_compra',
                        'cantidad' => $detalle->cantidad,
                        'stock_anterior' => $stockAnt,
                        'stock_nuevo' => $stockNue,
                        'referencia_tipo' => 'Compra',
                        'referencia_id' => $compra->id,
                        'motivo' => "Recepción de Orden de Compra {$compra->numero_orden}"
                    ]);
                }
            }

            $compra->update(['estado' => 'recibido']);
            DB::commit();

            return redirect()->route('compras.index')->with('success', "Orden {$compra->numero_orden} recibida e ingresada al inventario.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al recibir mercadería: ' . $e->getMessage()]);
        }
    }
}
