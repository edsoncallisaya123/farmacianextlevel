<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RolController extends Controller
{
    public function index()
    {
        return view('roles.index', ['roles' => Rol::with('permisos')->orderBy('id')->get()]);
    }

    public function create()
    {
        return view('roles.form', ['rol' => new Rol(), 'permisos' => Permiso::orderBy('nombre')->get(), 'asignados' => []]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:50|unique:roles,nombre',
            'descripcion' => 'nullable|string|max:255',
            'permisos' => 'nullable|array',
            'permisos.*' => 'exists:permisos,id',
        ]);
        $rol = Rol::create(['nombre' => $datos['nombre'], 'descripcion' => $datos['descripcion'] ?? null]);
        $rol->permisos()->sync($datos['permisos'] ?? []);
        return redirect()->route('roles.index')->with('ok', 'Rol creado correctamente.');
    }

    public function edit(Rol $rol)
    {
        return view('roles.form', [
            'rol' => $rol,
            'permisos' => Permiso::orderBy('nombre')->get(),
            'asignados' => $rol->permisos()->pluck('permisos.id')->all(),
        ]);
    }

    public function update(Request $request, Rol $rol)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:50', Rule::unique('roles', 'nombre')->ignore($rol->id)],
            'descripcion' => 'nullable|string|max:255',
            'permisos' => 'nullable|array',
            'permisos.*' => 'exists:permisos,id',
        ]);
        $rol->update(['nombre' => $datos['nombre'], 'descripcion' => $datos['descripcion'] ?? null]);
        $rol->permisos()->sync($datos['permisos'] ?? []);
        return redirect()->route('roles.index')->with('ok', 'Rol actualizado correctamente.');
    }

    public function destroy(Rol $rol)
    {
        if ($rol->nombre === 'admin') {
            return back()->with('error', 'El rol admin no se puede eliminar.');
        }
        if ($rol->users()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: hay usuarios con este rol.');
        }
        $rol->delete();
        return back()->with('ok', 'Rol eliminado.');
    }
}
