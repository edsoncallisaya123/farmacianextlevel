<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CajaMovimiento;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    public function index()
    {
        $consulta = Caja::with('usuario')
            ->withSum(['movimientos as ingresos' => function ($q) {
                $q->where('tipo', 'ingreso');
            }], 'monto')
            ->withSum(['movimientos as egresos' => function ($q) {
                $q->where('tipo', 'egreso');
            }], 'monto')
            ->orderByDesc('id');
        if (!auth()->user()->esAdmin()) {
            $consulta->where('user_id', auth()->id());
        }
        return view('cajas.index', ['cajas' => $consulta->get()]);
    }

    public function create()
    {
        return view('cajas.create');
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'fecha_apertura' => 'required|date',
            'monto_inicial' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:255',
        ]);
        if (Caja::where('user_id', $request->user()->id)->where('estado', 'abierta')->exists()) {
            return redirect()->route('cajas.index')->with('error', 'Ya tiene una caja abierta. Ciérrela antes de abrir otra.');
        }
        Caja::create($datos + ['user_id' => $request->user()->id, 'estado' => 'abierta']);
        return redirect()->route('cajas.index')->with('ok', 'Caja abierta correctamente.');
    }

    public function show(Caja $caja)
    {
        $caja->load(['usuario', 'movimientos']);
        return view('cajas.show', ['caja' => $caja]);
    }

    public function movimiento(Request $request, Caja $caja)
    {
        $datos = $request->validate([
            'tipo' => 'required|in:ingreso,egreso',
            'concepto' => 'required|string|max:200',
            'monto' => 'required|numeric|min:0.01',
        ]);
        if ($caja->estado !== 'abierta') {
            return back()->with('error', 'La caja está cerrada.');
        }
        CajaMovimiento::create($datos + ['caja_id' => $caja->id, 'origen' => 'manual', 'fecha' => now()]);
        return redirect()->route('cajas.show', $caja)->with('ok', 'Movimiento registrado.');
    }

    public function cerrar(Request $request, Caja $caja)
    {
        $datos = $request->validate(['monto_final' => 'required|numeric|min:0']);
        if ($caja->estado !== 'abierta') {
            return back()->with('error', 'La caja ya está cerrada.');
        }
        $caja->update(['monto_final' => $datos['monto_final'], 'fecha_cierre' => now(), 'estado' => 'cerrada']);
        $dif = (float)$datos['monto_final'] - $caja->monto_esperado;
        $msg = 'Caja cerrada. Diferencia del arqueo: Bs ' . number_format($dif, 2) . '.';
        return redirect()->route('cajas.show', $caja)->with(abs($dif) < 0.005 ? 'ok' : 'error', $msg);
    }

    public function destroy(Caja $caja)
    {
        if ($caja->movimientos()->exists() || $caja->ventas()->exists()) {
            return back()->with('error', 'No se puede eliminar una caja con movimientos o ventas.');
        }
        $caja->delete();
        return back()->with('ok', 'Caja eliminada.');
    }
}
