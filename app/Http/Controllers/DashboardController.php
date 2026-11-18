<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Compra;
use App\Models\DetalleVenta;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totales = [
            'productos' => Producto::count(),
            'compras' => Compra::count(),
            'ventas' => Venta::count(),
            'clientes' => Cliente::count(),
        ];

        $alertas = Producto::conStock()->where('activo', true)->get()
            ->filter(fn($p) => $p->stock < $p->stock_minimo)
            ->sortBy('stock')
            ->take(6);

        $masVendidos = DetalleVenta::select('producto_id', DB::raw('SUM(cantidad) as total'))
            ->groupBy('producto_id')
            ->orderByDesc('total')
            ->with('producto')
            ->limit(5)
            ->get();

        $porVencer = Lote::with('producto')
            ->where('cantidad_actual', '>', 0)
            ->whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<=', Carbon::today()->addDays(30)->toDateString())
            ->orderBy('fecha_vencimiento')
            ->limit(6)
            ->get();

        return view('dashboard', [
            'totales' => $totales,
            'alertas' => $alertas,
            'porVencer' => $porVencer,
            'ventasHoy' => Venta::whereDate('fecha', Carbon::today())->count(),
            'ingresosHoy' => (float)Venta::whereDate('fecha', Carbon::today())->sum('total'),
            'etiquetas' => $masVendidos->map(fn($d) => $d->producto->nombre ?? 'Producto')->values(),
            'valores' => $masVendidos->map(fn($d) => (int)$d->total)->values(),
        ]);
    }
}
