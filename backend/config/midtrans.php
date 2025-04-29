<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi ini akan digunakan untuk menghubungkan aplikasi Anda dengan
    | API Midtrans untuk pembayaran.
    |
    */

    'server_key' => env('MIDTRANS_SERVER_KEY'),  // Kunci server Midtrans
    'client_key' => env('MIDTRANS_CLIENT_KEY'),  // Kunci klien Midtrans
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false), // Tentukan apakah ini di mode produksi atau sandbox
    'is_sanitized' => true,  // Pengaturan keamanan sanitasi input
    'is_3ds' => true,  // Pengaturan untuk 3D Secure, menentukan apakah transaksi memerlukan autentikasi tambahan
];
