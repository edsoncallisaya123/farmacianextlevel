@extends('layouts.tienda')
@section('titulo', 'Catálogo')

@section('contenido')
<div class="t-banner">
    <div class="container">
        <h1><i class="bi bi-capsule-pill"></i> Catálogo de productos</h1>
        <p>Medicamentos y cuidado personal. Consulta precios y disponibilidad, y pide por WhatsApp.</p>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-3">@include('partials.tienda_sidebar')</div>

        <div class="col-lg-9">
            <h2 class="h4 fw-bold mb-3" style="color:var(--nl-orange-dark)">
                @if($busqueda !== '') Resultados para "{{ $busqueda }}"
                @elseif($categoriaSel) {{ $categoriaSel->nombre }}
                @else Todos los productos @endif
                <span style="color:#111827">({{ $productos->count() }})</span>
            </h2>
            <div class="row g-3">
                @forelse($productos as $p)
                    @php $disp = $p->stock; @endphp
                    <div class="col-sm-6 col-xl-4">
                        <div class="t-card">
                            <a href="{{ route('tienda.producto', $p->id) }}" class="foto">@include('partials.producto_imagen', ['p' => $p])</a>
                            <div class="cuerpo">
                                <div class="cat">{{ $p->categoria->nombre ?? 'Sin categoría' }}</div>
                                <a href="{{ route('tienda.producto', $p->id) }}" class="nom">{{ $p->nombre }}</a>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="precio">Bs {{ number_format($p->precio_venta, 2) }}</span>
                                    <span class="badge-stock {{ $disp <= 0 ? 'no' : ($disp < $p->stock_minimo ? 'poco' : 'ok') }}">{{ $disp <= 0 ? 'Agotado' : ($disp < $p->stock_minimo ? 'Stock limitado' : 'Disponible') }}</span>
                                </div>
                                <div class="btns">
                                    <a class="btn-detalle" href="{{ route('tienda.producto', $p->id) }}">Ver detalle</a>
                                    <a class="btn-pedir" target="_blank" href="https://wa.me/{{ $whatsapp }}?text={{ urlencode('Hola, quiero consultar por: ' . $p->nombre . ' (Bs ' . number_format($p->precio_venta, 2) . ')') }}"><i class="bi bi-whatsapp"></i> Pedir</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12"><div class="alert alert-warning fw-bold">No se encontraron productos con esa búsqueda.</div></div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
