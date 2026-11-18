<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CajaMovimiento;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Laboratorio;
use App\Models\Lote;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index()
    {
        $compras = Compra::with(['laboratorio', 'usuario', 'detalles.producto', 'detalles.lote'])->orderByDesc('fecha')->orderByDesc('id')->get();
        return view('compras.index', ['compras' => $compras]);
    }

    public function create()
    {
        return view('compras.create', [
            'laboratorios' => Laboratorio::orderBy('nombre')->get(),
            'productos' => Producto::where('activo', true)->orderBy('nombre')->get(['id', 'codigo', 'nombre']),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'laboratorio_id' => 'required|exists:laboratorios,id',
            'fecha' => 'required|date',
            'comprobante' => 'required|in:FACTURA,RECIBO,NOTA DE REMISION',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.costo' => 'required|numeric|min:0',
            'items.*.lote' => 'required|string|max:50',
            'items.*.vencimiento' => 'nullable|date|after_or_equal:today',
        ], [
            'items.required' => 'Agregue al menos un producto a la compra.',
            'items.*.vencimiento.after_or_equal' => 'No se puede registrar un lote con fecha de vencimiento pasada.',
        ]);

        $usuario = $request->user();
        $compra = DB::transaction(function () use ($datos, $usuario) {
            $caja = Caja::where('user_id', $usuario->id)->where('estado', 'abierta')->first();
            $compra = Compra::create([
                'laboratorio_id' => $datos['laboratorio_id'],
                'user_id' => $usuario->id,
                'caja_id' => $caja?->id,
                'fecha' => $datos['fecha'],
                'comprobante' => $datos['comprobante'],
                'total' => 0,
            ]);
            $total = 0;
            foreach ($datos['items'] as $it) {
                $lote = Lote::create([
                    'producto_id' => $it['producto_id'],
                    'numero_lote' => $it['lote'],
                    'fecha_vencimiento' => $it['vencimiento'] ?: null,
                    'cantidad_inicial' => $it['cantidad'],
                    'cantidad_actual' => $it['cantidad'],
                    'costo_unitario' => $it['costo'],
                    'compra_id' => $compra->id,
                ]);
                $subtotal = $it['cantidad'] * $it['costo'];
                $total += $subtotal;
                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $it['producto_id'],
                    'lote_id' => $lote->id,
                    'cantidad' => $it['cantidad'],
                    'costo_unitario' => $it['costo'],
                    'subtotal' => $subtotal,
                ]);
            }
            $compra->update(['total' => $total]);
            if ($caja && $total > 0) {
                CajaMovimiento::create([
                    'caja_id' => $caja->id,
                    'tipo' => 'egreso',
                    'concepto' => 'Compra de productos',
                    'monto' => $total,
                    'origen' => 'compra',
                    'referencia_id' => $compra->id,
                    'fecha' => now(),
                ]);
            }
            return $compra;
        });

        return redirect()->route('compras.show', $compra)->with('ok', 'Compra registrada. El stock fue actualizado.');
    }

    public function show(Compra $compra)
    {
        $compra->load(['laboratorio', 'usuario', 'detalles.producto', 'detalles.lote']);
        return view('compras.show', ['compra' => $compra]);
    }

    public function destroy(Compra $compra)
    {
        $compra->load('detalles.lote');
        foreach ($compra->detalles as $d) {
            if (!$d->lote || $d->lote->cantidad_actual != $d->lote->cantidad_inicial) {
                return back()->with('error', 'No se puede eliminar: parte de esta compra ya fue vendida o dada de baja.');
            }
        }
        DB::transaction(function () use ($compra) {
            $loteIds = $compra->detalles->pluck('lote_id')->all();
            $compra->detalles()->delete();
            Lote::whereIn('id', $loteIds)->delete();
            CajaMovimiento::where('origen', 'compra')->where('referencia_id', $compra->id)->delete();
            $compra->delete();
        });
        return redirect()->route('compras.index')->with('ok', 'Compra eliminada y stock revertido.');
    }
}
