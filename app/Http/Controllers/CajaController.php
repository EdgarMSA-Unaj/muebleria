<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caja;
use App\Models\Gasto;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;

class CajaController extends Controller
{
    public function index()
    {
        $cajaActual = Caja::where('estado', 'abierta')->latest()->first();
        $historialCajas = Caja::latest('fecha_apertura')->take(10)->get();
        $gastos = Gasto::latest('fecha_gasto')->take(15)->get();

        $totalGastosMes = Gasto::whereMonth('fecha_gasto', now()->month)->sum('monto');
        $gastosPorCategoria = Gasto::selectRaw('categoria_gasto, SUM(monto) as total')
            ->groupBy('categoria_gasto')
            ->get();

        return view('caja.index', compact('cajaActual', 'historialCajas', 'gastos', 'totalGastosMes', 'gastosPorCategoria'));
    }

    public function registrarGasto(Request $request)
    {
        $validated = $request->validate([
            'categoria_gasto' => 'required|string|max:100',
            'descripcion' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0.1',
            'comprobante_tipo' => 'nullable|string',
            'comprobante_numero' => 'nullable|string',
            'fecha_gasto' => 'required|date'
        ]);

        $cajaActual = Caja::where('estado', 'abierta')->latest()->first();

        $validated['user_id'] = Auth::id() ?? 1;
        $validated['caja_id'] = $cajaActual ? $cajaActual->id : null;

        Gasto::create($validated);

        if ($cajaActual) {
            $cajaActual->increment('monto_gastos', $validated['monto']);
            $cajaActual->decrement('monto_final_esperado', $validated['monto']);
        }

        return redirect()->route('caja.index')->with('success', 'Gasto operativo registrado correctamente.');
    }

    public function abrirCaja(Request $request)
    {
        $validated = $request->validate([
            'nombre_caja' => 'required|string|max:50',
            'monto_inicial' => 'required|numeric|min:0'
        ]);

        $cajaExistente = Caja::where('estado', 'abierta')->first();
        if ($cajaExistente) {
            return back()->withErrors(['caja' => 'Ya existe una caja abierta actualmente.']);
        }

        Caja::create([
            'user_id' => Auth::id() ?? 1,
            'nombre_caja' => $validated['nombre_caja'],
            'fecha_apertura' => now(),
            'monto_inicial' => $validated['monto_inicial'],
            'monto_final_esperado' => $validated['monto_inicial'],
            'estado' => 'abierta'
        ]);

        return redirect()->route('caja.index')->with('success', 'Caja abierta con éxito.');
    }

    public function cerrarCaja(Request $request, Caja $caja)
    {
        $validated = $request->validate([
            'monto_final_real' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string'
        ]);

        $diferencia = $validated['monto_final_real'] - $caja->monto_final_esperado;

        $caja->update([
            'fecha_cierre' => now(),
            'monto_final_real' => $validated['monto_final_real'],
            'diferencia' => $diferencia,
            'estado' => 'cerrada',
            'observaciones' => $validated['observaciones']
        ]);

        return redirect()->route('caja.index')->with('success', 'Caja cerrada exitosamente.');
    }
}
