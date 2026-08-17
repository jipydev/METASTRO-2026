<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengumumanRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        if ($this->isMethod('post')) {
            return $user->canCreatePengumuman();
        }

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string'],
            'tanggal_publish' => ['nullable', 'date'],
            'status' => ['required', 'in:draft,published'],
            'lampiran' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul pengumuman wajib diisi.',
            'judul.max' => 'Judul pengumuman maksimal 255 karakter.',
            'isi.required' => 'Isi pengumuman wajib diisi.',
            'tanggal_publish.date' => 'Tanggal publikasi tidak valid.',
            'status.required' => 'Status pengumuman wajib dipilih.',
            'status.in' => 'Status pengumuman harus Draft atau Publish.',
            'lampiran.mimes' => 'Lampiran harus berupa file PDF.',
            'lampiran.max' => 'Ukuran lampiran maksimal 5 MB.',
        ];
    }
}
