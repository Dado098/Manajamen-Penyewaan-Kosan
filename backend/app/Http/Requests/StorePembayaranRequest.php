<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request untuk validasi data pembuatan pembayaran.
 */
class StorePembayaranRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan untuk membuat permintaan ini.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Ubah jika menggunakan otorisasi berbasis policy/gate
    }

    /**
     * Aturan validasi untuk permintaan ini.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'metode_pembayaran' => 'required|string|max:255',
            'total_tagihan'     => 'required|numeric|min:1',
            'penyewa_id'        => 'required|exists:users,id',
            'pemesanan_id'      => 'nullable|exists:pemesanans,id',
        ];

    }

    /**
     * Pesan kustom untuk kesalahan validasi.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'metode_pembayaran.required' => 'Metode pembayaran harus diisi.',
            'metode_pembayaran.string'   => 'Metode pembayaran harus berupa teks.',
            'total_tagihan.required'     => 'Total tagihan harus diisi.',
            'total_tagihan.numeric'      => 'Total tagihan harus berupa angka.',
            'penyewa_id.required'        => 'ID penyewa harus diisi.',
            'penyewa_id.exists'          => 'ID penyewa tidak ditemukan.',
            'pemesanan_id.exists'        => 'ID pemesanan tidak ditemukan.', // hanya validasi exists
        ];
    }

}
