@extends('layouts.app')
@section('titulo', 'Gestión de inventario')
@section('migas', 'Inventarios')

@section('contenido')
<div class="nl-card">
    <div class="fs-5 fw-bold" style="color:var(--nl-navy)">Gestión de Inventario</div>
    <div class="small text-muted">Sucursal</div>
    <input class="form-control mt-1" style="max-width:340px" value="FARMACIA NEXT LEVEL" readonly>
</div>
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="stat-card text-center"><div class="etq">Total productos</div><div class="valor">{{ $totalProductos }}</div><a href="{{ route('productos.index') }}" class="btn btn-nl-outline btn-sm mt-2">Ver detalles</a></div></div>
    <div class="col-md-4"><div class="stat-card text-center"><div class="etq text-danger">Productos bajo stock</div><div class="valor">{{ $bajoStock->count() }}</div><a href="#bajo" class="btn btn-sm btn-outline-danger mt-2"><i class="bi bi-search"></i> Ver listado</a></div></div>
    <div class="col-md-4"><div class="stat-card text-center"><div class="etq text-danger">Lotes por vencer (30 días)</div><div class="valor">{{ $porVencer->count() }}</div><a href="#vencer" class="btn btn-sm btn-outline-danger mt-2"><i class="bi bi-search"></i> Ver listado</a></div></div>
</div>
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="stat-card"><div class="etq">Reporte de compras ({{ $mes->translatedFormat('M Y') }})</div><div class="mt-2">Compras realizadas<br><b>{{ $comprasMes }} compras</b></div><div class="mt-2 etq">Total comprado</div><div class="valor">Bs {{ number_format($totalComprado, 2) }}</div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="etq text-success">Reporte de ventas ({{ $mes->translatedFormat('M Y') }})</div><div class="mt-2">Ventas realizadas<br><b>{{ $ventasMes }} ventas</b></div><div class="mt-2 etq">Total vendido</div><div class="valor text-success">Bs {{ number_format($totalVendido, 2) }}</div></div></div>
    <div class="col-md-4"><div class="stat-card text-center"><div class="etq">Reportes generales</div><div class="fw-bold mt-2">FARMACIA NEXT LEVEL</div>@if(auth()->user()->tienePermiso('reportes'))<a href="{{ route('reportes.inventario') }}" class="btn btn-nl-outline btn-sm mt-3"><i class="bi bi-file-earmark-text"></i> Ver reportes</a>@endif</div></div>
</div>

<div class="nl-card" id="bajo">
    <div class="nl-sub"><i class="bi bi-exclamation-triangle-fill text-danger"></i> Productos con bajo stock</div>
    <div class="table-responsive"><table class="table nl-table">
        <thead><tr><th>Código</th><th>Producto</th><th class="text-center">Stock</th><th class="text-center">Mínimo</th><th class="text-center">Estado</th></tr></thead>
        <tbody>
        @forelse($bajoStock as $p)
            <tr><td>{{ $p->codigo }}</td><td>{{ $p->nombre }}</td><td class="text-center rojo-txt">{{ $p->stock }}</td><td class="text-center">{{ $p->stock_minimo }}</td><td class="text-center"><span class="estado {{ \Illuminate\Support\Str::slug($p->estado_stock) }}">{{ $p->estado_stock }}</span></td></tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted">Todos los productos tienen stock suficiente.</td></tr>
        @endforelse
        </tbody></table></div>
</div>
<div class="nl-card" id="vencer">
    <div class="nl-sub"><i class="bi bi-calendar-x-fill text-danger"></i> Control de fecha de vencimiento</div>
    <div class="table-responsive"><table class="table nl-table">
        <thead><tr><th>Producto</th><th>Lote</th><th>Vence</th><th class="text-center">Cantidad</th><th class="text-center">Estado</th></tr></thead>
        <tbody>
        @forelse($porVencer as $l)
            <tr><td>{{ $l->producto->nombre ?? '' }}</td><td>{{ $l->numero_lote }}</td><td>{{ $l->fecha_vencimiento->format('d/m/Y') }}</td><td class="text-center">{{ $l->cantidad_actual }}</td><td class="text-center"><span class="estado {{ \Illuminate\Support\Str::slug($l->estado) }}">{{ $l->estado }}</span></td></tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted">No hay lotes por vencer en los próximos 30 días.</td></tr>
        @endforelse
        </tbody></table></div>
</div>
@endsection
