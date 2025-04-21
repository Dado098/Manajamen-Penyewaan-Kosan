<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username' => 'required|string',
            'password' => 'required|string',
        ];
    }

    protected function passedValidation()
    {
        $user = User::where('username', $this->username)->first();

        if (!$user || !Hash::check($this->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        // Jika role adalah penyewa, periksa verifikasi email
        if ($user->role === 'penyewa' && !$user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => ['Email belum diverifikasi.'],
            ]);
        }

        // Menyimpan data user untuk digunakan di controller
        $this->merge(['user' => $user]);
    }
}

