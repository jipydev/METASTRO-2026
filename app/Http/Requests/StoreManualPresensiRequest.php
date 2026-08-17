<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManualPresensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canScanPresensi() ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('jam_tap')) {
            $this->merge([
                'jam_tap' => substr((string) $this->input('jam_tap'), 0, 5),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'kegiatan_id' => ['required', 'integer', 'exists:kegiatans,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'jam_tap' => ['nullable', 'date_format:H:i'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kegiatan_id.required' => 'Kegiatan wajib dipilih.',
            'kegiatan_id.exists' => 'Kegiatan yang dipilih tidak ditemukan.',
            'user_id.required' => 'Panitia wajib dipilih.',
            'user_id.exists' => 'Panitia yang dipilih tidak ditemukan.',
            'jam_tap.date_format' => 'Format jam tap tidak valid.',
        ];
    }
}
