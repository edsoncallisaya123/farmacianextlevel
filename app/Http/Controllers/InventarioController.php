<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Venta;
use Carbon\Carbon;

class InventarioController extends Controller
{
    public function index()
    {
        $productos = Producto::conStock()->where('activo', true)->orderBy('nombre')->get();
        $bajoStock = $productos->filter(fn($p) => $p->stock < $p->stock_minimo)->values();

        $hoy = Carbon::today();
        $porVencer = Lote::with('producto')->where('cantidad_actual', '>', 0)
            ->whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<=', $hoy->copy()->addDays(30)->toDateString())
            ->orderBy('fecha_vencimiento')->get();

        $mes = Carbon::now();
        $compras = Compra::whereYear('fecha', $mes->year)->whereMonth('fecha', $mes->month);
        $ventas = Venta::whereYear('fecha', $mes->year)->whereMonth('fecha', $mes->month);

        return view('inventario.index', [
            'totalProductos' => $productos->count(),
            'bajoStock' => $bajoStock,
            'porVencer' => $porVencer,
            'comprasMes' => (clone $compras)->count(),
            'totalComprado' => (float)(clone $compras)->sum('total'),
            'ventasMes' => (clone $ventas)->count(),
            'totalVendido' => (float)(clone $ventas)->sum('total'),
            'mes' => $mes,
        ]);
    }
}
