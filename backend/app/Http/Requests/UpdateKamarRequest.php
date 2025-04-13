<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKamarRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan memperbarui kamar.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Aturan validasi untuk memperbarui kamar.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'harga' => 'sometimes|numeric',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'fasilitas' => 'sometimes|string',
            'status' => 'sometimes|string',
        ];
    }
}
