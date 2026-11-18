<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Laboratorio;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $consulta = Producto::conStock()->with(['categoria', 'laboratorio'])->orderBy('nombre');
        if ($request->filled('q')) {
            $q = '%' . $request->input('q') . '%';
            $consulta->where(function ($w) use ($q) {
                $w->where('nombre', 'like', $q)->orWhere('codigo', 'like', $q)->orWhere('descripcion', 'like', $q);
            });
        }
        if ($request->filled('categoria')) {
            $consulta->where('categoria_id', $request->input('categoria'));
        }
        return view('productos.index', [
            'productos' => $consulta->get(),
            'categorias' => Categoria::orderBy('nombre')->get(),
            'totalCategorias' => Categoria::count(),
        ]);
    }

    public function create()
    {
        return view('productos.form', [
            'producto' => new Producto(['stock_minimo' => 0, 'stock_maximo' => 0, 'precio_venta' => 0, 'activo' => true]),
            'categorias' => Categoria::orderBy('nombre')->get(),
            'laboratorios' => Laboratorio::orderBy('nombre')->get(),
        ]);
    }

    private function reglas(?Producto $producto = null): array
    {
        return [
            'codigo' => ['required', 'string', 'max:50', Rule::unique('productos', 'codigo')->ignore($producto?->id)],
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:1000',
            'categoria_id' => 'required|exists:categorias,id',
            'laboratorio_id' => 'required|exists:laboratorios,id',
            'precio_venta' => 'required|numeric|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'stock_maximo' => 'required|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function store(Request $request)
    {
        $datos = $request->validate($this->reglas());
        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        }
        $datos['activo'] = true;
        Producto::create($datos);
        return redirect()->route('productos.index')->with('ok', 'Producto registrado correctamente.');
    }

    public function edit(Producto $producto)
    {
        return view('productos.form', [
            'producto' => $producto,
            'categorias' => Categoria::orderBy('nombre')->get(),
            'laboratorios' => Laboratorio::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Producto $producto)
    {
        $datos = $request->validate($this->reglas($producto));
        if ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        } else {
            unset($datos['imagen']);
        }
        $datos['activo'] = $request->boolean('activo');
        $producto->update($datos);
        return redirect()->route('productos.index')->with('ok', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        try {
            $imagen = $producto->imagen;
            $producto->delete();
            if ($imagen) {
                Storage::disk('public')->delete($imagen);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $producto->update(['activo' => false]);
            return back()->with('error', 'El producto tiene lotes o ventas registradas, por eso fue desactivado en lugar de eliminado.');
        }
        return back()->with('ok', 'Producto eliminado.');
    }

    // Búsqueda por código (usada en ventas y compras)
    public function buscarCodigo(Request $request)
    {
        $codigo = trim((string)$request->input('codigo'));
        $p = Producto::conStock()->where('activo', true)->where('codigo', $codigo)->first();
        if (!$p) {
            return response()->json(['encontrado' => false], 404);
        }
        return response()->json([
            'encontrado' => true,
            'id' => $p->id,
            'codigo' => $p->codigo,
            'nombre' => $p->nombre,
            'precio' => (float)$p->precio_venta,
            'stock' => $p->stock,
        ]);
    }
}
