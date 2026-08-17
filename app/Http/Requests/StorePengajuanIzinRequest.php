<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePengajuanIzinRequest extends FormRequest
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
            'kegiatan_id' => ['required', 'exists:kegiatans,id'],
            'jenis_izin' => ['required', 'in:sakit,izin'],
            'alasan' => ['required', 'string', 'max:1000'],
            'surat_izin' => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'bukti' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
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
            'jenis_izin.required' => 'Jenis izin wajib dipilih.',
            'jenis_izin.in' => 'Jenis izin harus Sakit atau Izin.',
            'alasan.required' => 'Alasan izin wajib diisi.',
            'alasan.max' => 'Alasan terlalu panjang. Maksimal 1000 karakter.',
            'surat_izin.required' => 'Surat izin wajib diunggah.',
            'surat_izin.file' => 'Surat izin harus berupa file.',
            'surat_izin.mimes' => 'Surat izin harus berupa file PDF.',
            'surat_izin.max' => 'Ukuran surat izin maksimal 5 MB.',
            'bukti.mimes' => 'Bukti dokumentasi harus berupa JPG atau PNG.',
            'bukti.max' => 'Ukuran bukti dokumentasi maksimal 5 MB.',
        ];
    }
}
