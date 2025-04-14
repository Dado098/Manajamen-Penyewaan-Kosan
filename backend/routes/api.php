<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\KosanExportController;
use App\Http\Controllers\AuthController;

// Route untuk Manajemen User (Admin)
Route::apiResource('users', UserController::class);
Route::get('users/role/{role}', [UserController::class, 'getUsersByRole']);

//  Untuk CRUD Kamar
Route::apiResource('kamars', KamarController::class);

// Untuk Export Data Kamar ke GeoJSON
Route::get('kamars/export', [KosanExportController::class, 'export']);

Route::apiResource('pemesanans', PemesananController::class);
Route::apiResource('pembayarans', PembayaranController::class);
// Endpoint untuk generate QR Code
Route::get('pembayarans/{id}/qrcode', [PembayaranController::class, 'generateQRCode'])->name('pembayarans.qrcode');


// Route untuk Authentication
Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
});
