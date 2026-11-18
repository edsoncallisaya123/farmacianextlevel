@extends('layouts.app')
@section('titulo', 'Productos')
@section('migas', 'Productos')

@section('contenido')
<div class="nl-card">
    <div class="nl-titulo">
        <div class="d-flex align-items-center gap-3">
            <div class="kpi-ico" style="background:var(--nl-orange);width:46px;height:46px;border-radius:12px"><i class="bi bi-box-seam-fill"></i></div>
            <div>
                <h1 style="font-size:1.05rem;text-transform:none">Gestión de Productos</h1>
                <div class="small"><span class="nl-badge-count naranja">{{ $productos->count() }} productos registrados</span> &nbsp; <span class="nl-badge-count naranja" style="color:var(--nl-navy)"><i class="bi bi-tags-fill"></i> {{ $totalCategorias }} categorías</span></div>
            </div>
        </div>
        <a href="{{ route('productos.create') }}" class="btn btn-nl"><i class="bi bi-plus-circle-fill"></i> Nuevo Producto</a>
    </div>
</div>

<div class="nl-card">
    <div class="nl-sub"><i class="bi bi-list-ul"></i> Listado de Productos</div>
    <form method="get" class="row g-2 mb-3">
        <div class="col-md-6"><div class="input-group"><span class="input-group-text bg-white"><i class="bi bi-search text-success"></i></span><input name="q" class="form-control" placeholder="Buscar por nombre, código o descripción..." value="{{ request('q') }}"></div></div>
        <div class="col-md-4">
            <select name="categoria" class="form-select" onchange="this.form.submit()">
                <option value="">Todas las categorías</option>
                @foreach($categorias as $c)
                    <option value="{{ $c->id }}" @selected(request('categoria') == $c->id)>{{ $c->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-grid"><button class="btn btn-nl">Buscar</button></div>
    </form>

    <div class="row g-3">
        @forelse($productos as $i => $p)
            @php $vence = $p->proximo_vencimiento ? \Carbon\Carbon::parse($p->proximo_vencimiento) : null; @endphp
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <div class="prod-card">
                    <span class="num">#{{ $i + 1 }}</span>
                    <div class="prod-img">@include('partials.producto_imagen', ['p' => $p])</div>
                    <div class="prod-body">
                        <div class="nombre">{{ $p->nombre }} @unless($p->activo)<span class="badge text-bg-secondary">Inactivo</span>@endunless</div>
                        <div class="prod-fila"><span>Código:</span><b>{{ $p->codigo }}</b></div>
                        <div class="prod-fila"><span>Stock:</span><b class="{{ $p->stock < $p->stock_minimo ? 'rojo-txt' : 'verde-txt' }}">{{ $p->stock }} / {{ $p->stock_maximo }}</b></div>
                        <div class="prod-fila"><span>Precio:</span><b class="verde-txt">Bs {{ number_format($p->precio_venta, 2) }}</b></div>
                        <div class="prod-fila"><span>Vence:</span>
                            @if($vence)
                                <b class="{{ $vence->lte(\Carbon\Carbon::today()->addDays(30)) ? 'rojo-txt' : '' }}">{{ $vence->format('d/m/Y') }} @if($vence->lte(\Carbon\Carbon::today()->addDays(30)))<i class="bi bi-exclamation-triangle-fill"></i>@endif</b>
                            @else
                                <b style="color:#1d7be8">No tiene fecha</b>
                            @endif
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <a href="{{ route('tienda.producto', $p->id) }}" target="_blank" class="btn btn-sm btn-ico ver flex-fill" style="width:auto" title="Ver"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('productos.edit', $p) }}" class="btn btn-sm btn-ico editar flex-fill" style="width:auto" title="Editar"><i class="bi bi-pencil-fill"></i></a>
                            <form method="post" action="{{ route('productos.destroy', $p) }}" class="flex-fill" data-confirmar="¿Eliminar este producto?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-ico eliminar w-100" style="width:auto" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="alert alert-info mb-0">No se encontraron productos.</div></div>
        @endforelse
    </div>
</div>
@endsection
