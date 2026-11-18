@extends('layouts.app')
@section('titulo', 'Inicio')
@section('migas', 'Inicio')

@section('contenido')
@php $u = auth()->user(); @endphp
<div class="dash-bienvenida">
    <h1>Bienvenido, {{ $u->name }}</h1>
    <p>Resumen de tu farmacia · {{ now()->translatedFormat('d \d\e F \d\e Y') }}</p>
</div>

<div class="row g-3 mb-3">
    <div class="col-sm-6 col-xl-3">
        <div class="kpi2" style="--c:#0a3b78">
            <div class="ico"><i class="bi bi-box-seam-fill"></i></div>
            <div><div class="etq">Productos</div><div class="num">{{ $totales['productos'] }}</div>
                @if($u->tienePermiso('productos'))<a class="acc" href="{{ route('productos.create') }}"><i class="bi bi-plus-circle-fill"></i> Agregar producto</a>@endif</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi2" style="--c:#dc2626">
            <div class="ico"><i class="bi bi-cart-fill"></i></div>
            <div><div class="etq">Compras</div><div class="num">{{ $totales['compras'] }}</div>
                @if($u->tienePermiso('compras'))<a class="acc" href="{{ route('compras.create') }}"><i class="bi bi-plus-circle-fill"></i> Nueva compra</a>@endif</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi2" style="--c:#059669">
            <div class="ico"><i class="bi bi-cash-coin"></i></div>
            <div><div class="etq">Ventas</div><div class="num">{{ $totales['ventas'] }}</div>
                @if($u->tienePermiso('ventas'))<a class="acc" href="{{ route('ventas.create') }}"><i class="bi bi-plus-circle-fill"></i> Registrar venta</a>@endif</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi2" style="--c:#ea580c">
            <div class="ico"><i class="bi bi-people-fill"></i></div>
            <div><div class="etq">Clientes</div><div class="num">{{ $totales['clientes'] }}</div>
                @if($u->tienePermiso('clientes'))<a class="acc" href="{{ route('clientes.index') }}"><i class="bi bi-arrow-right-circle-fill"></i> Gestionar clientes</a>@endif</div>
        </div>
    </div>
</div>

<div class="hoy-box mb-3">
    <div class="t"><i class="bi bi-calendar-check-fill"></i> HOY</div>
    <div><div class="small text-muted">Ventas realizadas</div><div class="v">{{ $ventasHoy }}</div></div>
    <div><div class="small text-muted">Ingresos del día</div><div class="v">Bs {{ number_format($ingresosHoy, 2) }}</div></div>
</div>

<div class="row g-3 mb-3">
    @if($u->tienePermiso('ventas'))
    <div class="col-6 col-lg-3"><a class="acceso" href="{{ route('ventas.create') }}"><i class="bi bi-cash-stack"></i> Nueva venta</a></div>
    @endif
    @if($u->tienePermiso('compras'))
    <div class="col-6 col-lg-3"><a class="acceso" href="{{ route('compras.create') }}"><i class="bi bi-truck"></i> Nueva compra</a></div>
    @endif
    @if($u->tienePermiso('caja'))
    <div class="col-6 col-lg-3"><a class="acceso" href="{{ route('cajas.index') }}"><i class="bi bi-safe2-fill"></i> Caja</a></div>
    @endif
    @if($u->tienePermiso('inventario'))
    <div class="col-6 col-lg-3"><a class="acceso" href="{{ route('inventario.index') }}"><i class="bi bi-clipboard-data-fill"></i> Inventarios</a></div>
    @endif
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="nl-card sin-pad">
            <div class="panel-titulo"><span><i class="bi bi-exclamation-triangle-fill"></i> Alerta de inventario</span><span class="cuenta">{{ $alertas->count() }}</span></div>
            <div class="table-responsive">
                <table class="table nl-table">
                    <thead><tr><th>Producto</th><th class="text-center">Stock</th><th class="text-center">Estado</th><th class="text-center">Reponer</th></tr></thead>
                    <tbody>
                    @forelse($alertas as $p)
                        <tr>
                            <td><b>{{ $p->nombre }}</b><div class="small text-muted">Código: {{ $p->codigo }}</div></td>
                            <td class="text-center"><b class="rojo-txt">{{ $p->stock }}</b> / mín. {{ $p->stock_minimo }}</td>
                            <td class="text-center"><span class="estado {{ \Illuminate\Support\Str::slug($p->estado_stock) }}">{{ $p->estado_stock }}</span></td>
                            <td class="text-center">
                                @if($u->tienePermiso('compras'))
                                    <a href="{{ route('compras.create') }}" class="btn-ico naranja" title="Hacer una compra"><i class="bi bi-cart-plus-fill"></i></a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-4"><i class="bi bi-check-circle-fill text-success"></i> Todos los productos tienen stock suficiente.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="nl-card sin-pad">
            <div class="panel-titulo"><span><i class="bi bi-trophy-fill"></i> Más vendidos</span></div>
            <div class="p-3">
                @if($valores->isEmpty())
                    <div class="text-center py-5">Aún no hay ventas registradas.</div>
                @else
                    <canvas id="graficoVendidos" height="230"></canvas>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="nl-card sin-pad">
    <div class="panel-titulo"><span><i class="bi bi-calendar-x-fill"></i> Lotes por vencer o vencidos (30 días)</span><span class="cuenta">{{ $porVencer->count() }}</span></div>
    <div class="table-responsive">
        <table class="table nl-table">
            <thead><tr><th>Producto</th><th>Lote</th><th>Vence</th><th class="text-center">Cantidad</th><th class="text-center">Estado</th></tr></thead>
            <tbody>
            @forelse($porVencer as $l)
                <tr>
                    <td><b>{{ $l->producto->nombre ?? '' }}</b></td><td>{{ $l->numero_lote }}</td><td>{{ $l->fecha_vencimiento->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $l->cantidad_actual }}</td>
                    <td class="text-center"><span class="estado {{ \Illuminate\Support\Str::slug($l->estado) }}">{{ $l->estado }}</span></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4"><i class="bi bi-check-circle-fill text-success"></i> No hay lotes por vencer.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
@if($valores->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('graficoVendidos'), {
    type: 'doughnut',
    data: {
        labels: @json($etiquetas),
        datasets: [{ data: @json($valores), backgroundColor: ['#f97316', '#2563eb', '#16a34a', '#a855f7', '#ef4444'], borderWidth: 2, borderColor: '#fff' }]
    },
    options: { cutout: '58%', plugins: { legend: { position: 'bottom', labels: { color: '#111827', font: { size: 14, weight: 'bold' } } } } }
});
</script>
@endif
@endpush
