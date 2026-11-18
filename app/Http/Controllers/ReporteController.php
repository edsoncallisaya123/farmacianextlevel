<?php

namespace App\Http\Controllers;

use App\Models\CajaMovimiento;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    private function rango(Request $request): array
    {
        $desde = $request->input('desde', Carbon::now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', Carbon::today()->toDateString());
        return [$desde, $hasta];
    }

    public function ingresos(Request $request)
    {
        [$desde, $hasta] = $this->rango($request);
        $ventas = Venta::with(['cliente', 'usuario', 'detalles.producto'])
            ->whereDate('fecha', '>=', $desde)->whereDate('fecha', '<=', $hasta)
            ->orderBy('fecha')->get();
        return view('reportes.ingresos', ['ventas' => $ventas, 'desde' => $desde, 'hasta' => $hasta, 'total' => (float)$ventas->sum('total')]);
    }

    public function egresos(Request $request)
    {
        [$desde, $hasta] = $this->rango($request);
        $compras = Compra::with(['laboratorio', 'usuario'])
            ->whereDate('fecha', '>=', $desde)->whereDate('fecha', '<=', $hasta)
            ->orderBy('fecha')->get();
        $otros = CajaMovimiento::where('tipo', 'egreso')->where('origen', 'manual')
            ->whereDate('fecha', '>=', $desde)->whereDate('fecha', '<=', $hasta)->orderBy('fecha')->get();
        return view('reportes.egresos', [
            'compras' => $compras,
            'otros' => $otros,
            'desde' => $desde,
            'hasta' => $hasta,
            'total' => (float)$compras->sum('total') + (float)$otros->sum('monto'),
        ]);
    }

    public function inventario()
    {
        $productos = Producto::conStock()->with(['categoria', 'laboratorio'])->where('activo', true)->orderBy('nombre')->get();
        $valor = $productos->sum(fn($p) => $p->stock * (float)$p->precio_venta);
        return view('reportes.inventario', ['productos' => $productos, 'valor' => $valor]);
    }
}
