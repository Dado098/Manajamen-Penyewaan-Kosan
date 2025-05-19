<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'tgl_masuk',
        'tgl_keluar',
        'nama_kamar',
        'kamar_id',
        'penyewa_id'
    ];

    // Relasi ke Kamar
    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);  // Relasi Pembayaran ke Pemesanan
    }

    // Relasi ke Penyewa (User)
public function penyewa()
{
    return $this->belongsTo(User::class, 'penyewa_id');
}
}
