@extends('layouts.app')
@section('titulo', 'Proveedores')
@section('migas', 'Laboratorios')

@section('contenido')
<div class="nl-card">
    <div class="nl-titulo">
        <h1><i class="bi bi-eyedropper"></i> Gestión de proveedores</h1>
        <div class="d-flex gap-2 align-items-center">
            <span class="nl-badge-count">{{ $laboratorios->count() }} proveedores</span>
            @include('partials.exportar', ['tabla' => '#tablaLab', 'nombre' => 'proveedores'])
        </div>
    </div>
</div>

<div class="nl-card">
    <div class="nl-sub"><i class="bi bi-plus-circle-fill"></i> {{ $editar ? 'Editar proveedor' : 'Registrar nuevo proveedor' }}</div>
    <form method="post" action="{{ $editar ? route('laboratorios.update', $editar) : route('laboratorios.store') }}" class="row g-3">
        @csrf
        @if($editar) @method('PUT') @endif
        <div class="col-md-4"><label class="form-label">Laboratorio *</label><input name="nombre" class="form-control" value="{{ old('nombre', $editar->nombre ?? '') }}" required></div>
        <div class="col-md-4"><label class="form-label">Nombre del proveedor</label><input name="contacto" class="form-control" value="{{ old('contacto', $editar->contacto ?? '') }}"></div>
        <div class="col-md-2"><label class="form-label">Teléfono</label><input name="telefono" class="form-control" value="{{ old('telefono', $editar->telefono ?? '') }}"></div>
        <div class="col-md-2"><label class="form-label">Celular</label><input name="celular" class="form-control" value="{{ old('celular', $editar->celular ?? '') }}"></div>
        <div class="col-md-8"><label class="form-label">Dirección</label><input name="direccion" class="form-control" value="{{ old('direccion', $editar->direccion ?? '') }}"></div>
        <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-nl"><i class="bi bi-save"></i> {{ $editar ? 'Actualizar' : 'Registrar' }}</button>
            @if($editar)<a href="{{ route('laboratorios.index') }}" class="btn btn-nl-outline ms-2">Cancelar</a>@endif
        </div>
    </form>
</div>

<div class="nl-card">
    <div class="nl-sub"><i class="bi bi-list-task"></i> Proveedores registrados</div>
    <div class="table-responsive">
        <table class="table nl-table" id="tablaLab" data-tabla>
            <thead><tr><th>#</th><th>Laboratorio</th><th>Teléfono</th><th>Nombre proveedor</th><th>Celular</th><th class="text-center no-exportar">Acciones</th></tr></thead>
            <tbody>
            @forelse($laboratorios as $i => $l)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><span class="kpi-ico d-inline-flex me-1" style="background:#1d7be8;width:24px;height:24px;font-size:.7rem"><i class="bi bi-person-fill"></i></span> <b>{{ $l->nombre }}</b></td>
                    <td><i class="bi bi-telephone-fill text-primary"></i> {{ $l->telefono }}</td>
                    <td><i class="bi bi-person-vcard text-primary"></i> {{ $l->contacto }}</td>
                    <td>
                        @if($l->celular)
                            <a class="btn btn-sm text-white fw-bold" style="background:#25d366" target="_blank" href="https://wa.me/591{{ preg_replace('/\D/', '', $l->celular) }}"><i class="bi bi-whatsapp"></i> {{ $l->celular }}</a>
                        @endif
                    </td>
                    <td class="text-center text-nowrap">
                        <a href="{{ route('laboratorios.index', ['editar' => $l->id]) }}" class="btn-ico editar"><i class="bi bi-pencil-fill"></i></a>
                        <form method="post" action="{{ route('laboratorios.destroy', $l) }}" class="d-inline" data-confirmar="¿Eliminar este proveedor?">
                            @csrf @method('DELETE')
                            <button class="btn-ico eliminar"><i class="bi bi-trash-fill"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr class="fila-vacia"><td colspan="6" class="text-center text-muted">No hay proveedores registrados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
