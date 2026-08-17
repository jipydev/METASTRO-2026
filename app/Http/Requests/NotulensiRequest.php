<?php

namespace App\Http\Requests;

use App\Models\Notulensi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class NotulensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageSekretariat() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:150'],
            'kegiatan_id' => ['nullable', 'exists:kegiatans,id'],
            'isi' => ['nullable', 'string'],
            'lampiran' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul notulensi wajib diisi.',
            'judul.max' => 'Judul notulensi maksimal 150 karakter.',
            'kegiatan_id.exists' => 'Kegiatan yang dipilih tidak ditemukan.',
            'lampiran.mimes' => 'Lampiran harus berupa file PDF.',
            'lampiran.max' => 'Ukuran lampiran maksimal 5 MB.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var Notulensi|null $notulensi */
            $notulensi = $this->route('notulensi');
            $hasExistingLampiran = (bool) $notulensi?->lampiran;

            if (blank($this->input('isi')) && ! $this->hasFile('lampiran') && ! $hasExistingLampiran) {
                $validator->errors()->add('lampiran', 'Isi notulensi atau unggah file PDF.');
            }
        });
    }
}
