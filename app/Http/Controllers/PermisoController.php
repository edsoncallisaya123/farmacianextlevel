<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PermisoController extends Controller
{
    public function index(Request $request)
    {
        return view('permisos.index', [
            'permisos' => Permiso::withCount('roles')->orderBy('nombre')->get(),
            'editar' => $request->filled('editar') ? Permiso::find($request->input('editar')) : null,
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate(['nombre' => 'required|string|max:100']);
        $clave = Str::slug($datos['nombre'], '_');
        if (Permiso::where('clave', $clave)->exists()) {
            return back()->with('error', 'Ya existe un permiso con ese nombre.')->withInput();
        }
        Permiso::create(['nombre' => $datos['nombre'], 'clave' => $clave]);
        return redirect()->route('permisos.index')->with('ok', 'Permiso creado correctamente.');
    }

    public function update(Request $request, Permiso $permiso)
    {
        $datos = $request->validate(['nombre' => 'required|string|max:100']);
        $permiso->update(['nombre' => $datos['nombre']]);
        return redirect()->route('permisos.index')->with('ok', 'Permiso actualizado.');
    }

    public function destroy(Permiso $permiso)
    {
        $permiso->delete();
        return back()->with('ok', 'Permiso eliminado.');
    }
}
