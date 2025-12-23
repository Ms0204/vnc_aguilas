<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    UsuarioController,
    RecursoController,
    PrestamoController,
    ProductoController,
    RoleController,
    ReporteController,
    ProfileController,
    DashboardController,
};

// 🔐 Autenticación personalizada
Route::get('/', fn() => redirect()->route('login')); // Página raíz redirige al login
Route::get('/login', fn() => view('auth.login'))->name('login');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login')->with('status', 'Sesión cerrada correctamente');
})->name('logout');

require __DIR__ . '/auth.php'; // Rutas internas de autenticación

// 📊 Dashboard protegido
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// 🔎 Rutas públicas para búsqueda y validación AJAX
Route::get('/usuarios/buscar', [UsuarioController::class, 'buscar']);
Route::post('/usuarios/validar-nombre', [UsuarioController::class, 'validarNombre'])->name('usuarios.validarNombre');
Route::post('/usuarios/validar-email', [UsuarioController::class, 'validarEmail'])->name('usuarios.validarEmail');

Route::get('/recursos/buscar', [RecursoController::class, 'buscar']);
Route::post('/recursos/validar-disponibilidad', [RecursoController::class, 'verificarDisponibilidad']);
Route::post('/recursos/validar-nombre', [RecursoController::class, 'validarNombre'])->name('recursos.validarNombre');

Route::get('/prestamos/buscar', [PrestamoController::class, 'buscar']);

Route::get('/productos/buscar', [ProductoController::class, 'buscar']);
Route::post('/productos/validar-nombre', [ProductoController::class, 'validarNombre'])->name('productos.validarNombre');

Route::get('/roles/buscar', [RoleController::class, 'buscar']);

// 🔐 Áreas protegidas por autenticación y verificación
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/home', 'home')->name('home');

    // 👤 Perfil del usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 🔄 Gestión principal de entidades
    Route::resources([
        'usuarios'  => UsuarioController::class,
        'recursos'  => RecursoController::class,
        'prestamos' => PrestamoController::class,
        'productos' => ProductoController::class,
        'roles'     => RoleController::class,
    ]);

    // 📄 Reportes y descargas PDF
    Route::controller(ReporteController::class)->group(function () {
        Route::get('/reporte-productos', 'productosPDF');
        Route::get('/descargar-reporte-productos', 'descargarProductosPDF');

        Route::get('/reporte-usuarios', 'usuariosPDF');
        Route::get('/descargar-reporte-usuarios', 'descargarUsuariosPDF');

        Route::get('/reporte-prestamos', 'prestamosPDF');
        Route::get('/descargar-reporte-prestamos', 'descargarPrestamosPDF');

        Route::get('/reportes/recursos/pdf', 'recursosPDF')->name('reportes.recursos.pdf');
        Route::get('/reportes/recursos/descargar', 'descargarRecursosPDF')->name('reportes.recursos.descargar');
    });
});