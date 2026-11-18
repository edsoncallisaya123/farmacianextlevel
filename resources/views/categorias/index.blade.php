@extends('layouts.app')
@section('titulo', 'Categorías')
@section('migas', 'Catálogo')

@section('contenido')
<div class="nl-card">
    <div class="nl-titulo">
        <h1><i class="bi bi-tags-fill"></i> Gestión de categorías</h1>
        <div class="d-flex gap-2 align-items-center">
            <span class="nl-badge-count"><i class="bi bi-list-ul"></i> {{ $categorias->count() }} categorías</span>
            @include('partials.exportar', ['tabla' => '#tablaCategorias', 'nombre' => 'categorias'])
        </div>
    </div>
</div>

<div class="nl-card">
    <div class="nl-sub"><i class="bi bi-plus-circle-fill"></i> {{ $editar ? 'Editar categoría' : 'Nueva categoría' }}</div>
    <form method="post" action="{{ $editar ? route('categorias.update', $editar) : route('categorias.store') }}" class="row g-3 align-items-end">
        @csrf
        @if($editar) @method('PUT') @endif
        <div class="col-md-4"><label class="form-label">Nombre *</label><input name="nombre" class="form-control" value="{{ old('nombre', $editar->nombre ?? '') }}" required></div>
        <div class="col-md-5"><label class="form-label">Descripción</label><input name="descripcion" class="form-control" value="{{ old('descripcion', $editar->descripcion ?? '') }}"></div>
        <div class="col-md-3">
            <button class="btn btn-nl"><i class="bi bi-save"></i> {{ $editar ? 'Actualizar' : 'Registrar' }}</button>
            @if($editar)<a href="{{ route('categorias.index') }}" class="btn btn-nl-outline">Cancelar</a>@endif
        </div>
    </form>
</div>

<div class="nl-card">
    <div class="nl-sub"><i class="bi bi-list-task"></i> Categorías registradas</div>
    <div class="table-responsive">
        <table class="table nl-table" id="tablaCategorias" data-tabla>
            <thead><tr><th>#</th><th>Categoría</th><th>Descripción</th><th class="text-end">Productos</th><th class="text-center no-exportar">Acciones</th></tr></thead>
            <tbody>
            @forelse($categorias as $i => $c)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><span class="kpi-ico d-inline-flex me-1" style="background:#1d7be8;width:24px;height:24px;font-size:.7rem"><i class="bi bi-tag-fill"></i></span> <b>{{ $c->nombre }}</b></td>
                    <td>{{ $c->descripcion }}</td>
                    <td class="text-end">{{ $c->productos_count }}</td>
                    <td class="text-center text-nowrap">
                        <a href="{{ route('categorias.index', ['editar' => $c->id]) }}" class="btn-ico editar"><i class="bi bi-pencil-fill"></i></a>
                        <form method="post" action="{{ route('categorias.destroy', $c) }}" class="d-inline" data-confirmar="¿Eliminar esta categoría?">
                            @csrf @method('DELETE')
                            <button class="btn-ico eliminar"><i class="bi bi-trash-fill"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr class="fila-vacia"><td colspan="5" class="text-center text-muted">No hay categorías registradas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
