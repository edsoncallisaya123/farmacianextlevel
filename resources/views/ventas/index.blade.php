@extends('layouts.app')
@section('titulo', 'Panel de ventas')
@section('migas', 'Ventas')

@section('contenido')
<div class="nl-card">
    <div class="nl-titulo"><h1>Panel de Ventas</h1><a href="{{ route('ventas.create') }}" class="btn btn-verde"><i class="bi bi-plus-circle-fill"></i> NUEVA VENTA</a></div>
</div>
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="stat-card"><div class="d-flex gap-3 align-items-center"><div class="kpi-ico" style="background:var(--nl-navy)"><i class="bi bi-receipt"></i></div><div><div class="etq">Total ventas</div><div class="valor">{{ $totalVentas }}</div></div></div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="d-flex gap-3 align-items-center"><div class="kpi-ico" style="background:var(--nl-teal)"><i class="bi bi-cash-stack"></i></div><div><div class="etq">Ingresos hoy</div><div class="valor">Bs {{ number_format($ingresosHoy, 2) }}</div></div></div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="d-flex gap-3 align-items-center"><div class="kpi-ico" style="background:#1d7be8"><i class="bi bi-calendar3"></i></div><div><div class="etq">Mes actual</div><div class="valor">Bs {{ number_format($mesActual, 2) }}</div></div></div></div></div>
</div>
<div class="nl-card">
    <div class="nl-titulo mb-2"><div class="nl-sub mb-0"><i class="bi bi-list-task"></i> Registro de Ventas</div>@include('partials.exportar', ['tabla' => '#tablaVentas', 'nombre' => 'ventas'])</div>
    <div class="table-responsive">
        <table class="table nl-table" id="tablaVentas" data-tabla>
            <thead><tr><th>#</th><th>Detalles</th><th>Cliente</th><th>Fecha</th><th>Total</th><th>Vendedor</th><th class="text-center no-exportar">Acciones</th></tr></thead>
            <tbody>
            @forelse($ventas as $v)
                <tr>
                    <td>{{ $v->id }}</td>
                    <td>
                        <details><summary class="btn btn-sm btn-nl-outline d-inline-block">Productos ({{ $v->detalles->groupBy('producto_id')->count() }})</summary>
                            <ul class="small mt-2 mb-0 ps-3">
                                @foreach($v->detalles->groupBy('producto_id') as $g)
                                    <li>{{ $g->sum('cantidad') }} x {{ $g->first()->producto->nombre ?? '' }}</li>
                                @endforeach
                            </ul>
                        </details>
                    </td>
                    <td>{{ $v->cliente->nombre ?? 'Cliente general' }}</td>
                    <td>{{ $v->fecha->format('d/m/Y H:i') }}</td>
                    <td class="verde-txt">Bs {{ number_format($v->total, 2) }}</td>
                    <td>{{ $v->usuario->name ?? '' }}</td>
                    <td class="text-center text-nowrap">
                        <a href="{{ route('ventas.show', $v) }}" class="btn-ico ver" title="Ver recibo"><i class="bi bi-eye-fill"></i></a>
                        <form method="post" action="{{ route('ventas.destroy', $v) }}" class="d-inline" data-confirmar="¿Anular esta venta? El stock será devuelto a sus lotes.">
                            @csrf @method('DELETE')
                            <button class="btn-ico eliminar" title="Anular"><i class="bi bi-trash-fill"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr class="fila-vacia"><td colspan="7" class="text-center text-muted">Aún no hay ventas registradas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
