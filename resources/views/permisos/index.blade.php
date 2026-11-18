@extends('layouts.app')
@section('titulo', 'Permisos')
@section('migas', 'Permisos')

@section('contenido')
<div class="nl-card">
    <div class="nl-sub"><i class="bi bi-key-fill"></i> Permisos</div>
    <form method="post" action="{{ $editar ? route('permisos.update', $editar) : route('permisos.store') }}" class="row g-2 align-items-end">
        @csrf
        @if($editar) @method('PUT') @endif
        <div class="col-md-6">
            <label class="form-label">{{ $editar ? 'Editar permiso' : 'Crear nuevo permiso' }}</label>
            <input name="nombre" class="form-control" placeholder="Nombre del permiso" value="{{ old('nombre', $editar->nombre ?? '') }}" required>
        </div>
        <div class="col-auto">
            <button class="btn btn-nl">{{ $editar ? 'Actualizar' : 'Crear Nuevo Permiso' }}</button>
            @if($editar)<a href="{{ route('permisos.index') }}" class="btn btn-nl-outline">Cancelar</a>@endif
        </div>
    </form>
</div>
<div class="nl-card">
    <div class="fw-bold mb-3" style="color:var(--nl-navy)">Permisos Disponibles</div>
    <div>
        @foreach($permisos as $p)
            <span class="perm-btn">{{ $p->nombre }}</span>
        @endforeach
    </div>
</div>
<div class="row g-3">
    @foreach($permisos as $p)
        <div class="col-md-6 col-xl-4">
            <div class="nl-card h-100">
                <div class="nl-sub"><i class="bi bi-key-fill"></i> {{ $p->nombre }}</div>
                <div class="small text-muted mb-3">Clave: {{ $p->clave }} &middot; usado en {{ $p->roles_count }} rol(es)</div>
                <div class="d-flex gap-2">
                    <a href="{{ route('permisos.index', ['editar' => $p->id]) }}" class="btn btn-sm btn-nl flex-fill"><i class="bi bi-pencil-square"></i> Editar</a>
                    <form method="post" action="{{ route('permisos.destroy', $p) }}" class="flex-fill" data-confirmar="¿Eliminar este permiso?">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger w-100"><i class="bi bi-trash"></i> Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
