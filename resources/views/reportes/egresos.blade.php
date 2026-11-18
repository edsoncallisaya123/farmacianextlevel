@extends('layouts.app')
@section('titulo', 'Reporte de egresos')
@section('migas', 'Reportes / Egresos')

@section('contenido')
<div class="nl-card">
    <div class="nl-titulo"><h1><i class="bi bi-graph-down-arrow"></i> Reporte de Compras (Egresos)</h1>
        <div class="no-imprimir d-flex gap-2"><button class="btn btn-nl" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>@include('partials.exportar', ['tabla' => '#tablaRep', 'nombre' => 'reporte_egresos'])</div></div>
    <form method="get" class="row g-2 mt-2 no-imprimir">
        <div class="col-md-3"><label class="form-label">Desde</label><input type="date" name="desde" class="form-control" value="{{ $desde }}"></div>
        <div class="col-md-3"><label class="form-label">Hasta</label><input type="date" name="hasta" class="form-control" value="{{ $hasta }}"></div>
        <div class="col-md-2 d-flex align-items-end"><button class="btn btn-nl w-100">Generar</button></div>
    </form>
    <div class="small text-muted mt-2">Periodo: {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}</div>
</div>
<div class="nl-card sin-pad"><div class="table-responsive">
    <table class="table nl-table" id="tablaRep">
        <thead><tr><th>Tipo</th><th>Fecha</th><th>Proveedor / concepto</th><th>Comprobante</th><th>Registrado por</th><th class="text-end">Monto</th></tr></thead>
        <tbody>
        @foreach($compras as $c)
            <tr><td>Compra N° {{ $c->id }}</td><td>{{ $c->fecha->format('d/m/Y') }}</td><td>{{ $c->laboratorio->nombre ?? '' }}</td><td>{{ $c->comprobante }}</td><td>{{ $c->usuario->name ?? '' }}</td><td class="text-end">Bs {{ number_format($c->total, 2) }}</td></tr>
        @endforeach
        @foreach($otros as $o)
            <tr><td>Egreso de caja</td><td>{{ $o->fecha->format('d/m/Y') }}</td><td>{{ $o->concepto }}</td><td>-</td><td>-</td><td class="text-end">Bs {{ number_format($o->monto, 2) }}</td></tr>
        @endforeach
        @if($compras->isEmpty() && $otros->isEmpty())
            <tr><td colspan="6" class="text-center text-muted">Sin egresos en el periodo.</td></tr>
        @endif
        </tbody>
        <tfoot><tr><th colspan="5" class="text-end">TOTAL EGRESOS</th><th class="text-end rojo-txt">Bs {{ number_format($total, 2) }}</th></tr></tfoot>
    </table>
</div></div>
@endsection
