<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;

class DespachosController extends Controller
{
    public function index(Request $request)
    {
        $query = Venta::where('requiere_entrega', true)
            ->with(['cliente', 'detalles.producto']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_entrega_programada', $request->fecha);
        }

        $despachos = $query->orderBy('fecha_entrega_programada', 'asc')->paginate(10)->withQueryString();

        $pendientesEntrega = Venta::where('requiere_entrega', true)->whereIn('estado', ['pendiente', 'en_entrega'])->count();
        $entregadosMes = Venta::where('requiere_entrega', true)->where('estado', 'completada')->whereMonth('fecha_emision', now()->month)->count();
        $conArmado = Venta::where('requiere_entrega', true)->where('requiere_armado', true)->whereIn('estado', ['pendiente', 'en_entrega'])->count();

        return view('despachos.index', compact('despachos', 'pendientesEntrega', 'entregadosMes', 'conArmado'));
    }

    public function asignarRepartidor(Request $request, Venta $venta)
    {
        $validated = $request->validate([
            'repartidor_asignado' => 'required|string|max:100',
            'fecha_entrega_programada' => 'required|date'
        ]);

        $venta->update([
            'repartidor_asignado' => $validated['repartidor_asignado'],
            'fecha_entrega_programada' => $validated['fecha_entrega_programada'],
            'estado' => 'en_entrega'
        ]);

        return back()->with('success', 'Transportista y fecha de entrega asignados correctamente.');
    }
}
