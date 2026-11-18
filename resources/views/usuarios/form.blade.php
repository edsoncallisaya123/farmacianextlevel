@extends('layouts.app')
@php $nuevo = !$usuario->exists; @endphp
@section('titulo', $nuevo ? 'Nuevo usuario' : 'Editar usuario')
@section('migas', 'Usuarios')

@section('contenido')
<form method="post" action="{{ $nuevo ? route('usuarios.store') : route('usuarios.update', $usuario) }}" class="nl-card">
    @csrf
    @unless($nuevo) @method('PUT') @endunless
    <h2 class="fw-bold mb-1" style="color:#1d7be8;font-size:1.5rem">{{ $nuevo ? 'Crear un Nuevo Usuario' : 'Editar Usuario' }}</h2>
    <p class="text-muted">Ingresa todos los datos obligatorios</p>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Nombre de usuario *</label><input name="usuario" class="form-control" value="{{ old('usuario', $usuario->usuario) }}" required></div>
        <div class="col-md-6"><label class="form-label">Correo *</label><input type="email" name="email" class="form-control" value="{{ old('email', $usuario->email) }}" required></div>
        <div class="col-md-6"><label class="form-label">Nombres *</label><input name="name" class="form-control" value="{{ old('name', $usuario->name) }}" required></div>
        <div class="col-md-3"><label class="form-label">Apellido paterno</label><input name="apellido_paterno" class="form-control" value="{{ old('apellido_paterno', $usuario->apellido_paterno) }}"></div>
        <div class="col-md-3"><label class="form-label">Apellido materno</label><input name="apellido_materno" class="form-control" value="{{ old('apellido_materno', $usuario->apellido_materno) }}"></div>
        <div class="col-md-4"><label class="form-label">Teléfono</label><input name="telefono" class="form-control" value="{{ old('telefono', $usuario->telefono) }}"></div>
        <div class="col-md-8"><label class="form-label">Dirección</label><input name="direccion" class="form-control" value="{{ old('direccion', $usuario->direccion) }}"></div>
        <div class="col-md-4">
            <label class="form-label">Rol *</label>
            <select name="rol_id" class="form-select" required>
                <option value="">Seleccione un rol</option>
                @foreach($roles as $r)
                    <option value="{{ $r->id }}" @selected(old('rol_id', $usuario->rol_id) == $r->id)>{{ $r->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4"><label class="form-label">Contraseña {{ $nuevo ? '*' : '(dejar vacía para no cambiarla)' }}</label><input type="password" name="password" class="form-control" {{ $nuevo ? 'required' : '' }} minlength="6" autocomplete="new-password"></div>
        @unless($nuevo)
            @if($usuario->id !== auth()->id())
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" @checked(old('activo', $usuario->activo))><label class="form-check-label" for="activo">Usuario activo</label></div>
            </div>
            @endif
        @endunless
    </div>
    <div class="mt-4">
        <button class="btn btn-nl">Guardar</button>
        <a href="{{ route('usuarios.index') }}" class="btn btn-nl-outline">Cancelar</a>
    </div>
</form>
@endsection
