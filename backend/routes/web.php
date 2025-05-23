<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email/verified/success', function () {
    return view('auth.email-verified');
})->name('email.verified.success');

Route::get('/reset-password', function () {
    return redirect()->away('http://localhost:5500/frontend-user/html/lupa-password.html?' . http_build_query([
        'token' => request('token'),
        'email' => request('email'),
    ]));
})->name('password.reset');
