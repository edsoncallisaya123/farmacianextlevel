@extends('layouts.app')
@section('titulo', 'Registro de nueva caja')
@section('migas', 'Caja / Apertura')

@section('contenido')
<div class="nl-card">
    <div class="nl-titulo"><h1 style="font-size:1.4rem;text-transform:none"><span class="kpi-ico" style="background:var(--nl-navy)"><i class="bi bi-cash"></i></span> Registro de Nueva Caja</h1></div>
</div>
<form method="post" action="{{ route('cajas.store') }}" class="nl-card">
    @csrf
    <div class="nl-sub"><i class="bi bi-pencil-square"></i> Ingrese los datos de la caja</div>
    <div class="row g-3">
        <div class="col-md-4"><label class="form-label">Fecha de apertura</label><input type="datetime-local" name="fecha_apertura" class="form-control" value="{{ old('fecha_apertura', date('Y-m-d\TH:i')) }}" required></div>
        <div class="col-md-4"><label class="form-label">Monto inicial (Bs)</label><input type="number" step="0.01" min="0" name="monto_inicial" class="form-control" value="{{ old('monto_inicial', '0') }}" required></div>
        <div class="col-md-4"><label class="form-label">Descripción</label><input name="descripcion" class="form-control" value="{{ old('descripcion') }}"></div>
    </div>
    <div class="text-end mt-4">
        <a href="{{ route('cajas.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i> Cancelar</a>
        <button class="btn btn-nl"><i class="bi bi-save"></i> Registrar Caja</button>
    </div>
</form>
@endsection
