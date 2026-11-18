@extends('layouts.app')
@section('titulo', 'Reporte de caja')
@section('migas', 'Caja / Reporte')

@section('contenido')
@php
    $ing = $caja->total_ingresos; $egr = $caja->total_egresos;
    $esperado = $caja->monto_esperado;
@endphp
<div class="nl-card">
    <div class="nl-titulo">
        <h1 style="font-size:1.3rem;text-transform:none">Reporte de Caja N° {{ $caja->id }}</h1>
        <div class="d-flex gap-2 align-items-center no-imprimir">
            <span class="estado {{ $caja->estado === 'abierta' ? 'abierta' : 'cerrada' }}" style="font-size:.8rem;padding:7px 14px">{{ strtoupper($caja->estado === 'abierta' ? 'ABIERTA' : 'CERRADA') }}</span>
            <button class="btn btn-danger fw-bold" onclick="window.print()">Exportar PDF</button>
            <a href="{{ route('cajas.index') }}" class="btn btn-nl-outline">Volver</a>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="nl-card h-100">
            <div class="nl-sub"><i class="bi bi-folder2-open"></i> Información de Caja</div>
            <ul class="timeline">
                <li><span class="punto" style="background:var(--nl-navy)"><i class="bi bi-check-lg"></i></span>
                    <div class="fw-bold">Apertura</div><div class="small">{{ $caja->fecha_apertura->format('d/m/Y H:i') }}</div>
                    <span class="pill-azul" style="font-size:.65rem">BS / {{ number_format($caja->monto_inicial, 2) }}</span></li>
                <li><span class="punto" style="background:var(--nl-teal)"><i class="bi bi-lock-fill"></i></span>
                    <div class="fw-bold">Cierre</div><div class="small">{{ $caja->fecha_cierre ? $caja->fecha_cierre->format('d/m/Y H:i') : 'Caja abierta' }}</div>
                    @if($caja->monto_final !== null)<span class="chip-ing">BS / {{ number_format($caja->monto_final, 2) }}</span>@endif</li>
                <li><span class="punto" style="background:#1d7be8"><i class="bi bi-list-ul"></i></span>
                    <div class="fw-bold">Balance</div>
                    <span class="chip-ing">+BS/ {{ number_format($ing, 2) }}</span> <span class="chip-egr">-BS/ {{ number_format($egr, 2) }}</span><br>
                    <span class="pill-azul mt-1" style="font-size:.65rem">MONTO ESPERADO: BS/ {{ number_format($esperado, 2) }}</span>
                    @if($caja->monto_final !== null)<br><span class="pill-azul mt-1" style="font-size:.65rem;background:{{ abs($caja->monto_final - $esperado) < 0.005 ? '#1e9e62' : '#f0503e' }}">DIFERENCIA: BS/ {{ number_format($caja->monto_final - $esperado, 2) }}</span>@endif</li>
            </ul>
            @if($caja->descripcion)<div class="small text-muted">Notas: {{ $caja->descripcion }}</div>@endif
            <div class="small text-muted">Responsable: {{ $caja->usuario->nombre_completo ?? '' }}</div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="nl-card">
            <div class="d-flex justify-content-around mb-3">
                <div>Ingresos <span class="chip-ing" style="font-size:.8rem">{{ $caja->movimientos->where('tipo', 'ingreso')->count() }}</span></div>
                <div>Egresos <span class="chip-egr" style="font-size:.8rem">{{ $caja->movimientos->where('tipo', 'egreso')->count() }}</span></div>
            </div>
            <div class="table-responsive">
                <table class="table nl-table">
                    <thead><tr><th>Hora</th><th>Tipo</th><th>Detalle</th><th class="text-end">Monto</th></tr></thead>
                    <tbody>
                    @forelse($caja->movimientos->sortBy('fecha') as $m)
                        <tr>
                            <td>{{ $m->fecha->format('d/m H:i') }}</td>
                            <td><span class="{{ $m->tipo === 'ingreso' ? 'chip-ing' : 'chip-egr' }}">{{ strtoupper($m->tipo) }}</span></td>
                            <td>{{ $m->concepto }} <span class="text-muted small">({{ $m->origen }})</span></td>
                            <td class="text-end {{ $m->tipo === 'ingreso' ? 'verde-txt' : 'rojo-txt' }}">BS/ {{ number_format($m->monto, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">Sin movimientos.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($caja->estado === 'abierta')
        <div class="row g-3 no-imprimir" id="cerrar">
            <div class="col-md-6">
                <div class="nl-card h-100">
                    <div class="nl-sub"><i class="bi bi-arrow-left-right"></i> Registrar ingreso / egreso</div>
                    <form method="post" action="{{ route('cajas.movimiento', $caja) }}" class="row g-2">
                        @csrf
                        <div class="col-5"><select name="tipo" class="form-select"><option value="ingreso">Ingreso</option><option value="egreso">Egreso</option></select></div>
                        <div class="col-7"><input type="number" step="0.01" min="0.01" name="monto" class="form-control" placeholder="Monto (Bs)" required></div>
                        <div class="col-12"><input name="concepto" class="form-control" placeholder="Concepto" required></div>
                        <div class="col-12"><button class="btn btn-nl w-100">Registrar movimiento</button></div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="nl-card h-100">
                    <div class="nl-sub"><i class="bi bi-lock-fill"></i> Cierre de caja (arqueo)</div>
                    <form method="post" action="{{ route('cajas.cerrar', $caja) }}" data-confirmar="¿Cerrar la caja? Esta acción no se puede deshacer.">
                        @csrf
                        <label class="form-label">Monto contado físicamente (Bs)</label>
                        <input type="number" step="0.01" min="0" name="monto_final" class="form-control mb-1" required>
                        <div class="small text-muted mb-3">Monto esperado: Bs {{ number_format($esperado, 2) }}</div>
                        <button class="btn btn-danger w-100">Cerrar caja</button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
