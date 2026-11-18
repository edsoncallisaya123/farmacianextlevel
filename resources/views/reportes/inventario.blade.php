@extends('layouts.app')
@section('titulo', 'Reporte de inventario')
@section('migas', 'Reportes / Inventario')

@section('contenido')
<div class="nl-card">
    <div class="nl-titulo"><h1><i class="bi bi-clipboard-data"></i> Reporte de Inventario</h1>
        <div class="no-imprimir d-flex gap-2"><button class="btn btn-nl" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>@include('partials.exportar', ['tabla' => '#tablaRep', 'nombre' => 'reporte_inventario'])</div></div>
    <div class="small text-muted mt-2">Generado el {{ now()->format('d/m/Y H:i') }}</div>
</div>
<div class="nl-card sin-pad"><div class="table-responsive">
    <table class="table nl-table" id="tablaRep">
        <thead><tr><th>Código</th><th>Producto</th><th>Categoría</th><th>Laboratorio</th><th class="text-end">Stock</th><th class="text-end">Mín.</th><th class="text-end">Máx.</th><th class="text-end">Precio</th><th class="text-end">Valor</th></tr></thead>
        <tbody>
        @foreach($productos as $p)
            <tr><td>{{ $p->codigo }}</td><td>{{ $p->nombre }}</td><td>{{ $p->categoria->nombre ?? '-' }}</td><td>{{ $p->laboratorio->nombre ?? '-' }}</td>
                <td class="text-end {{ $p->stock < $p->stock_minimo ? 'rojo-txt' : '' }}">{{ $p->stock }}</td><td class="text-end">{{ $p->stock_minimo }}</td><td class="text-end">{{ $p->stock_maximo }}</td>
                <td class="text-end">Bs {{ number_format($p->precio_venta, 2) }}</td><td class="text-end">Bs {{ number_format($p->stock * $p->precio_venta, 2) }}</td></tr>
        @endforeach
        </tbody>
        <tfoot><tr><th colspan="8" class="text-end">VALOR TOTAL DEL INVENTARIO (a precio de venta)</th><th class="text-end verde-txt">Bs {{ number_format($valor, 2) }}</th></tr></tfoot>
    </table>
</div></div>
@endsection
