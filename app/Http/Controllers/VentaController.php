<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\CajaMovimiento;
use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with(['cliente', 'usuario', 'detalles.producto'])->orderByDesc('fecha')->orderByDesc('id')->get();
        return view('ventas.index', [
            'ventas' => $ventas,
            'totalVentas' => $ventas->count(),
            'ingresosHoy' => (float)Venta::whereDate('fecha', Carbon::today())->sum('total'),
            'mesActual' => (float)Venta::whereYear('fecha', Carbon::now()->year)->whereMonth('fecha', Carbon::now()->month)->sum('total'),
        ]);
    }

    public function create(Request $request)
    {
        $caja = Caja::where('user_id', auth()->id())->where('estado', 'abierta')->first();
        if (!$caja) {
            return redirect()->route('cajas.create')->with('error', 'Debe abrir una caja antes de registrar ventas.');
        }
        $productos = Producto::conStock()->where('activo', true)->orderBy('nombre')->get()
            ->map(fn($p) => ['id' => $p->id, 'codigo' => $p->codigo, 'nombre' => $p->nombre, 'precio' => (float)$p->precio_venta, 'stock' => $p->stock]);
        return view('ventas.create', [
            'productos' => $productos,
            'clientes' => Cliente::orderBy('nombre')->get(),
            'clienteSel' => $request->filled('cliente') ? Cliente::find($request->input('cliente')) : null,
            'caja' => $caja,
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
        ], ['items.required' => 'Agregue al menos un producto a la venta.']);

        $caja = Caja::where('user_id', $request->user()->id)->where('estado', 'abierta')->first();
        if (!$caja) {
            return redirect()->route('cajas.create')->with('error', 'Debe abrir una caja antes de registrar ventas.');
        }

        // Agrupar productos repetidos
        $items = [];
        foreach ($datos['items'] as $it) {
            $items[$it['producto_id']] = ($items[$it['producto_id']] ?? 0) + (int)$it['cantidad'];
        }

        try {
            $venta = DB::transaction(function () use ($items, $datos, $request, $caja) {
                $total = 0;
                $lineas = [];
                foreach ($items as $productoId => $cantidad) {
                    $producto = Producto::where('id', $productoId)->where('activo', true)->first();
                    if (!$producto) {
                        throw new \RuntimeException('Uno de los productos ya no está disponible.');
                    }
                    // FEFO: primero se vende el lote que vence antes; los lotes sin fecha van al final
                    $lotes = Lote::where('producto_id', $productoId)->vigentes()
                        ->orderByRaw('fecha_vencimiento IS NULL, fecha_vencimiento')->orderBy('id')
                        ->lockForUpdate()->get();
                    $disponible = (int)$lotes->sum('cantidad_actual');
                    if ($disponible < $cantidad) {
                        throw new \RuntimeException("Stock insuficiente de {$producto->nombre} (disponible: {$disponible}).");
                    }
                    $resto = $cantidad;
                    foreach ($lotes as $lote) {
                        if ($resto <= 0) {
                            break;
                        }
                        $tomar = min($resto, (int)$lote->cantidad_actual);
                        $lineas[] = [$producto, $lote, $tomar];
                        $resto -= $tomar;
                    }
                    $total += $cantidad * (float)$producto->precio_venta;
                }

                $venta = Venta::create([
                    'cliente_id' => $datos['cliente_id'] ?? null,
                    'user_id' => $request->user()->id,
                    'caja_id' => $caja->id,
                    'fecha' => now(),
                    'total' => $total,
                ]);
                foreach ($lineas as [$producto, $lote, $tomar]) {
                    DetalleVenta::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $producto->id,
                        'lote_id' => $lote->id,
                        'cantidad' => $tomar,
                        'precio_unitario' => $producto->precio_venta,
                        'subtotal' => $tomar * (float)$producto->precio_venta,
                    ]);
                    $lote->decrement('cantidad_actual', $tomar);
                }
                CajaMovimiento::create([
                    'caja_id' => $caja->id,
                    'tipo' => 'ingreso',
                    'concepto' => 'Venta N° ' . $venta->id,
                    'monto' => $total,
                    'origen' => 'venta',
                    'referencia_id' => $venta->id,
                    'fecha' => now(),
                ]);
                return $venta;
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('ventas.show', $venta)->with('ok', 'Venta registrada correctamente.');
    }

    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'usuario', 'detalles.producto']);
        // Agrupar por producto (una venta puede salir de varios lotes)
        $lineas = $venta->detalles->groupBy('producto_id')->map(function ($g) {
            return (object)[
                'nombre' => $g->first()->producto->nombre ?? 'Producto',
                'codigo' => $g->first()->producto->codigo ?? '',
                'cantidad' => $g->sum('cantidad'),
                'precio' => $g->first()->precio_unitario,
                'subtotal' => $g->sum('subtotal'),
            ];
        })->values();
        return view('ventas.show', ['venta' => $venta, 'lineas' => $lineas]);
    }

    // Anular venta: devuelve el stock a sus lotes y quita el ingreso de caja
    public function destroy(Venta $venta)
    {
        $caja = $venta->caja;
        if ($caja && $caja->estado === 'cerrada') {
            return back()->with('error', 'No se puede anular: la caja de esta venta ya fue cerrada.');
        }
        DB::transaction(function () use ($venta) {
            foreach ($venta->detalles as $d) {
                Lote::where('id', $d->lote_id)->increment('cantidad_actual', $d->cantidad);
            }
            CajaMovimiento::where('origen', 'venta')->where('referencia_id', $venta->id)->delete();
            $venta->delete();
        });
        return redirect()->route('ventas.index')->with('ok', 'Venta anulada y stock devuelto.');
    }
}
