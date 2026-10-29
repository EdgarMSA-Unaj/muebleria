<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Almacen;
use App\Models\MovimientoStock;
use Illuminate\Support\Facades\Auth;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with('categoria');

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nombre', 'like', "%{$s}%")
                  ->orWhere('sku', 'like', "%{$s}%")
                  ->orWhere('material', 'like', "%{$s}%");
            });
        }

        if ($request->get('filtro') === 'stock_bajo') {
            $query->whereColumn('stock_actual', '<=', 'stock_minimo');
        }

        $productos = $query->latest()->paginate(10)->withQueryString();
        $categorias = Categoria::where('activo', true)->get();
        $almacenes = Almacen::where('activo', true)->get();

        $totalProductos = Producto::count();
        $totalStock = Producto::sum('stock_actual');
        $valorInventario = Producto::selectRaw('SUM(stock_actual * precio_compra) as total')->value('total') ?? 0;
        $productosBajoStock = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')->count();

        return view('inventario.index', compact(
            'productos',
            'categorias',
            'almacenes',
            'totalProductos',
            'totalStock',
            'valorInventario',
            'productosBajoStock'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|unique:productos,sku',
            'nombre' => 'required|max:200',
            'categoria_id' => 'required|exists:categorias,id',
            'material' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:100',
            'largo' => 'nullable|numeric',
            'ancho' => 'nullable|numeric',
            'alto' => 'nullable|numeric',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:1',
            'descripcion' => 'nullable|string'
        ]);

        $validated['unidad_medida'] = $request->input('unidad_medida', 'UND');
        $validated['activo'] = true;

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('productos', 'public');
            $validated['imagen'] = $path;
        }

        $producto = Producto::create($validated);

        // Registrar movimiento inicial de stock
        if ($producto->stock_actual > 0) {
            $almacen = Almacen::where('principal', true)->first() ?? Almacen::first();
            if ($almacen) {
                MovimientoStock::create([
                    'producto_id' => $producto->id,
                    'almacen_id' => $almacen->id,
                    'user_id' => Auth::id() ?? 1,
                    'tipo_movimiento' => 'ajuste_positivo',
                    'cantidad' => $producto->stock_actual,
                    'stock_anterior' => 0,
                    'stock_nuevo' => $producto->stock_actual,
                    'motivo' => 'Inventario inicial al crear mueble'
                ]);
            }
        }

        return redirect()->route('inventario.index')->with('success', 'Mueble registrado exitosamente.');
    }

    public function ajustarStock(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:agregar,reducir',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255'
        ]);

        $stockAnterior = $producto->stock_actual;
        $cantidad = $validated['cantidad'];

        if ($validated['tipo'] === 'agregar') {
            $stockNuevo = $stockAnterior + $cantidad;
            $tipoMovimiento = 'ajuste_positivo';
        } else {
            if ($stockAnterior < $cantidad) {
                return back()->withErrors(['cantidad' => 'No se puede reducir más stock del disponible actualmente.']);
            }
            $stockNuevo = $stockAnterior - $cantidad;
            $tipoMovimiento = 'ajuste_negativo';
        }

        $producto->update(['stock_actual' => $stockNuevo]);

        $almacen = Almacen::where('principal', true)->first() ?? Almacen::first();
        MovimientoStock::create([
            'producto_id' => $producto->id,
            'almacen_id' => $almacen ? $almacen->id : 1,
            'user_id' => Auth::id() ?? 1,
            'tipo_movimiento' => $tipoMovimiento,
            'cantidad' => $cantidad,
            'stock_anterior' => $stockAnterior,
            'stock_nuevo' => $stockNuevo,
            'motivo' => $validated['motivo']
        ]);

        return redirect()->route('inventario.index')->with('success', 'Stock actualizado correctamente.');
    }

    public function kardex(Request $request)
    {
        $query = MovimientoStock::with(['producto.categoria', 'almacen', 'usuario']);

        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        if ($request->filled('tipo_movimiento')) {
            $query->where('tipo_movimiento', $request->tipo_movimiento);
        }

        $movimientos = $query->latest()->paginate(15)->withQueryString();
        $productos = Producto::orderBy('nombre')->get();

        return view('inventario.kardex', compact('movimientos', 'productos'));
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'sku' => 'required|unique:productos,sku,' . $producto->id,
            'nombre' => 'required|max:200',
            'categoria_id' => 'required|exists:categorias,id',
            'material' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:100',
            'largo' => 'nullable|numeric',
            'ancho' => 'nullable|numeric',
            'alto' => 'nullable|numeric',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock_minimo' => 'required|integer|min:1',
            'descripcion' => 'nullable|string'
        ]);

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('productos', 'public');
            $validated['imagen'] = $path;
        }

        $producto->update($validated);

        return redirect()->route('inventario.index')->with('success', "Mueble '{$producto->nombre}' actualizado exitosamente.");
    }

    public function destroy(Producto $producto)
    {
        $nombre = $producto->nombre;
        $producto->delete();
        return redirect()->route('inventario.index')->with('info', "Mueble '{$nombre}' enviado a papelera.");
    }

    public function exportarCsv()
    {
        $productos = Producto::with('categoria')->get();

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=inventario_muebleria_" . date('Y-m-d') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($productos) {
            $file = fopen('php://output', 'w');
            // BOM UTF-8 para visualización en Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'SKU', 'Nombre del Mueble', 'Categoría', 'Material', 'Color',
                'Largo (cm)', 'Ancho (cm)', 'Alto (cm)', 'Stock Actual',
                'Stock Mínimo', 'Precio Compra (S/.)', 'Precio Venta (S/.)',
                'Valorización Total (S/.)', 'Estado Stock'
            ]);

            foreach ($productos as $p) {
                fputcsv($file, [
                    $p->sku,
                    $p->nombre,
                    $p->categoria->nombre ?? 'General',
                    $p->material,
                    $p->color,
                    $p->largo,
                    $p->ancho,
                    $p->alto,
                    $p->stock_actual,
                    $p->stock_minimo,
                    number_format($p->precio_compra, 2, '.', ''),
                    number_format($p->precio_venta, 2, '.', ''),
                    number_format($p->stock_actual * $p->precio_compra, 2, '.', ''),
                    $p->stock_status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
