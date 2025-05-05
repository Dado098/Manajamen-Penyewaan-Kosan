<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePemesananRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Ubah sesuai kebutuhan autentikasi
    }

    public function rules()
{
    return [
        'tgl_masuk'  => 'nullable|date',
        'tgl_keluar' => 'nullable|date|after:tgl_masuk',
        'kamar_id'   => 'required|exists:kamars,id',
        'penyewa_id' => 'sometimes|exists:users,id',
        'nama_kamar' => 'nullable|string',
        'status'     => 'nullable|string',
    ];

}

}
