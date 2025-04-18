<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PembayaranResource extends JsonResource
{
    /**
     * Transformasikan sumber daya menjadi array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'metode_pembayaran' => $this->metode_pembayaran,
            'total_tagihan' => $this->total_tagihan,
            'penyewa' => [
                'id' => $this->penyewa_id, // ID penyewa
                'name' => $this->penyewa->name ?? 'Nama tidak ditemukan', // Menampilkan nama penyewa
                'username' => $this->penyewa->username ?? 'Username tidak ditemukan', // Menampilkan username penyewa
                'no_telp' => $this->penyewa->no_telp ?? 'Nomor telepon tidak ditemukan', // Menampilkan nomor telepon penyewa
            ],
            'pemesanan_id' => $this->pemesanan_id,
            'qr_code' => $this->qr_code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
