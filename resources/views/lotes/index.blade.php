@extends('layouts.app')
@section('titulo', 'Gestión de lotes')
@section('migas', 'Lotes')

@section('contenido')
<div class="nl-card">
    <div class="nl-titulo">
        <h1><i class="bi bi-boxes"></i> Gestión de Lotes</h1>
        <div class="d-flex gap-2 align-items-center">
            <span class="nl-badge-count">{{ $lotes->count() }} lotes</span>
            <button class="btn btn-nl" data-bs-toggle="modal" data-bs-target="#modalLote"><i class="bi bi-plus-circle-fill"></i> Nuevo lote</button>
        </div>
    </div>
</div>

<div class="nl-card">
    <form method="get" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Producto</label>
            <select name="producto" class="form-select">
                <option value="">Todos los productos</option>
                @foreach($productos as $p)<option value="{{ $p->id }}" @selected(request('producto') == $p->id)>{{ $p->nombre }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
                <option value="">Todos los estados</option>
                <option value="activo" @selected(request('estado') == 'activo')>Activo</option>
                <option value="por_vencer" @selected(request('estado') == 'por_vencer')>Por vencer (30 días)</option>
                <option value="vencido" @selected(request('estado') == 'vencido')>Vencido con stock</option>
                <option value="agotado" @selected(request('estado') == 'agotado')>Agotado</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-nl flex-fill"><i class="bi bi-funnel-fill"></i> Filtrar</button>
            <a href="{{ route('lotes.index') }}" class="btn btn-nl-outline"><i class="bi bi-arrow-repeat"></i></a>
        </div>
    </form>
</div>

<div class="row g-3">
    @forelse($lotes as $l)
        @php $clase = \Illuminate\Support\Str::slug($l->estado); @endphp
        <div class="col-md-6 col-xl-4">
            <div class="lote-card {{ $clase === 'vencido' ? 'vencido' : ($clase === 'por-vencer' ? 'por-vencer' : ($clase === 'agotado' ? 'agotado' : '')) }}">
                <h3>Lote #{{ $l->numero_lote }} <span class="nl-badge-count">{{ $l->cantidad_actual }}</span></h3>
                <div class="d-flex gap-3">
                    <div class="prod-img" style="width:84px;height:84px;border-radius:8px;flex:none">@include('partials.producto_imagen', ['p' => $l->producto])</div>
                    <div class="flex-fill small">
                        <div class="fw-semibold mb-1">{{ $l->producto->nombre }}</div>
                        <div class="prod-fila"><span>Cant.:</span><b>{{ $l->cantidad_actual }}u</b></div>
                        <div class="prod-fila"><span>Ingreso:</span><b>{{ $l->created_at->format('d/m/y') }}</b></div>
                        <div class="prod-fila"><span>Vence:</span><b>{{ $l->fecha_vencimiento ? $l->fecha_vencimiento->format('d/m/y') : 'Sin Fecha' }}</b></div>
                        <div class="prod-fila"><span>Estado:</span><span class="estado {{ $clase }}">{{ $l->estado }}</span></div>
                    </div>
                </div>
                @if($l->cantidad_actual > 0)
                <form method="post" action="{{ route('lotes.baja', $l) }}" class="mt-2 text-end" data-confirmar="¿Dar de baja este lote? El stock quedará en 0.">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm btn-outline-danger">Dar de baja</button>
                </form>
                @endif
            </div>
        </div>
    @empty
        <div class="col-12"><div class="alert alert-info">No hay lotes con esos filtros.</div></div>
    @endforelse
</div>

<div class="modal fade" id="modalLote" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="{{ route('lotes.store') }}" class="modal-content">
            @csrf
            <div class="modal-header"><h5 class="modal-title">Registrar lote (ingreso manual)</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-2"><label class="form-label">Producto *</label>
                    <select name="producto_id" class="form-select" required><option value="">Seleccione</option>
                        @foreach($productos as $p)<option value="{{ $p->id }}">{{ $p->codigo }} - {{ $p->nombre }}</option>@endforeach
                    </select></div>
                <div class="row g-2">
                    <div class="col-6"><label class="form-label">N.º de lote *</label><input name="numero_lote" class="form-control" required></div>
                    <div class="col-6"><label class="form-label">Vencimiento</label><input type="date" name="fecha_vencimiento" class="form-control" min="{{ date('Y-m-d') }}"></div>
                    <div class="col-6"><label class="form-label">Cantidad *</label><input type="number" name="cantidad" min="1" class="form-control" required></div>
                    <div class="col-6"><label class="form-label">Costo unit. (Bs)</label><input type="number" name="costo_unitario" min="0" step="0.01" value="0" class="form-control"></div>
                </div>
                <div class="small text-muted mt-2">Para ingresos de mercadería con proveedor use el módulo de Compras.</div>
            </div>
            <div class="modal-footer"><button class="btn btn-nl">Guardar lote</button></div>
        </form>
    </div>
</div>
@endsection
