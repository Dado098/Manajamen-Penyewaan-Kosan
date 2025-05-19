<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'pemesanan_id',
        'penyewa_id',
        'metode_pembayaran',
        'total_tagihan',
        'status',
        'qr_code',
        'snap_token',
    ];

    protected static function boot()
    {
        parent::boot();

        // Set UUID otomatis sebelum menyimpan
        static::creating(function ($pembayaran) {
            $pembayaran->qr_code = Str::uuid();
        });
    }

    public function penyewa()
    {
        return $this->belongsTo(User::class, 'penyewa_id');
    }

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'pemesanan_id');
    }
}
