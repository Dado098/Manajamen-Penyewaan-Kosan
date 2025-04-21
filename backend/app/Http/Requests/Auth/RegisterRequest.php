<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RegisterRequest
 *
 * Request untuk validasi data registrasi user baru.
 *
 * @package App\Http\Requests\Auth
 */
class RegisterRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna dapat membuat permintaan ini.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Dapatkan aturan validasi untuk permintaan ini.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',       // Validasi nama
            'username' => 'required|string|max:255|unique:users', // Validasi username
            'email' => 'required|email|unique:users,email', // Validasi email
            'no_telp' => 'nullable|string|max:15',      // Validasi nomor telepon (opsional)
            'password' => 'required|string|min:8|confirmed', // Validasi password (dengan konfirmasi)
            'role' => 'required|string|in:admin,penyewa', // Validasi role
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
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ];
    }
}
