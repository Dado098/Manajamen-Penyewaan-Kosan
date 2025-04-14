<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @bodyParam name string required Nama lengkap.
 * @bodyParam username string required Username unik.
 * @bodyParam no_telp string Nomor telepon (opsional).
 * @bodyParam password string required Minimal 6 karakter.
 * @bodyParam role string required Role user.
 */
class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'username' => 'required|string',
            'no_telp' => 'nullable|string',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['penyewa'])],
        ];
    }
}
