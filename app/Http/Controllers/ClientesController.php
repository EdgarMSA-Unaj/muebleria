<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Caja;
use Illuminate\Support\Facades\DB;

class ClientesController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::withCount('ventas');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nombre', 'like', "%{$s}%")
                  ->orWhere('apellidos', 'like', "%{$s}%")
                  ->orWhere('razon_social', 'like', "%{$s}%")
                  ->orWhere('documento_numero', 'like', "%{$s}%")
                  ->orWhere('celular', 'like', "%{$s}%");
            });
        }

        $clientes = $query->latest()->paginate(10)->withQueryString();
        $totalClientes = Cliente::count();
        $clientesConSaldo = Cliente::where('saldo_pendiente', '>', 0)->count();
        $saldoTotalPendiente = Cliente::sum('saldo_pendiente');

        return view('clientes.index', compact('clientes', 'totalClientes', 'clientesConSaldo', 'saldoTotalPendiente'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:persona_natural,empresa',
            'nombre' => 'required_if:tipo,persona_natural|nullable|string|max:200',
            'apellidos' => 'nullable|string|max:200',
            'razon_social' => 'required_if:tipo,empresa|nullable|string|max:200',
            'documento_tipo' => 'required|string|max:10',
            'documento_numero' => 'nullable|string|max:20|unique:clientes,documento_numero',
            'email' => 'nullable|email',
            'celular' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'distrito' => 'nullable|string|max:100',
            'ciudad' => 'nullable|string|max:100',
            'credito_limite' => 'nullable|numeric|min:0'
        ]);

        if (empty($validated['nombre'])) {
            $validated['nombre'] = $validated['razon_social'] ?? 'Empresa';
        }

        $correlativo = Cliente::count() + 1;
        $validated['codigo'] = 'CLI-' . str_pad($correlativo, 4, '0', STR_PAD_LEFT);
        $validated['activo'] = true;

        $cliente = Cliente::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cliente registrado correctamente.',
                'cliente' => [
                    'id' => $cliente->id,
                    'codigo' => $cliente->codigo,
                    'nombre_completo' => $cliente->nombre_completo,
                    'tipo' => $cliente->tipo,
                    'documento_tipo' => $cliente->documento_tipo,
                    'documento_numero' => $cliente->documento_numero ?? '',
                    'celular' => $cliente->celular ?? $cliente->telefono ?? '',
                    'email' => $cliente->email ?? '',
                    'direccion' => $cliente->direccion ?? '',
                    'distrito' => $cliente->distrito ?? '',
                    'ciudad' => $cliente->ciudad ?? ''
                ]
            ]);
        }

        return redirect()->route('clientes.index')->with('success', 'Cliente registrado correctamente.');
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:persona_natural,empresa',
            'nombre' => 'required|string|max:200',
            'apellidos' => 'nullable|string|max:200',
            'razon_social' => 'nullable|string|max:200',
            'documento_tipo' => 'required|string|max:10',
            'documento_numero' => 'nullable|string|max:20|unique:clientes,documento_numero,' . $cliente->id,
            'email' => 'nullable|email',
            'celular' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'distrito' => 'nullable|string|max:100',
            'ciudad' => 'nullable|string|max:100',
            'credito_limite' => 'nullable|numeric|min:0'
        ]);

        $cliente->update($validated);

        return redirect()->route('clientes.index')->with('success', "Cliente '{$cliente->nombre_completo}' actualizado correctamente.");
    }

    public function registrarAbono(Request $request, Cliente $cliente)
    {
        $maxAbono = max(0.01, (float)$cliente->saldo_pendiente);

        $validated = $request->validate([
            'monto' => 'required|numeric|min:0.50|max:' . $maxAbono,
            'metodo_pago' => 'required|in:efectivo,yape_plin,transferencia,tarjeta',
            'notas' => 'nullable|string|max:255'
        ]);

        $monto = (float)$validated['monto'];

        DB::beginTransaction();
        try {
            $nuevoSaldo = max(0, $cliente->saldo_pendiente - $monto);
            $cliente->update(['saldo_pendiente' => $nuevoSaldo]);

            // Si hay caja abierta, registrar el ingreso
            $cajaAbierta = Caja::where('estado', 'abierta')->latest()->first();
            if ($cajaAbierta) {
                if ($validated['metodo_pago'] === 'efectivo') {
                    $cajaAbierta->increment('monto_ventas_efectivo', $monto);
                    $cajaAbierta->increment('monto_final_esperado', $monto);
                } else {
                    $cajaAbierta->increment('monto_ventas_digital', $monto);
                }
            }

            DB::commit();

            return redirect()->route('clientes.index')->with('success', "Abono de S/. " . number_format($monto, 2) . " registrado con éxito para {$cliente->nombre_completo}. Saldo restante: S/. " . number_format($nuevoSaldo, 2));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar el abono: ' . $e->getMessage()]);
        }
    }

    public function destroy(Cliente $cliente)
    {
        if ($cliente->ventas()->count() > 0) {
            return back()->withErrors(['error' => 'No se puede eliminar el cliente porque tiene ventas o pedidos asociados.']);
        }

        $nombre = $cliente->nombre_completo;
        $cliente->delete();

        return redirect()->route('clientes.index')->with('info', "Cliente '{$nombre}' eliminado correctamente.");
    }
}
