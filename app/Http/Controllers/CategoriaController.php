<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        return view('categorias.index', [
            'categorias' => Categoria::withCount('productos')->orderBy('nombre')->get(),
            'editar' => $request->filled('editar') ? Categoria::find($request->input('editar')) : null,
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias,nombre',
            'descripcion' => 'nullable|string|max:255',
        ]);
        Categoria::create($datos);
        return redirect()->route('categorias.index')->with('ok', 'Categoría registrada correctamente.');
    }

    public function update(Request $request, Categoria $categoria)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100', Rule::unique('categorias', 'nombre')->ignore($categoria->id)],
            'descripcion' => 'nullable|string|max:255',
        ]);
        $categoria->update($datos);
        return redirect()->route('categorias.index')->with('ok', 'Categoría actualizada correctamente.');
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete(); // los productos quedan "sin categoría"
        return back()->with('ok', 'Categoría eliminada.');
    }
}
