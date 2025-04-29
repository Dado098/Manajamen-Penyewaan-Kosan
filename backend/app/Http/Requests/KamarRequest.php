<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request validasi untuk data Kamar.
 */
class KamarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Bolehkan semua request
    }

    public function rules(): array
    {
        return [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'luas' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'image' => 'required|string|max:255',
            'fasilitas' => 'required|string|max:255',
            'status' => 'nullable|string|in:tersedia,terisi',
        ];
    }
}
