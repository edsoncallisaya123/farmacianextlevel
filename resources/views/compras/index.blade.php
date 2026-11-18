@extends('layouts.app')
@section('titulo', 'Historial de compras')
@section('migas', 'Compras')

@section('contenido')
<div class="nl-card">
    <div class="nl-titulo">
        <h1><i class="bi bi-clock-history"></i> Historial de Compras</h1>
        <a href="{{ route('compras.create') }}" class="btn btn-verde"><i class="bi bi-plus-circle-fill"></i> REGISTRAR COMPRA</a>
    </div>
    <div class="mt-2 d-flex align-items-center gap-2">
        <span class="small">Total: <span class="nl-badge-count">{{ $compras->count() }}</span></span>
        <span class="ms-auto">@include('partials.exportar', ['tabla' => '#tablaCompras', 'nombre' => 'compras'])</span>
    </div>
</div>
<div class="nl-card">
    <div class="nl-sub"><i class="bi bi-list-task"></i> Registro de Compras</div>
    <div class="table-responsive">
        <table class="table nl-table" id="tablaCompras" data-tabla>
            <thead><tr><th>#</th><th>Detalles</th><th>Proveedor</th><th>Fecha</th><th>Total</th><th>Comprobante</th><th class="text-center no-exportar">Acciones</th></tr></thead>
            <tbody>
            @forelse($compras as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>
                        <details><summary class="btn btn-sm btn-nl-outline d-inline-block">Ver productos ({{ $c->detalles->count() }})</summary>
                            <ul class="small mt-2 mb-0 ps-3">
                                @foreach($c->detalles as $d)
                                    <li>{{ $d->cantidad }} x {{ $d->producto->nombre ?? '' }} <span class="text-muted">(lote {{ $d->lote->numero_lote ?? '-' }})</span></li>
                                @endforeach
                            </ul>
                        </details>
                    </td>
                    <td>{{ $c->laboratorio->nombre ?? '-' }}</td>
                    <td>{{ $c->fecha->format('d/m/Y') }}</td>
                    <td class="verde-txt">Bs {{ number_format($c->total, 2) }}</td>
                    <td><span class="pill-azul" style="font-size:.62rem">{{ $c->comprobante }}</span></td>
                    <td class="text-center text-nowrap">
                        <a href="{{ route('compras.show', $c) }}" class="btn-ico ver"><i class="bi bi-eye-fill"></i></a>
                        <form method="post" action="{{ route('compras.destroy', $c) }}" class="d-inline" data-confirmar="¿Eliminar esta compra y revertir el stock?">
                            @csrf @method('DELETE')
                            <button class="btn-ico eliminar"><i class="bi bi-trash-fill"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr class="fila-vacia"><td colspan="7" class="text-center text-muted">Aún no hay compras registradas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
