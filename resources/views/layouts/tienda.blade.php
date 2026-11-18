<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Catálogo') - Farmacia Next Level</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/nextlevel.css') }}" rel="stylesheet">
</head>
<body class="tienda-body">
<header class="tnav">
    <div class="container d-flex align-items-center gap-3 flex-wrap">
        <a href="{{ route('tienda') }}" class="marca"><span class="logo"><i class="bi bi-heart-pulse-fill"></i></span><span>FARMACIA<br>NEXT LEVEL</span></a>
        <form action="{{ route('tienda') }}" method="get" class="busca">
            <div class="input-group">
                <input name="q" id="buscaTienda" class="form-control" placeholder="Buscar medicamentos y productos..." value="{{ request('q') }}" autocomplete="off">
                <button class="btn" title="Buscar"><i class="bi bi-search"></i></button>
            </div>
            <div class="sugerencias" id="sugerencias"></div>
        </form>
        <a class="btn-wa-grande ms-auto" target="_blank" href="https://wa.me/{{ $whatsapp }}?text={{ urlencode('Hola, quiero hacer una consulta en Farmacia Next Level.') }}"><i class="bi bi-whatsapp"></i> WhatsApp {{ $whatsappVisible }}</a>
        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="btn btn-outline-light fw-bold">{{ auth()->check() ? 'Ir al sistema' : 'Acceso del personal' }}</a>
    </div>
</header>

@yield('contenido')

<footer class="text-center py-4 fw-bold" style="color:#374151">© {{ date('Y') }} Farmacia Next Level · La Paz, Bolivia · WhatsApp {{ $whatsappVisible }}</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    var campo = document.getElementById('buscaTienda');
    var caja = document.getElementById('sugerencias');
    var temporizador = null;
    campo.addEventListener('input', function () {
        clearTimeout(temporizador);
        var q = campo.value.trim();
        if (q === '') { caja.style.display = 'none'; return; }
        temporizador = setTimeout(function () {
            fetch('{{ route('tienda.sugerencias') }}?q=' + encodeURIComponent(q))
                .then(function (r) { return r.json(); })
                .then(function (lista) {
                    caja.innerHTML = '';
                    if (lista.length === 0) { caja.style.display = 'none'; return; }
                    lista.forEach(function (p) {
                        var a = document.createElement('a');
                        a.href = p.url;
                        var s = document.createElement('span'); s.textContent = p.nombre; a.appendChild(s);
                        caja.appendChild(a);
                    });
                    caja.style.display = 'block';
                });
        }, 200);
    });
    document.addEventListener('click', function (e) { if (!caja.contains(e.target) && e.target !== campo) { caja.style.display = 'none'; } });
})();
</script>
</body>
</html>
