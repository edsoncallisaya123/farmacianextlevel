<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\LaboratorioController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VentaController;
use App\Http\Middleware\VerificarPermiso;
use Illuminate\Support\Facades\Route;

// ---------- Página web pública (catálogo) ----------
Route::get('/', [TiendaController::class, 'index'])->name('tienda');
Route::get('/catalogo/producto/{producto}', [TiendaController::class, 'show'])->name('tienda.producto');
Route::get('/catalogo/sugerencias', [TiendaController::class, 'sugerencias'])->name('tienda.sugerencias');

// ---------- Autenticación ----------
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ---------- Sistema (requiere sesión) ----------
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::get('/perfil/password', [PerfilController::class, 'password'])->name('perfil.password');
    Route::put('/perfil/password', [PerfilController::class, 'updatePassword'])->name('perfil.password.update');

    // Usuarios, roles y permisos
    Route::middleware(VerificarPermiso::class . ':usuarios')->group(function () {
        Route::resource('usuarios', UsuarioController::class)->except(['show']);
    });
    Route::middleware(VerificarPermiso::class . ':roles')->group(function () {
        Route::resource('roles', RolController::class)->except(['show'])->parameters(['roles' => 'rol']);
    });
    Route::middleware(VerificarPermiso::class . ':permisos')->group(function () {
        Route::get('permisos', [PermisoController::class, 'index'])->name('permisos.index');
        Route::post('permisos', [PermisoController::class, 'store'])->name('permisos.store');
        Route::put('permisos/{permiso}', [PermisoController::class, 'update'])->name('permisos.update');
        Route::delete('permisos/{permiso}', [PermisoController::class, 'destroy'])->name('permisos.destroy');
    });

    // Parametrización e inventario
    Route::middleware(VerificarPermiso::class . ':categorias')->group(function () {
        Route::resource('categorias', CategoriaController::class)->only(['index', 'store', 'update', 'destroy']);
    });
    Route::middleware(VerificarPermiso::class . ':laboratorios')->group(function () {
        Route::resource('laboratorios', LaboratorioController::class)->only(['index', 'store', 'update', 'destroy']);
    });
    Route::middleware(VerificarPermiso::class . ':productos')->group(function () {
        Route::get('productos/buscar-codigo', [ProductoController::class, 'buscarCodigo'])->name('productos.buscar');
        Route::resource('productos', ProductoController::class)->except(['show']);
    });
    Route::middleware(VerificarPermiso::class . ':lotes')->group(function () {
        Route::get('lotes', [LoteController::class, 'index'])->name('lotes.index');
        Route::post('lotes', [LoteController::class, 'store'])->name('lotes.store');
        Route::patch('lotes/{lote}/baja', [LoteController::class, 'baja'])->name('lotes.baja');
    });
    Route::middleware(VerificarPermiso::class . ':clientes')->group(function () {
        Route::resource('clientes', ClienteController::class)->only(['index', 'store', 'update', 'destroy']);
    });
    Route::get('inventario', [InventarioController::class, 'index'])->name('inventario.index')
        ->middleware(VerificarPermiso::class . ':inventario');

    // Operaciones
    Route::middleware(VerificarPermiso::class . ':compras')->group(function () {
        Route::resource('compras', CompraController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    });
    Route::middleware(VerificarPermiso::class . ':ventas')->group(function () {
        Route::resource('ventas', VentaController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    });
    Route::middleware(VerificarPermiso::class . ':caja')->group(function () {
        Route::post('cajas/{caja}/movimiento', [CajaController::class, 'movimiento'])->name('cajas.movimiento');
        Route::post('cajas/{caja}/cerrar', [CajaController::class, 'cerrar'])->name('cajas.cerrar');
        Route::resource('cajas', CajaController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    });

    // Reportes
    Route::middleware(VerificarPermiso::class . ':reportes')->group(function () {
        Route::get('reportes/ingresos', [ReporteController::class, 'ingresos'])->name('reportes.ingresos');
        Route::get('reportes/egresos', [ReporteController::class, 'egresos'])->name('reportes.egresos');
        Route::get('reportes/inventario', [ReporteController::class, 'inventario'])->name('reportes.inventario');
    });
});
