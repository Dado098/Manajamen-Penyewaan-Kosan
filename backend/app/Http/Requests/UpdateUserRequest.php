<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Ubah jika butuh permission
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|string',
            'username' => 'sometimes|string',
            'password' => 'sometimes|string|min:6',
            'no_telp' => 'nullable|string',
            'role' => ['sometimes', Rule::in(['penyewa', 'pemilik'])],
        ];
    }
}
