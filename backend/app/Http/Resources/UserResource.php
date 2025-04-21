<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 *
 * Resource untuk menampilkan data pengguna (user).
 *
 * @package App\Http\Resources
 */
class UserResource extends JsonResource
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
            'id' => $this->id,                          // ID pengguna
            'name' => $this->name,                      // Nama pengguna
            'username' => $this->username,              // Username pengguna
            'no_telp' => $this->no_telp,                // Nomor telepon pengguna
            'email' => $this->email,                    // Email pengguna
            'role' => $this->role,                      // Role pengguna
            'email_verified_at' => $this->email_verified_at, // Waktu verifikasi email
            'created_at' => $this->created_at,          // Waktu dibuatnya pengguna
            'updated_at' => $this->updated_at,          // Waktu terakhir diperbarui pengguna
        ];
    }
}
