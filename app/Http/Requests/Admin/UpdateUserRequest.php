<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends UserFormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => $this->filled('email') ? $this->email : null,
            'divisi_id' => $this->filled('divisi_id') ? $this->divisi_id : null,
            'jabatan_id' => $this->filled('jabatan_id') ? $this->jabatan_id : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        return [
            'nama' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:20', Rule::unique(User::class, 'nim')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($user->id)],
            'role' => ['required', 'exists:roles,name'],
            'divisi_id' => ['nullable', 'exists:divisis,id'],
            'jabatan_id' => ['nullable', 'exists:jabatans,id', $this->jabatanDivisiRule()],
            'status' => ['required', 'in:0,1'],
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
            'email.email' => 'Format email tidak valid. Contoh: nama@email.com',
            'email.unique' => 'Email ini sudah terdaftar. Gunakan email lain.',
            'role.required' => 'Role wajib dipilih.',
            'role.exists' => 'Role yang dipilih tidak valid.',
            'divisi_id.exists' => 'Divisi yang dipilih tidak valid.',
            'jabatan_id.exists' => 'Jabatan yang dipilih tidak valid.',
            'status.required' => 'Status akun wajib dipilih.',
            'status.in' => 'Status akun harus Aktif atau Nonaktif.',
        ];
    }
}
