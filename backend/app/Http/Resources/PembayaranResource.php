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
            'order_id' => $this->order_id, // ✅ Ditambahkan order_id
            'metode_pembayaran' => $this->metode_pembayaran,
            'total_tagihan' => $this->total_tagihan,
            'snap_token' => $this->snap_token, // ✅ Tambahan snap_token
            'penyewa' => [
                'id' => $this->penyewa_id,
                'name' => $this->penyewa->name ?? 'Nama tidak ditemukan',
                'username' => $this->penyewa->username ?? 'Username tidak ditemukan',
                'no_telp' => $this->penyewa->no_telp ?? 'Nomor telepon tidak ditemukan',
            ],
            'pemesanan_id' => $this->pemesanan_id,
            'qr_code' => $this->qr_code,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'updated_at' => $this->updated_at,
        ];
    }
}
