<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name', 'username', 'no_telp', 'password', 'role', 'email',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Memastikan pengguna menerima email verifikasi setelah registrasi.
     */
    public static function booted()
    {
        static::created(function ($user) {
            // Tidak perlu memanggil sendEmailVerificationNotification secara manual jika sudah mengimplementasikan MustVerifyEmail
            $user->sendEmailVerificationNotification();
        });
    }

    /**
     * Kirim notifikasi verifikasi email.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }
}
