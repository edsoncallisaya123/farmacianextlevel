@extends('layouts.app')
@section('titulo', 'Listado de cajas')
@section('migas', 'Caja')

@section('contenido')
<div class="nl-card">
    <div class="nl-titulo">
        <h1 style="font-size:1.4rem;text-transform:none">Listado de cajas</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('cajas.create') }}" class="btn btn-verde"><i class="bi bi-plus-circle-fill"></i> Abrir caja</a>
            @include('partials.exportar', ['tabla' => '#tablaCajas', 'nombre' => 'cajas'])
        </div>
    </div>
</div>
<div class="nl-card">
    <div class="nl-sub"><i class="bi bi-cash-coin"></i> Detalle de cajas</div>
    <div class="table-responsive">
        <table class="table nl-table" id="tablaCajas" data-tabla>
            <thead><tr><th>#</th><th>Usuario</th><th>Apertura</th><th>Inicial</th><th>Cierre</th><th>Final</th><th>Movimientos</th><th class="text-center no-exportar">Acciones</th></tr></thead>
            <tbody>
            @forelse($cajas as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>{{ $c->usuario->name ?? '' }}</td>
                    <td>{{ $c->fecha_apertura->format('d/m/Y') }}<div class="small text-muted">{{ $c->fecha_apertura->format('H:i') }}</div></td>
                    <td class="verde-txt">Bs {{ number_format($c->monto_inicial, 2) }}</td>
                    <td>@if($c->fecha_cierre){{ $c->fecha_cierre->format('d/m/Y') }}<div class="small text-muted">{{ $c->fecha_cierre->format('H:i') }}</div>@else<span class="estado abierta">Abierta</span>@endif</td>
                    <td class="verde-txt">{{ $c->monto_final !== null ? 'Bs ' . number_format($c->monto_final, 2) : '--' }}</td>
                    <td><span class="chip-ing">INGRESOS</span> Bs {{ number_format($c->ingresos ?? 0, 2) }}<br><span class="chip-egr">EGRESOS</span> Bs {{ number_format($c->egresos ?? 0, 2) }}</td>
                    <td class="text-center text-nowrap">
                        <a href="{{ route('cajas.show', $c) }}" class="btn-ico ver" title="Ver"><i class="bi bi-eye-fill"></i></a>
                        @if($c->estado === 'abierta')
                            <a href="{{ route('cajas.show', $c) }}#cerrar" class="btn-ico naranja" title="Movimientos y cierre"><i class="bi bi-lock-fill"></i></a>
                        @endif
                        <form method="post" action="{{ route('cajas.destroy', $c) }}" class="d-inline" data-confirmar="¿Eliminar esta caja?">
                            @csrf @method('DELETE')
                            <button class="btn-ico eliminar"><i class="bi bi-trash-fill"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr class="fila-vacia"><td colspan="8" class="text-center text-muted">Aún no hay cajas registradas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
