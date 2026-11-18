<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    private function reglas(): array
    {
        return [
            'nombre' => 'required|string|max:150',
            'nit_ci' => 'nullable|string|max:30',
            'celular' => 'nullable|string|max:30',
            'correo' => 'nullable|email|max:100',
        ];
    }

    public function index(Request $request)
    {
        return view('clientes.index', [
            'clientes' => Cliente::orderBy('nombre')->get(),
            'editar' => $request->filled('editar') ? Cliente::find($request->input('editar')) : null,
        ]);
    }

    public function store(Request $request)
    {
        $cliente = Cliente::create($request->validate($this->reglas()));
        if ($request->input('volver') === 'venta') {
            return redirect()->route('ventas.create', ['cliente' => $cliente->id])->with('ok', 'Cliente registrado.');
        }
        return redirect()->route('clientes.index')->with('ok', 'Cliente registrado correctamente.');
    }

    public function update(Request $request, Cliente $cliente)
    {
        $cliente->update($request->validate($this->reglas()));
        return redirect()->route('clientes.index')->with('ok', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete(); // las ventas quedan como "cliente general"
        return back()->with('ok', 'Cliente eliminado.');
    }
}
