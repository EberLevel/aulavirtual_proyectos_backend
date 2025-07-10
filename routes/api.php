<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas para reset de contraseña
Route::post('/password/verify-token', 'App\Http\Controllers\PasswordResetController@verifyToken');
Route::post('/password/reset', 'App\Http\Controllers\PasswordResetController@resetPassword');

// Rutas para cambio de contraseña inicial
Route::middleware('auth:api')->group(function () {
    Route::post('/password/change-initial', 'App\Http\Controllers\PasswordChangeController@changeInitialPassword');
    Route::get('/password/status', 'App\Http\Controllers\PasswordChangeController@checkPasswordStatus');
});

// Rutas públicas para el frontend (sin autenticación)
// Movidas a web.php para coincidir con la configuración del frontend
