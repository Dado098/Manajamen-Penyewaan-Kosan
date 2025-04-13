<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Ubah jika butuh permission
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'username' => 'required|string',
            'no_telp' => 'nullable|string',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['penyewa', 'pemilik'])],
        ];
    }
}
