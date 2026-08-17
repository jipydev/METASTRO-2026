<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ImportPresensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canScanPresensi() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'kegiatan_id' => ['required', 'integer', 'exists:kegiatans,id'],
            'file' => ['required', 'file', 'max:2048'],
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
            'file.required' => 'Berkas impor wajib diunggah.',
            'file.file' => 'Berkas impor tidak valid.',
            'file.max' => 'Ukuran berkas maksimal 2 MB.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $file = $this->file('file');
            if (! $file) {
                return;
            }

            $extension = strtolower($file->getClientOriginalExtension());
            if (! in_array($extension, ['csv', 'txt', 'xlsx', 'xls'], true)) {
                $validator->errors()->add('file', 'Berkas harus berupa CSV atau Excel (.xlsx, .xls).');
            }
        });
    }
}
