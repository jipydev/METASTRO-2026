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
            'tugas_file' => ['required', 'file', 'mimes:pdf', 'max:10240'], // Wajib PDF, maks 10MB
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tugas_file.required' => 'File tugas PDF wajib diunggah.',
            'tugas_file.file' => 'File yang diunggah tidak valid.',
            'tugas_file.mimes' => 'Format file tugas harus berupa PDF.',
            'tugas_file.max' => 'Ukuran file PDF maksimal 10MB.',
        ];
    }
}
