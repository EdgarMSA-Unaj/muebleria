<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;

class ProveedoresController extends Controller
{
    public function index(Request $request)
    {
        $query = Proveedor::withCount('compras');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('razon_social', 'like', "%{$s}%")
                  ->orWhere('nombre_comercial', 'like', "%{$s}%")
                  ->orWhere('ruc', 'like', "%{$s}%")
                  ->orWhere('contacto_nombre', 'like', "%{$s}%");
            });
        }

        $proveedores = $query->latest()->paginate(10)->withQueryString();
        $totalProveedores = Proveedor::count();
        $saldoPorPagar = Proveedor::sum('saldo_pendiente');

        return view('proveedores.index', compact('proveedores', 'totalProveedores', 'saldoPorPagar'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'razon_social' => 'required|string|max:200',
            'nombre_comercial' => 'nullable|string|max:200',
            'ruc' => 'nullable|string|max:20|unique:proveedores,ruc',
            'contacto_nombre' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'direccion' => 'nullable|string|max:255',
            'banco' => 'nullable|string|max:100',
            'cuenta_bancaria' => 'nullable|string|max:50',
            'dias_credito' => 'nullable|integer|min:0'
        ]);

        $correlativo = Proveedor::count() + 1;
        $validated['codigo'] = 'PRV-' . str_pad($correlativo, 3, '0', STR_PAD_LEFT);
        $validated['activo'] = true;

        Proveedor::create($validated);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado exitosamente.');
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'razon_social' => 'required|string|max:200',
            'nombre_comercial' => 'nullable|string|max:200',
            'ruc' => 'nullable|string|max:20|unique:proveedores,ruc,' . $proveedor->id,
            'contacto_nombre' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'direccion' => 'nullable|string|max:255',
            'banco' => 'nullable|string|max:100',
            'cuenta_bancaria' => 'nullable|string|max:50',
            'dias_credito' => 'nullable|integer|min:0'
        ]);

        $proveedor->update($validated);

        return redirect()->route('proveedores.index')->with('success', "Proveedor '{$proveedor->razon_social}' actualizado exitosamente.");
    }

    public function destroy(Proveedor $proveedor)
    {
        if ($proveedor->compras()->count() > 0) {
            return back()->withErrors(['error' => 'No se puede eliminar el proveedor porque tiene órdenes de compra asociadas.']);
        }

        $nombre = $proveedor->razon_social;
        $proveedor->delete();

        return redirect()->route('proveedores.index')->with('info', "Proveedor '{$nombre}' eliminado exitosamente.");
    }

    public function registrarPago(Request $request, Proveedor $proveedor)
    {
        $maxAbono = max(0.01, (float)$proveedor->saldo_pendiente);

        $validated = $request->validate([
            'monto' => 'required|numeric|min:0.50|max:' . $maxAbono,
            'metodo_pago' => 'required|in:efectivo,yape_plin,transferencia,cheque',
            'comprobante_numero' => 'nullable|string|max:50',
            'notas' => 'nullable|string|max:255'
        ]);

        $monto = (float)$validated['monto'];

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $nuevoSaldo = max(0, $proveedor->saldo_pendiente - $monto);
            $proveedor->update(['saldo_pendiente' => $nuevoSaldo]);

            // Registrar el gasto operativo
            $cajaActual = \App\Models\Caja::where('estado', 'abierta')->latest()->first();

            \App\Models\Gasto::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
                'caja_id' => $cajaActual ? $cajaActual->id : null,
                'categoria_gasto' => 'pago_proveedor',
                'descripcion' => "Pago/Amortización a proveedor {$proveedor->razon_social}" . (!empty($validated['notas']) ? " - {$validated['notas']}" : ''),
                'monto' => $monto,
                'comprobante_tipo' => $validated['metodo_pago'],
                'comprobante_numero' => $validated['comprobante_numero'] ?? null,
                'fecha_gasto' => now()->toDateString()
            ]);

            // Si hay caja abierta y se pagó en efectivo, restar de la gaveta de caja
            if ($cajaActual) {
                if ($validated['metodo_pago'] === 'efectivo') {
                    $cajaActual->increment('monto_gastos', $monto);
                    $cajaActual->decrement('monto_final_esperado', $monto);
                } else {
                    $cajaActual->increment('monto_gastos', $monto);
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('proveedores.index')->with('success', "Pago de S/. " . number_format($monto, 2) . " registrado con éxito a {$proveedor->razon_social}. Saldo deudor restante: S/. " . number_format($nuevoSaldo, 2));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar el pago: ' . $e->getMessage()]);
        }
    }
}
