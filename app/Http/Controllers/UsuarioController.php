<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index()
    {
        return view('usuarios.index', ['usuarios' => User::with('rol')->orderBy('name')->get()]);
    }

    public function create()
    {
        return view('usuarios.form', ['usuario' => new User(), 'roles' => Rol::orderBy('nombre')->get()]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'usuario' => 'required|string|max:50|unique:users,usuario',
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:100',
            'apellido_paterno' => 'nullable|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:30',
            'direccion' => 'nullable|string|max:200',
            'rol_id' => 'required|exists:roles,id',
            'password' => 'required|string|min:6',
        ]);
        $datos['password'] = Hash::make($datos['password']);
        $datos['activo'] = true;
        User::create($datos);
        return redirect()->route('usuarios.index')->with('ok', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        return view('usuarios.form', ['usuario' => $usuario, 'roles' => Rol::orderBy('nombre')->get()]);
    }

    public function update(Request $request, User $usuario)
    {
        $datos = $request->validate([
            'usuario' => ['required', 'string', 'max:50', Rule::unique('users', 'usuario')->ignore($usuario->id)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($usuario->id)],
            'name' => 'required|string|max:100',
            'apellido_paterno' => 'nullable|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:30',
            'direccion' => 'nullable|string|max:200',
            'rol_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:6',
        ]);
        if (empty($datos['password'])) {
            unset($datos['password']);
        } else {
            $datos['password'] = Hash::make($datos['password']);
        }
        if ($usuario->id !== auth()->id()) {
            $datos['activo'] = $request->boolean('activo');
        }
        $usuario->update($datos);
        return redirect()->route('usuarios.index')->with('ok', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puede eliminar su propio usuario.');
        }
        try {
            $usuario->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            $usuario->update(['activo' => false]);
            return back()->with('error', 'El usuario tiene operaciones registradas, por eso fue desactivado en lugar de eliminado.');
        }
        return back()->with('ok', 'Usuario eliminado.');
    }
}
