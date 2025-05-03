<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\DiskonController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuDiskonController;
use App\Models\Transaksi;
use App\Http\Controllers\TransaksiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(authController::class)->group(function () {
    Route::post('/siswa/register', 'registerSiswa');
    Route::post('/siswa/login', 'loginSiswa');
    Route::put('/siswa/update/{id}', 'updateStan');
    Route::delete('/siswa/delete/{id}', 'deleteSiswa');
    Route::post('/logout', 'logout');
    Route::post('/reset-password/{id_user}', 'resetPassword');
});

Route::controller(authController::class)->group(function () {
    Route::post('/stan/register', 'registerStan');
    Route::post('/stan/login', 'loginStan');
    Route::put('/stan/update/{id}', 'updateStan');
    Route::delete('/stan/delete/{id}', 'deleteStan');
    Route::get('/menus/stan/{id_stan}   ', [MenuController::class, 'getmenuStan']);
});

Route::controller(MenuController::class)->group(function () {
    Route::post('/menu/register', 'createMenu');
    Route::post('/menu/update/{id}', 'updateMenu');
    Route::delete('/menu/{id}', 'deleteMenu');
    Route::get('/menu/{id}', 'getMenu');
});

Route::controller(DiskonController::class)->group(function () {
    Route::post('/diskon/create', 'createDiskon');
    Route::get('/diskon/{id}', 'getDiskon');
});

Route::controller(MenuDiskonController::class)->group(function () {
    Route::post('/menu-diskon', 'store');
    Route::delete('/menu-diskon/{menuId}/{diskonId}', 'destroy');
});

Route::controller(TransaksiController::class)->group(function () {
    Route::post('/transaksi', 'store');
    Route::get('/transaksi/status/{siswa_id}', 'statusBySiswa');
    Route::put('/transaksi/{id}/status', 'updateStatus');
});
