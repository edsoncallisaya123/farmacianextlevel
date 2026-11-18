@extends('layouts.app')
@section('titulo', 'Factura de venta')
@section('migas', 'Ventas / Recibo')

@section('contenido')
<div class="recibo">
    <div class="nl-card">
        <div class="text-center mb-3">
            <div class="kpi-ico mx-auto mb-2" style="background:var(--nl-teal);width:52px;height:52px;font-size:1.5rem"><i class="bi bi-heart-pulse-fill"></i></div>
            <h2 class="h5 fw-bold mb-0" style="color:var(--nl-navy)">FARMACIA NEXT LEVEL</h2>
            <div class="small text-muted">Recibo de venta N° {{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
        <div class="row small mb-3">
            <div class="col-6"><b>Fecha:</b> {{ $venta->fecha->format('d/m/Y H:i') }}</div>
            <div class="col-6"><b>Atendido por:</b> {{ $venta->usuario->nombre_completo ?? '' }}</div>
            <div class="col-6"><b>Cliente:</b> {{ $venta->cliente->nombre ?? 'S/N' }}</div>
            <div class="col-6"><b>NIT/CI:</b> {{ $venta->cliente->nit_ci ?? '0' }}</div>
        </div>
        <table class="table nl-table">
            <thead><tr><th>Producto</th><th class="text-end">Cant.</th><th class="text-end">Precio</th><th class="text-end">Subtotal</th></tr></thead>
            <tbody>
            @foreach($lineas as $l)
                <tr><td>{{ $l->nombre }}<div class="small text-muted">{{ $l->codigo }}</div></td><td class="text-end">{{ $l->cantidad }}</td><td class="text-end">Bs {{ number_format($l->precio, 2) }}</td><td class="text-end">Bs {{ number_format($l->subtotal, 2) }}</td></tr>
            @endforeach
            </tbody>
            <tfoot><tr><th colspan="3" class="text-end">TOTAL</th><th class="text-end verde-txt">Bs {{ number_format($venta->total, 2) }}</th></tr></tfoot>
        </table>
        <div class="text-center small text-muted">¡Gracias por su compra!</div>
    </div>
    <div class="no-imprimir text-center">
        <button class="btn btn-nl" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>
        <a href="{{ route('ventas.create') }}" class="btn btn-verde">Nueva venta</a>
        <a href="{{ route('ventas.index') }}" class="btn btn-nl-outline">Historial</a>
    </div>
</div>
@endsection
