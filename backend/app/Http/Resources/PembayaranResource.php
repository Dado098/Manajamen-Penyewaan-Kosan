<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PembayaranResource extends JsonResource
{
    /**
     * Transformasi sumber daya menjadi array JSON.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'order_id'          => $this->order_id,
            'metode_pembayaran' => $this->metode_pembayaran,
            'total_tagihan'     => $this->total_tagihan,
            'snap_token'        => $this->snap_token,
            'qr_code'           => $this->qr_code,
            'status'            => $this->status,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,

            // Relasi ke penyewa
            'penyewa' => [
                'id'       => $this->penyewa_id,
                'name'     => optional($this->penyewa)->name ?? null,
                'username' => optional($this->penyewa)->username ?? null,
                'no_telp'  => optional($this->penyewa)->no_telp ?? null,
            ],

            // Relasi ke pemesanan
            'pemesanan_id' => $this->pemesanan_id,
        ];
    }
}
