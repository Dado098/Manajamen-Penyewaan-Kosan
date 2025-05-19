<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PembayaranPerpanjanganController;
use App\Http\Controllers\KosanExportController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\LocationController;




// Route untuk Manajemen User (Admin)
Route::apiResource('users', UserController::class);
Route::get('users/role/{role}', [UserController::class, 'getUsersByRole']);

Route::middleware(['auth:sanctum', 'verified'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});




//  Untuk CRUD Kamar
Route::apiResource('kamars', KamarController::class);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Untuk Export Data Kamar ke GeoJSON
Route::get('kamars/export', [KosanExportController::class, 'export']);

// Route Untuk CRUD Pemesanan
Route::apiResource('pemesanans', PemesananController::class);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/pemesanans', [PemesananController::class, 'store']);
});


// Route untuk Pembayaran
Route::apiResource('pembayarans', PembayaranController::class);
Route::middleware('auth:sanctum')->get('/pembayarans', [PembayaranController::class, 'index']);


// Route untuk Pembayaran berdasarkan ID Pemesanan (QRCode)
Route::get('pembayarans/{id}/qrcode', [PembayaranController::class, 'generateQRCode'])->name('pembayarans.qrcode');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route untuk Authentication
Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
});



// Verifikasi email (gunakan middleware signed agar hash bisa divalidasi Laravel)
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed'])
    ->name('verification.verify');

// Tes kirim email
Route::get('/tes-email', [EmailController::class, 'sendTestEmail']);

// Contoh akses user yang sudah login & sudah verifikasi email
Route::middleware(['auth:sanctum', 'verified'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Link verifikasi dikirim ulang.']);
})->middleware(['auth:sanctum', 'throttle:6,1']);



Route::post('midtrans/notification', [PembayaranController::class, 'notification']);


Route::post('midtrans/notification', [PembayaranController::class, 'notification'])->withoutMiddleware(['auth:sanctum']);


Route::get('locations', [LocationController::class, 'getLocations']);




