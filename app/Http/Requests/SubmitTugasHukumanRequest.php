<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitTugasHukumanRequest extends FormRequest
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
        return [
            'tugas_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'], // Opsional (boleh kosong), jika diisi harus PDF maks 10MB
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tugas_file.file' => 'File yang diunggah tidak valid.',
            'tugas_file.mimes' => 'Format file tugas harus berupa PDF.',
            'tugas_file.max' => 'Ukuran file PDF maksimal 10MB.',
        ];
    }
}
