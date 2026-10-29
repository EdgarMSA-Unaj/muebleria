<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuracion;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $config = Configuracion::first() ?? new Configuracion();
        return view('configuracion.index', compact('config'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nombre_empresa' => 'required|string|max:200',
            'ruc' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:50',
            'email' => 'required|email',
            'sitio_web' => 'nullable|string|max:255',
            'moneda_simbolo' => 'required|string|max:10',
            'porcentaje_igv' => 'required|numeric',
            'serie_boleta' => 'required|string|max:10',
            'serie_factura' => 'required|string|max:10',
            'serie_nota_venta' => 'required|string|max:10',
            'serie_cotizacion' => 'nullable|string|max:10',
            'terminos_garantia' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048'
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('empresa', 'public');
            $validated['logo'] = $path;
        }

        $config = Configuracion::first();
        if ($config) {
            $config->update($validated);
        } else {
            Configuracion::create($validated);
        }

        return redirect()->route('configuracion.index')->with('success', 'Configuración de la empresa y logotipo guardados exitosamente.');
    }
}
