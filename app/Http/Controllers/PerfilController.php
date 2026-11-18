<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PerfilController extends Controller
{
    public function edit()
    {
        return view('perfil.edit', ['usuario' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $datos = $request->validate([
            'usuario' => ['nullable', 'string', 'max:50', Rule::unique('users', 'usuario')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'name' => 'required|string|max:100',
            'apellido_paterno' => 'nullable|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:30',
            'direccion' => 'nullable|string|max:200',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $datos['foto'] = $request->file('foto')->store('usuarios', 'public');
        } else {
            unset($datos['foto']);
        }

        $user->update($datos);
        return redirect()->route('perfil')->with('ok', 'Perfil actualizado correctamente.');
    }

    public function password()
    {
        return view('perfil.password');
    }

    public function updatePassword(Request $request)
    {
        $datos = $request->validate([
            'password_actual' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();
        if (!Hash::check($datos['password_actual'], $user->password)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual no es correcta.']);
        }
        $user->update(['password' => Hash::make($datos['password'])]);
        return redirect()->route('perfil.password')->with('ok', 'Contraseña actualizada correctamente.');
    }
}
