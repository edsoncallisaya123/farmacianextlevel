@extends('layouts.app')
@section('titulo', 'Usuarios')
@section('migas', 'Usuarios')

@section('contenido')
<div class="nl-card">
    <div class="nl-titulo">
        <h1><i class="bi bi-person-fill"></i> Gestión de usuarios</h1>
        <a href="{{ route('usuarios.create') }}" class="btn btn-nl">Crear Usuario</a>
    </div>
</div>
<div class="nl-card sin-pad">
    <div class="p-3 pb-0">@include('partials.exportar', ['tabla' => '#tablaUsuarios', 'nombre' => 'usuarios'])</div>
    <div class="p-3">
    <div class="table-responsive">
        <table class="table nl-table" id="tablaUsuarios" data-tabla>
            <thead><tr><th>#</th><th>Nombres</th><th>Rol</th><th>Teléfono</th><th>Estado</th><th class="text-center no-exportar">Acciones</th></tr></thead>
            <tbody>
            @foreach($usuarios as $i => $u)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="nl-avatar sm">@if($u->foto_url)<img src="{{ $u->foto_url }}" alt="">@else<i class="bi bi-person-fill"></i>@endif</div>
                            <div><div class="fw-semibold">{{ $u->nombre_completo }}</div><div class="small text-muted">{{ $u->email }}</div></div>
                        </div>
                    </td>
                    <td>{{ $u->rol->nombre ?? '-' }}</td>
                    <td>{{ $u->telefono }}</td>
                    <td><span class="estado {{ $u->activo ? 'activo' : 'agotado' }}">{{ $u->activo ? 'Activo' : 'Inactivo' }}</span></td>
                    <td class="text-center text-nowrap">
                        <a href="{{ route('usuarios.edit', $u) }}" class="btn-ico editar" title="Editar"><i class="bi bi-pencil-fill"></i></a>
                        @if($u->id !== auth()->id())
                        <form method="post" action="{{ route('usuarios.destroy', $u) }}" class="d-inline" data-confirmar="¿Eliminar este usuario?">
                            @csrf @method('DELETE')
                            <button class="btn-ico eliminar" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    </div>
</div>
@endsection
