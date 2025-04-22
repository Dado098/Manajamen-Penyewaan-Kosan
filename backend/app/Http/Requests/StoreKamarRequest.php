<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKamarRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan membuat kamar.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Aturan validasi untuk membuat kamar.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'harga' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'fasilitas' => 'required|string',
            'status' => 'nullable|string',
            'luas' => 'required|string', // validasi baru untuk luas
        ];

    }
}
