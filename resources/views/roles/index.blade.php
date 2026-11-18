@extends('layouts.app')
@section('titulo', 'Roles')
@section('migas', 'Roles')

@section('contenido')
<div class="nl-card">
    <div class="nl-titulo">
        <h1><i class="bi bi-person-gear"></i> Gestión roles</h1>
        <a href="{{ route('roles.create') }}" class="btn btn-nl">Crear Nuevo Rol</a>
    </div>
</div>
<div class="row g-3">
    @foreach($roles as $rol)
        <div class="col-md-6 col-xl-4">
            <div class="nl-card h-100">
                <div class="nl-sub"><i class="bi bi-person-fill-gear"></i> {{ $rol->nombre }}</div>
                <div class="small text-muted mb-1">{{ $rol->descripcion }}</div>
                <div class="fw-semibold mb-2">Permisos:</div>
                <div style="max-height:120px;overflow:auto">
                    @forelse($rol->permisos as $p)
                        <span class="badge-perm">{{ $p->nombre }}</span>
                    @empty
                        <span class="text-muted small">Sin permisos asignados</span>
                    @endforelse
                </div>
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('roles.edit', $rol) }}" class="btn btn-sm btn-outline-success flex-fill"><i class="bi bi-pencil-square"></i> Editar</a>
                    <form method="post" action="{{ route('roles.destroy', $rol) }}" class="flex-fill" data-confirmar="¿Eliminar este rol?">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger w-100"><i class="bi bi-trash"></i> Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
