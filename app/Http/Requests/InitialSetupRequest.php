<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class InitialSetupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->user();

        $rules = [
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'nomor_hp' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];

        $rules['foto'] = $user->isPeserta()
            ? ['nullable']
            : ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.max' => 'Nama lengkap maksimal 100 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid. Contoh: nama@email.com',
            'email.unique' => 'Email ini sudah terdaftar. Gunakan email lain.',
            'nomor_hp.required' => 'Nomor HP wajib diisi.',
            'nomor_hp.max' => 'Nomor HP maksimal 20 karakter.',
            'foto.required' => 'Foto profil wajib diunggah.',
            'foto.image' => 'Foto profil harus berupa gambar.',
            'foto.mimes' => 'Foto profil harus berformat JPG, JPEG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto profil maksimal 5 MB.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Ulangi password belum sama. Pastikan keduanya sama.',
            'password.min' => 'Password minimal :min karakter.',
        ];
    }
}
