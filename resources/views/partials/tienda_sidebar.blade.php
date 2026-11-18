<div class="t-side">
    <div class="cab"><i class="bi bi-hospital-fill"></i> FARMACIA NEXT LEVEL</div>
    <div class="sub">Categorías</div>
    <a class="t-cat {{ empty($categoriaSel) ? 'activa' : '' }}" href="{{ route('tienda') }}"><span>Todos los productos</span></a>
    @foreach($categorias as $c)
        <a class="t-cat {{ !empty($categoriaSel) && $categoriaSel->id === $c->id ? 'activa' : '' }}" href="{{ route('tienda', ['categoria' => $c->id]) }}">
            <span>{{ $c->nombre }}</span><span class="n">{{ $c->productos_count ?? '' }}</span>
        </a>
    @endforeach
    <div class="t-contacto">
        <b><i class="bi bi-whatsapp"></i> Pedidos por WhatsApp</b>
        <a class="btn-wa-grande" target="_blank" href="https://wa.me/{{ $whatsapp }}?text={{ urlencode('Hola, quiero hacer un pedido en Farmacia Next Level.') }}"><i class="bi bi-whatsapp"></i> {{ $whatsappVisible }}</a>
    </div>
</div>
