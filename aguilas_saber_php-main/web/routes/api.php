<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controladores para productos
use App\Http\Controllers\Api\ProductoApiController;
use App\Http\Controllers\ReporteController;

// Controladores para demás módulos
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PrestamoController;
use App\Http\Controllers\Api\RecursoController;
use App\Http\Controllers\Api\LoginController;

// Rutas de la API
Route::post('/login', [LoginController::class, 'login']);

//
// 📦 Productos
//
Route::apiResource('productos', ProductoApiController::class);
Route::get('/reporte-productos', [ReporteController::class, 'productosPDF']);

//
// 👤 Usuarios
//
Route::apiResource('usuarios', UsuarioController::class);

//
// 🔐 Roles
//
Route::apiResource('roles', RoleController::class);

//
// 📚 Préstamos
//
Route::apiResource('prestamos', PrestamoController::class);

//
// 🧰 Recursos
//
Route::apiResource('recursos', RecursoController::class);
