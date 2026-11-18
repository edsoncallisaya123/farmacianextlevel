<?php

namespace App\Http\Controllers;

use App\Models\Laboratorio;
use Illuminate\Http\Request;

class LaboratorioController extends Controller
{
    private function reglas(): array
    {
        return [
            'nombre' => 'required|string|max:150',
            'telefono' => 'nullable|string|max:30',
            'contacto' => 'nullable|string|max:150',
            'celular' => 'nullable|string|max:30',
            'direccion' => 'nullable|string|max:200',
        ];
    }

    public function index(Request $request)
    {
        return view('laboratorios.index', [
            'laboratorios' => Laboratorio::orderBy('nombre')->get(),
            'editar' => $request->filled('editar') ? Laboratorio::find($request->input('editar')) : null,
        ]);
    }

    public function store(Request $request)
    {
        Laboratorio::create($request->validate($this->reglas()));
        return redirect()->route('laboratorios.index')->with('ok', 'Proveedor registrado correctamente.');
    }

    public function update(Request $request, Laboratorio $laboratorio)
    {
        $laboratorio->update($request->validate($this->reglas()));
        return redirect()->route('laboratorios.index')->with('ok', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Laboratorio $laboratorio)
    {
        try {
            $laboratorio->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'No se puede eliminar: el proveedor tiene compras registradas.');
        }
        return back()->with('ok', 'Proveedor eliminado.');
    }
}
