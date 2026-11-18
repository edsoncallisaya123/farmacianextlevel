@extends('layouts.app')
@section('titulo', 'Actualizar contraseña')
@section('migas', 'Actualizar contraseña')

@section('contenido')
<form method="post" action="{{ route('perfil.password.update') }}" class="nl-card" style="max-width:520px">
    @csrf @method('PUT')
    <div class="nl-sub"><i class="bi bi-shield-lock-fill"></i> Actualizar contraseña</div>
    <div class="mb-3"><label class="form-label">Contraseña actual</label><input type="password" name="password_actual" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Nueva contraseña</label><input type="password" name="password" class="form-control" required minlength="6"></div>
    <div class="mb-3"><label class="form-label">Confirmar nueva contraseña</label><input type="password" name="password_confirmation" class="form-control" required></div>
    <button class="btn btn-nl">Guardar contraseña</button>
</form>
@endsection
