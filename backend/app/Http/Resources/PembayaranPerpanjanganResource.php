<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource untuk format API pembayaran perpanjangan.
 */
class PembayaranPerpanjanganResource extends JsonResource
{
    /**
     * Transformasi resource menjadi array untuk response API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                 => $this->id,
            'metode_pembayaran'  => $this->metode_pembayaran,
            'total_tagihan'      => $this->total_tagihan,
            'status'             => $this->status,
            'pemesanan_id'       => $this->pemesanan_id,
            'qr_code'            => $this->qr_code,
            'snap_token'         => $this->snap_token,
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
        ];
    }
}
