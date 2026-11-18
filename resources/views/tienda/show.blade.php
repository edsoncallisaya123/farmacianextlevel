@extends('layouts.tienda')
@section('titulo', $producto->nombre)

@section('contenido')
@php $disp = $producto->stock; @endphp
<div class="t-banner" style="padding:20px 0">
    <div class="container"><h1 style="font-size:1.6rem"><i class="bi bi-capsule-pill"></i> Detalle del producto</h1></div>
</div>
<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-3">@include('partials.tienda_sidebar')</div>
        <div class="col-lg-9">
            <a href="{{ route('tienda') }}" class="fw-bold" style="color:var(--nl-orange-dark)"><i class="bi bi-arrow-left-circle-fill"></i> Volver al catálogo</a>
            <div class="t-det mt-3">
                <div class="row g-0">
                    <div class="col-md-5"><div class="foto">@include('partials.producto_imagen', ['p' => $producto])</div></div>
                    <div class="col-md-7 p-4">
                        <div class="dato">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</div>
                        <h1>{{ $producto->nombre }}</h1>
                        <div class="precio">Bs {{ number_format($producto->precio_venta, 2) }}</div>
                        <div class="mt-3 aviso-stock {{ $disp <= 0 ? 'agotado' : ($disp < $producto->stock_minimo ? 'limitado' : 'disponible') }}">
                            @if($disp <= 0)<b><i class="bi bi-x-circle-fill"></i> Producto agotado</b> · por el momento no hay unidades.
                            @elseif($disp < $producto->stock_minimo)<b><i class="bi bi-exclamation-triangle-fill"></i> Stock limitado</b> · {{ $disp }} unidades disponibles.
                            @else<b><i class="bi bi-check-circle-fill"></i> Disponible</b> · {{ $disp }} unidades.@endif
                        </div>
                        <div class="mt-3"><div class="dato">Descripción</div><div class="valor">{{ $producto->descripcion ?: 'Sin descripción.' }}</div></div>
                        <div class="row mt-3 g-3">
                            <div class="col-4"><div class="dato">Código</div><div class="valor">{{ $producto->codigo }}</div></div>
                            <div class="col-4"><div class="dato">Laboratorio</div><div class="valor">{{ $producto->laboratorio->nombre ?? '-' }}</div></div>
                        </div>
                        <a class="btn-wa-grande mt-4" target="_blank" href="https://wa.me/{{ $whatsapp }}?text={{ urlencode('Hola, quiero consultar por: ' . $producto->nombre . ' (Bs ' . number_format($producto->precio_venta, 2) . ')') }}"><i class="bi bi-whatsapp"></i> Pedir por WhatsApp</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
