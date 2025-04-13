<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\PembayaranController;

Route::apiResource('users', UserController::class);
Route::get('users/role/{role}', [UserController::class, 'getUsersByRole']);
Route::apiResource('kamars', KamarController::class);
Route::apiResource('pemesanans', PemesananController::class);
Route::apiResource('pembayarans', PembayaranController::class);
// Endpoint untuk generate QR Code
Route::get('pembayarans/{id}/qrcode', [PembayaranController::class, 'generateQRCode'])->name('pembayarans.qrcode');


