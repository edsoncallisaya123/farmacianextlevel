@extends('layouts.app')
@php $nuevo = !$rol->exists; @endphp
@section('titulo', $nuevo ? 'Nuevo rol' : 'Editar rol')
@section('migas', 'Roles')

@section('contenido')
<form method="post" action="{{ $nuevo ? route('roles.store') : route('roles.update', $rol) }}" class="nl-card">
    @csrf
    @unless($nuevo) @method('PUT') @endunless
    <div class="nl-sub"><i class="bi bi-person-gear"></i> {{ $nuevo ? 'Crear nuevo rol' : 'Editar rol' }}</div>
    <div class="row g-3 mb-3">
        <div class="col-md-4"><label class="form-label">Nombre del rol *</label><input name="nombre" class="form-control" value="{{ old('nombre', $rol->nombre) }}" required></div>
        <div class="col-md-8"><label class="form-label">Descripción</label><input name="descripcion" class="form-control" value="{{ old('descripcion', $rol->descripcion) }}"></div>
    </div>
    <div class="fw-semibold mb-2">Permisos del rol</div>
    <div class="row g-2">
        @foreach($permisos as $p)
            <div class="col-sm-6 col-lg-3">
                <label class="d-flex align-items-center gap-2 border rounded p-2" style="cursor:pointer">
                    <input type="checkbox" class="form-check-input mt-0" name="permisos[]" value="{{ $p->id }}" @checked(in_array($p->id, old('permisos', $asignados)))>
                    <span>{{ $p->nombre }}</span>
                </label>
            </div>
        @endforeach
    </div>
    <div class="mt-4">
        <button class="btn btn-nl">Guardar</button>
        <a href="{{ route('roles.index') }}" class="btn btn-nl-outline">Cancelar</a>
    </div>
</form>
@endsection
