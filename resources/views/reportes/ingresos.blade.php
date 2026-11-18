@extends('layouts.app')
@section('titulo', 'Reporte de ingresos')
@section('migas', 'Reportes / Ingresos')

@section('contenido')
<div class="nl-card">
    <div class="nl-titulo"><h1><i class="bi bi-graph-up-arrow"></i> Reporte de Ventas (Ingresos)</h1>
        <div class="no-imprimir d-flex gap-2"><button class="btn btn-nl" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>@include('partials.exportar', ['tabla' => '#tablaRep', 'nombre' => 'reporte_ingresos'])</div></div>
    <form method="get" class="row g-2 mt-2 no-imprimir">
        <div class="col-md-3"><label class="form-label">Desde</label><input type="date" name="desde" class="form-control" value="{{ $desde }}"></div>
        <div class="col-md-3"><label class="form-label">Hasta</label><input type="date" name="hasta" class="form-control" value="{{ $hasta }}"></div>
        <div class="col-md-2 d-flex align-items-end"><button class="btn btn-nl w-100">Generar</button></div>
    </form>
    <div class="small text-muted mt-2">Periodo: {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}</div>
</div>
<div class="nl-card sin-pad"><div class="table-responsive">
    <table class="table nl-table" id="tablaRep">
        <thead><tr><th>N°</th><th>Fecha</th><th>Cliente</th><th>Productos</th><th>Vendedor</th><th class="text-end">Total</th></tr></thead>
        <tbody>
        @forelse($ventas as $v)
            <tr><td>{{ $v->id }}</td><td>{{ $v->fecha->format('d/m/Y H:i') }}</td><td>{{ $v->cliente->nombre ?? 'Cliente general' }}</td>
                <td class="small">@foreach($v->detalles->groupBy('producto_id') as $g){{ $g->sum('cantidad') }} x {{ $g->first()->producto->nombre ?? '' }}@if(!$loop->last), @endif @endforeach</td>
                <td>{{ $v->usuario->name ?? '' }}</td><td class="text-end">Bs {{ number_format($v->total, 2) }}</td></tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted">Sin ventas en el periodo.</td></tr>
        @endforelse
        </tbody>
        <tfoot><tr><th colspan="5" class="text-end">TOTAL VENDIDO ({{ $ventas->count() }} ventas)</th><th class="text-end verde-txt">Bs {{ number_format($total, 2) }}</th></tr></tfoot>
    </table>
</div></div>
@endsection
