@extends('layouts.app')
@section('titulo', 'Compra N° ' . $compra->id)
@section('migas', 'Compras / Detalle')

@section('contenido')
<div class="nl-card">
    <div class="nl-titulo">
        <h1><i class="bi bi-receipt"></i> Compra N° {{ $compra->id }}</h1>
        <div class="no-imprimir"><button class="btn btn-nl" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button> <a href="{{ route('compras.index') }}" class="btn btn-nl-outline">Volver</a></div>
    </div>
    <div class="row mt-3 small">
        <div class="col-md-3"><b>Proveedor:</b> {{ $compra->laboratorio->nombre ?? '-' }}</div>
        <div class="col-md-3"><b>Fecha:</b> {{ $compra->fecha->format('d/m/Y') }}</div>
        <div class="col-md-3"><b>Comprobante:</b> {{ $compra->comprobante }}</div>
        <div class="col-md-3"><b>Registrada por:</b> {{ $compra->usuario->nombre_completo ?? '' }}</div>
    </div>
</div>
<div class="nl-card sin-pad">
    <div class="table-responsive">
        <table class="table nl-table">
            <thead><tr><th>Código</th><th>Producto</th><th>Lote</th><th>Vence</th><th class="text-end">Cant.</th><th class="text-end">Costo</th><th class="text-end">Subtotal</th></tr></thead>
            <tbody>
            @foreach($compra->detalles as $d)
                <tr>
                    <td>{{ $d->producto->codigo ?? '' }}</td><td>{{ $d->producto->nombre ?? '' }}</td><td>{{ $d->lote->numero_lote ?? '' }}</td>
                    <td>{{ optional($d->lote->fecha_vencimiento ?? null)->format('d/m/Y') ?? 'Sin fecha' }}</td>
                    <td class="text-end">{{ $d->cantidad }}</td><td class="text-end">Bs {{ number_format($d->costo_unitario, 2) }}</td><td class="text-end">Bs {{ number_format($d->subtotal, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot><tr><th colspan="6" class="text-end">TOTAL</th><th class="text-end verde-txt">Bs {{ number_format($compra->total, 2) }}</th></tr></tfoot>
        </table>
    </div>
</div>
@endsection
