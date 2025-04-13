<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePembayaranRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan membuat pembayaran.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Aturan validasi untuk menyimpan pembayaran baru.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'metode_pembayaran' => 'required|string|max:255',
            'total_tagihan' => 'required|numeric|min:0',
            'penyewa_id' => 'required|exists:users,id',
            'pemesanan_id' => 'required|exists:pemesanans,id',
        ];
    }
}
