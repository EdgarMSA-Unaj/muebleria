<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriasController extends Controller
{
    public function index(Request $request)
    {
        $query = Categoria::withCount('productos');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nombre', 'like', "%{$s}%")
                  ->orWhere('descripcion', 'like', "%{$s}%");
            });
        }

        $categorias = $query->orderBy('nombre')->paginate(12)->withQueryString();
        $totalCategorias = Categoria::count();
        $categoriasActivas = Categoria::where('activo', true)->count();

        return view('categorias.index', compact('categorias', 'totalCategorias', 'categoriasActivas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias,nombre',
            'descripcion' => 'nullable|string|max:255',
            'icono' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20'
        ]);

        $validated['icono'] = $validated['icono'] ?? 'fa-couch';
        $validated['color'] = $validated['color'] ?? '#ff6a3d';
        $validated['activo'] = true;

        Categoria::create($validated);

        return redirect()->route('categorias.index')->with('success', "Categoría '{$validated['nombre']}' creada exitosamente.");
    }

    public function update(Request $request, Categoria $categoria)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string|max:255',
            'icono' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'activo' => 'nullable|boolean'
        ]);

        $validated['activo'] = $request->boolean('activo', true);

        $categoria->update($validated);

        return redirect()->route('categorias.index')->with('success', "Categoría '{$categoria->nombre}' actualizada exitosamente.");
    }

    public function destroy(Categoria $categoria)
    {
        $mueblesCount = $categoria->productos()->count();
        if ($mueblesCount > 0) {
            return back()->withErrors(['error' => "No se puede eliminar '{$categoria->nombre}' porque contiene {$mueblesCount} mueble(s) asignado(s). Primero cambie de categoría dichos muebles en el inventario."]);
        }

        $nombre = $categoria->nombre;
        $categoria->delete();

        return redirect()->route('categorias.index')->with('info', "Categoría '{$nombre}' eliminada correctamente.");
    }
}
