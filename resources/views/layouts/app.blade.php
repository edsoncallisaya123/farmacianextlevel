<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Inicio') - Farmacia Next Level</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/nextlevel.css') }}" rel="stylesheet">
    @stack('estilos')
</head>
<body>
@php
    $u = auth()->user();
    $grupos = [
        ['titulo' => null, 'icono' => null, 'items' => [
            ['Inicio', 'dashboard', null, 'dashboard'],
            ['Actualizar contraseña', 'perfil.password', null, 'perfil/password'],
            ['Perfil', 'perfil', null, 'perfil'],
        ]],
        ['titulo' => 'Administración de usuarios', 'icono' => 'bi-people-fill', 'items' => [
            ['Usuarios', 'usuarios.index', 'usuarios', 'usuarios*'],
        ]],
        ['titulo' => 'Roles y permisos', 'icono' => 'bi-person-gear', 'items' => [
            ['Gestión de Roles', 'roles.index', 'roles', 'roles*'],
            ['Gestión de Permisos', 'permisos.index', 'permisos', 'permisos*'],
        ]],
        ['titulo' => 'Administración y parametrización', 'icono' => 'bi-gear-wide-connected', 'items' => [
            ['Catálogo del Sistema', 'tienda', null, '__tienda'],
            ['Categorías', 'categorias.index', 'categorias', 'categorias*'],
        ]],
        ['titulo' => 'Inventario', 'icono' => 'bi-diagram-3-fill', 'items' => [
            ['Laboratorio', 'laboratorios.index', 'laboratorios', 'laboratorios*'],
            ['Productos', 'productos.index', 'productos', 'productos*'],
            ['Gestionar Lotes', 'lotes.index', 'lotes', 'lotes*'],
            ['Clientes', 'clientes.index', 'clientes', 'clientes*'],
            ['Inventarios', 'inventario.index', 'inventario', 'inventario*'],
        ]],
        ['titulo' => 'Operaciones', 'icono' => 'bi-gear-fill', 'items' => [
            ['Compras', 'compras.index', 'compras', 'compras*'],
            ['Ventas', 'ventas.index', 'ventas', 'ventas*'],
            ['Caja', 'cajas.index', 'caja', 'cajas*'],
        ]],
        ['titulo' => 'Reportes', 'icono' => 'bi-bar-chart-line-fill', 'items' => [
            ['Reporte de Ingresos', 'reportes.ingresos', 'reportes', 'reportes/ingresos'],
            ['Reporte de Egresos', 'reportes.egresos', 'reportes', 'reportes/egresos'],
            ['Reporte de Inventario', 'reportes.inventario', 'reportes', 'reportes/inventario'],
        ]],
    ];
@endphp
<div class="nl-band"></div>
<button class="btn-menu-movil" id="btnMenu" type="button"><i class="bi bi-list fs-4"></i></button>

<aside class="nl-sidebar" id="sidebar">
    <div class="nl-marca"><i class="bi bi-heart-pulse-fill"></i> FARMACIA NEXT LEVEL</div>
    <div class="nl-perfil">
        <div class="nl-avatar">
            @if($u->foto_url)
                <img src="{{ $u->foto_url }}" alt="Foto">
            @else
                <i class="bi bi-person-fill"></i>
            @endif
        </div>
        <div class="nombre">{{ $u->nombre_completo }}</div>
        <div class="rol">{{ $u->rol->nombre ?? 'sin rol' }}</div>
    </div>

    @foreach($grupos as $g)
        @php
            $visibles = collect($g['items'])->filter(fn($i) => $i[2] === null || $u->tienePermiso($i[2]));
        @endphp
        @if($visibles->isNotEmpty())
            @if($g['titulo'])
                <div class="nl-grupo"><i class="bi {{ $g['icono'] }}"></i> {{ $g['titulo'] }}</div>
            @endif
            @foreach($visibles as $i)
                @php
                    $activo = $i[3] !== '__tienda' && request()->is($i[3]);
                @endphp
                <a class="nl-link {{ $g['titulo'] ? '' : 'suelto' }} {{ $activo ? 'active' : '' }}"
                   href="{{ route($i[1]) }}" @if($i[3] === '__tienda') target="_blank" @endif>{{ $i[0] }}</a>
            @endforeach
        @endif
    @endforeach

    <form method="post" action="{{ route('logout') }}" class="px-3 mt-3">
        @csrf
        <button class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</button>
    </form>
</aside>

<main class="nl-main">
    <div class="nl-migas">
        <a href="{{ route('dashboard') }}">Inicio</a>
        @hasSection('migas')
            / <span class="actual">@yield('migas')</span>
        @endif
    </div>

    @if(session('ok'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('ok') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('contenido')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/nextlevel.js') }}"></script>
@stack('scripts')
</body>
</html>
