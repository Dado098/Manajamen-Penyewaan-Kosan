<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class PemesananResource extends JsonResource
{
    /**
     * Transform resource menjadi array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'tgl_masuk' => Carbon::parse($this->tgl_masuk)->format('Y-m-d H:i:s'),
            'tgl_keluar' => Carbon::parse($this->tgl_keluar)->format('Y-m-d H:i:s'),
            'kamar_id' => $this->kamar_id,
            'penyewa_id' => $this->penyewa_id, // <--- Tambahan ini
            'nama_kamar' => $this->nama_kamar,
            'image' => $this->kamar ? asset('storage/' . $this->kamar->image) : null,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
        ];

    }

}
