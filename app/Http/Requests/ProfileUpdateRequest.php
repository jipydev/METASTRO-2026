<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'nim' => [
                'required',
                'string',
                'max:20',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.max' => 'Nama lengkap maksimal 255 karakter.',
            'nim.required' => 'NIM wajib diisi.',
            'nim.max' => 'NIM terlalu panjang. Maksimal 20 karakter.',
            'nim.unique' => 'NIM ini sudah dipakai akun lain. Gunakan NIM yang berbeda.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid. Contoh: nama@email.com',
            'email.unique' => 'Email ini sudah terdaftar. Gunakan email lain.',
            'foto.image' => 'Foto profil harus berupa gambar.',
            'foto.mimes' => 'Foto profil harus berformat JPG, JPEG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto profil maksimal 2 MB.',
        ];
    }
}
