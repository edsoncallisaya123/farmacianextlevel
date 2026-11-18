<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión - Farmacia Next Level</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/nextlevel.css') }}" rel="stylesheet">
</head>
<body>
<div class="lg-wrap">
    <div class="lg-izq">
        <div class="ilus">@include('partials.ilustracion_farmacia')</div>
        <h2>Farmacia Next Level</h2>
        <p>Controla tu inventario, tus ventas y tu caja en un solo lugar, de forma rápida y segura.</p>
    </div>

    <div class="lg-der">
        <div class="lg-form">
            <div class="marca"><span class="logo"><i class="bi bi-heart-pulse-fill"></i></span> FARMACIA NEXT LEVEL</div>
            <h1>Iniciar sesión</h1>
            <div class="sub">Ingresa tu correo y tu contraseña para entrar al sistema.</div>

            @if($errors->any())
                <div class="alert alert-danger fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> {{ $errors->first() }}</div>
            @endif

            <form method="post" action="{{ url('/login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com" value="{{ old('email') }}" required autofocus>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="password" id="clave" class="form-control" placeholder="Tu contraseña" required>
                        <button class="btn btn-ver" type="button" id="verClave" title="Mostrar u ocultar"><i class="bi bi-eye-fill"></i></button>
                    </div>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="recordar" id="recordar" value="1">
                    <label class="form-check-label fw-bold" for="recordar">Recordarme en este equipo</label>
                </div>
                <button class="btn btn-entrar"><i class="bi bi-box-arrow-in-right"></i> Entrar al sistema</button>
            </form>

            <div class="text-center mt-4">
                <a href="{{ route('tienda') }}" class="fw-bold" style="color:var(--nl-orange-dark)"><i class="bi bi-shop"></i> Ver catálogo de productos</a>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('verClave').addEventListener('click', function () {
    var c = document.getElementById('clave');
    c.type = c.type === 'password' ? 'text' : 'password';
});
</script>
</body>
</html>
