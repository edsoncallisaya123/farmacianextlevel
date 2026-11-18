<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LoteController extends Controller
{
    public function index(Request $request)
    {
        $consulta = Lote::with('producto')->orderByRaw('fecha_vencimiento IS NULL, fecha_vencimiento')->orderBy('id');
        if ($request->filled('producto')) {
            $consulta->where('producto_id', $request->input('producto'));
        }
        $hoy = Carbon::today()->toDateString();
        $limite = Carbon::today()->addDays(30)->toDateString();
        switch ($request->input('estado')) {
            case 'activo':
                $consulta->where('cantidad_actual', '>', 0)->where(function ($w) use ($limite) {
                    $w->whereNull('fecha_vencimiento')->orWhere('fecha_vencimiento', '>', $limite);
                });
                break;
            case 'por_vencer':
                $consulta->where('cantidad_actual', '>', 0)->whereBetween('fecha_vencimiento', [$hoy, $limite]);
                break;
            case 'vencido':
                $consulta->where('cantidad_actual', '>', 0)->where('fecha_vencimiento', '<', $hoy);
                break;
            case 'agotado':
                $consulta->where('cantidad_actual', '<=', 0);
                break;
        }
        return view('lotes.index', [
            'lotes' => $consulta->get(),
            'productos' => Producto::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'numero_lote' => 'required|string|max:50',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:today',
            'cantidad' => 'required|integer|min:1',
            'costo_unitario' => 'nullable|numeric|min:0',
        ]);
        Lote::create([
            'producto_id' => $datos['producto_id'],
            'numero_lote' => $datos['numero_lote'],
            'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? null,
            'cantidad_inicial' => $datos['cantidad'],
            'cantidad_actual' => $datos['cantidad'],
            'costo_unitario' => $datos['costo_unitario'] ?? 0,
        ]);
        return redirect()->route('lotes.index')->with('ok', 'Lote registrado correctamente.');
    }

    public function baja(Lote $lote)
    {
        $lote->update(['cantidad_actual' => 0]);
        return back()->with('ok', 'El lote fue dado de baja (stock en 0).');
    }
}
