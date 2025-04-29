<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource untuk format output Kamar.
 */
class KamarResource extends JsonResource
{
    /**
     * Transform resource menjadi array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'luas' => $this->luas,
            'harga' => $this->harga,
            'image' => $this->image,
            'fasilitas' => $this->fasilitas,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
