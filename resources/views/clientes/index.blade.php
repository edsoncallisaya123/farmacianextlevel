@extends('layouts.app')
@section('titulo', 'Clientes')
@section('migas', 'Clientes')

@section('contenido')
<div class="nl-card">
    <div class="nl-titulo">
        <h1><i class="bi bi-people-fill"></i> Clientes</h1>
        <div class="d-flex gap-2 align-items-center">
            <span class="nl-badge-count">{{ $clientes->count() }} clientes</span>
            @include('partials.exportar', ['tabla' => '#tablaClientes', 'nombre' => 'clientes'])
        </div>
    </div>
</div>

<div class="nl-card">
    <div class="nl-sub"><i class="bi bi-person-plus-fill"></i> {{ $editar ? 'Editar cliente' : 'Registro de nuevo cliente' }}</div>
    <form method="post" action="{{ $editar ? route('clientes.update', $editar) : route('clientes.store') }}" class="row g-3 align-items-end">
        @csrf
        @if($editar) @method('PUT') @endif
        <div class="col-md-4"><label class="form-label">Nombre *</label><input name="nombre" class="form-control" value="{{ old('nombre', $editar->nombre ?? '') }}" required></div>
        <div class="col-md-2"><label class="form-label">NIT/CI</label><input name="nit_ci" class="form-control" value="{{ old('nit_ci', $editar->nit_ci ?? '') }}"></div>
        <div class="col-md-2"><label class="form-label">Celular</label><input name="celular" class="form-control" value="{{ old('celular', $editar->celular ?? '') }}"></div>
        <div class="col-md-2"><label class="form-label">Correo</label><input type="email" name="correo" class="form-control" value="{{ old('correo', $editar->correo ?? '') }}"></div>
        <div class="col-md-2">
            <button class="btn btn-nl w-100"><i class="bi bi-save"></i> {{ $editar ? 'Actualizar' : 'Registrar' }}</button>
        </div>
    </form>
    @if($editar)<a href="{{ route('clientes.index') }}" class="small">Cancelar edición</a>@endif
</div>

<div class="nl-card">
    <div class="nl-sub"><i class="bi bi-list-task"></i> Clientes registrados</div>
    <div class="table-responsive">
        <table class="table nl-table" id="tablaClientes" data-tabla>
            <thead><tr><th>Nro</th><th>Nombre</th><th>NIT/CI</th><th>Celular</th><th>Correo</th><th class="text-center no-exportar">Acciones</th></tr></thead>
            <tbody>
            @forelse($clientes as $i => $c)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><span class="pill-azul"><i class="bi bi-person-fill"></i> {{ $c->nombre }}</span></td>
                    <td>{{ $c->nit_ci }}</td>
                    <td>{{ $c->celular }}</td>
                    <td>{{ $c->correo }}</td>
                    <td class="text-center text-nowrap">
                        <a href="{{ route('clientes.index', ['editar' => $c->id]) }}" class="btn-ico editar"><i class="bi bi-pencil-fill"></i></a>
                        <form method="post" action="{{ route('clientes.destroy', $c) }}" class="d-inline" data-confirmar="¿Eliminar este cliente?">
                            @csrf @method('DELETE')
                            <button class="btn-ico eliminar"><i class="bi bi-trash-fill"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr class="fila-vacia"><td colspan="6" class="text-center text-muted">No hay clientes registrados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
