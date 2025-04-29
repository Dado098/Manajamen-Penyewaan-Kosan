<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StorePembayaranRequest
 *
 * Request untuk menyimpan pembayaran dengan validasi yang diperlukan.
 *
 * @package App\Http\Requests
 */
class StorePembayaranRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna dapat membuat permintaan ini.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Ubah sesuai kontrol akses yang diperlukan
    }

    /**
     * Dapatkan aturan validasi untuk permintaan ini.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'metode_pembayaran' => 'required|string|max:255',
            'total_tagihan' => 'required|numeric|min:1',
            'penyewa_id' => 'required|exists:users,id',
            'pemesanan_id' => 'required|exists:pemesanans,id',
            'qr_code' => 'nullable|string|max:255',
            'snap_token' => 'nullable|string|max:255', // ✅ Tambahan validasi snap_token
        ];
    }

    /**
     * Pesan validasi kustom.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'metode_pembayaran.required' => 'Metode pembayaran harus diisi.',
            'total_tagihan.required' => 'Total tagihan harus diisi.',
            'penyewa_id.required' => 'Penyewa ID harus diisi.',
            'pemesanan_id.required' => 'Pemesanan ID harus diisi.',
            'qr_code.string' => 'QR code harus berupa string.',
            'snap_token.string' => 'Snap token harus berupa string.', // ✅ Pesan validasi snap_token
        ];
    }
}
