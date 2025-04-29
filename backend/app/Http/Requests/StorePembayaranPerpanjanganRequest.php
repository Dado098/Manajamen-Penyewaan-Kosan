<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request untuk validasi input saat membuat pembayaran perpanjangan.
 */
class StorePembayaranPerpanjanganRequest extends FormRequest
{
    /**
     * Menentukan apakah user berhak melakukan request ini.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Aturan validasi untuk pembayaran perpanjangan.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'metode_pembayaran' => 'required|string|max:50',
            'total_tagihan'     => 'required|numeric|min:0',
            'pemesanan_id'      => 'required|exists:pemesanans,id',
        ];
    }
}
