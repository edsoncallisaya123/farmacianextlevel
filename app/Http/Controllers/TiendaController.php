<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class TiendaController extends Controller
{
    public function index(Request $request)
    {
        $consulta = Producto::conStock()->with('categoria')->where('activo', true)->orderBy('nombre');
        if ($request->filled('q')) {
            $q = '%' . $request->input('q') . '%';
            $consulta->where(function ($w) use ($q) {
                $w->where('nombre', 'like', $q)->orWhere('descripcion', 'like', $q);
            });
        }
        $categoriaSel = null;
        if ($request->filled('categoria')) {
            $categoriaSel = Categoria::find($request->input('categoria'));
            $consulta->where('categoria_id', $request->input('categoria'));
        }
        return view('tienda.index', [
            'productos' => $consulta->get(),
            'categorias' => Categoria::withCount(['productos' => function ($q) {
                $q->where('activo', true);
            }])->orderBy('nombre')->get(),
            'categoriaSel' => $categoriaSel,
            'busqueda' => $request->input('q', ''),
        ]);
    }

    public function show(Producto $producto)
    {
        abort_unless($producto->activo, 404);
        $producto = Producto::conStock()->with(['categoria', 'laboratorio'])->findOrFail($producto->id);
        return view('tienda.show', [
            'producto' => $producto,
            'categoriaSel' => null,
            'categorias' => Categoria::withCount(['productos' => function ($q) {
                $q->where('activo', true);
            }])->orderBy('nombre')->get(),
        ]);
    }

    public function sugerencias(Request $request)
    {
        $q = trim((string)$request->input('q'));
        if ($q === '') {
            return response()->json([]);
        }
        $items = Producto::where('activo', true)->where('nombre', 'like', '%' . $q . '%')
            ->orderBy('nombre')->limit(6)->get()
            ->map(fn($p) => ['id' => $p->id, 'nombre' => $p->nombre, 'imagen' => $p->imagen_url, 'url' => route('tienda.producto', $p->id)]);
        return response()->json($items);
    }
}
