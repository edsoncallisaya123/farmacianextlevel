@extends('layouts.app')
@section('titulo', 'Perfil')
@section('migas', 'Perfil')

@section('contenido')
<div class="nl-card">
    <div class="d-flex align-items-center gap-3">
        <div class="nl-avatar" style="width:64px;height:64px">
            @if($usuario->foto_url)<img src="{{ $usuario->foto_url }}" alt="">@else<i class="bi bi-person-fill" style="font-size:2.6rem"></i>@endif
        </div>
        <div><div class="fs-5 fw-bold text-navy" style="color:var(--nl-navy)">{{ $usuario->nombre_completo }}</div><div class="text-muted">{{ $usuario->rol->nombre ?? '' }}</div></div>
    </div>
</div>
<form method="post" action="{{ route('perfil.update') }}" enctype="multipart/form-data" class="nl-card">
    @csrf @method('PUT')
    <div class="nl-titulo mb-3"><div class="text-muted fs-5">Editar Perfil</div><button class="btn btn-nl">Actualizar Datos</button></div>
    <div class="text-uppercase small text-muted mb-3">Información de usuario</div>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Foto de perfil</label><input type="file" name="foto" class="form-control" accept="image/*"></div>
        <div class="col-md-6"><label class="form-label">Nombre de usuario</label><input name="usuario" class="form-control" value="{{ old('usuario', $usuario->usuario) }}"></div>
        <div class="col-md-6"><label class="form-label">Correo</label><input type="email" name="email" class="form-control" value="{{ old('email', $usuario->email) }}" required></div>
        <div class="col-md-6"><label class="form-label">Nombre</label><input name="name" class="form-control" value="{{ old('name', $usuario->name) }}" required></div>
        <div class="col-md-6"><label class="form-label">Apellido paterno</label><input name="apellido_paterno" class="form-control" value="{{ old('apellido_paterno', $usuario->apellido_paterno) }}"></div>
        <div class="col-md-6"><label class="form-label">Apellido materno</label><input name="apellido_materno" class="form-control" value="{{ old('apellido_materno', $usuario->apellido_materno) }}"></div>
        <div class="col-md-6"><label class="form-label">Teléfono</label><input name="telefono" class="form-control" value="{{ old('telefono', $usuario->telefono) }}"></div>
        <div class="col-md-6"><label class="form-label">Dirección</label><input name="direccion" class="form-control" value="{{ old('direccion', $usuario->direccion) }}"></div>
    </div>
</form>
@endsection
