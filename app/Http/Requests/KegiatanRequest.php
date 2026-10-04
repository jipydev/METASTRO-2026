<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class KegiatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canManageKegiatan() ?? false;
    }

    protected function prepareForValidation(): void
    {
        foreach (['waktu_mulai', 'waktu_selesai'] as $field) {
            if ($this->filled($field)) {
                $this->merge([
                    $field => substr((string) $this->input($field), 0, 5),
                ]);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'jenis' => ['required', 'in:rapat,pelaksanaan'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'waktu_mulai' => ['required', 'date_format:H:i'],
            'waktu_selesai' => ['nullable', 'date_format:H:i'],
            'tempat' => ['required', 'string', 'max:255'],
            'presensi_mulai' => ['nullable', 'date'],
            'presensi_selesai' => ['nullable', 'date', 'after_or_equal:presensi_mulai'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->filled('tanggal_mulai') || ! $this->filled('tanggal_selesai')
                || ! $this->filled('waktu_mulai') || ! $this->filled('waktu_selesai')) {
                return;
            }

            $mulai = Carbon::parse($this->input('tanggal_mulai').' '.$this->input('waktu_mulai'));
            $selesai = Carbon::parse($this->input('tanggal_selesai').' '.$this->input('waktu_selesai'));

            if ($selesai->lt($mulai)) {
                $validator->errors()->add(
                    'waktu_selesai',
                    'Waktu selesai harus sama dengan atau setelah waktu mulai.'
                );
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kegiatan wajib diisi.',
            'nama.max' => 'Nama kegiatan maksimal 255 karakter.',
            'deskripsi.max' => 'Deskripsi terlalu panjang. Maksimal 2000 karakter.',
            'jenis.required' => 'Jenis kegiatan wajib dipilih.',
            'jenis.in' => 'Jenis kegiatan tidak valid.',
            'tanggal_mulai.required' => 'Tanggal mulai kegiatan wajib diisi.',
            'tanggal_mulai.date' => 'Tanggal mulai kegiatan tidak valid.',
            'tanggal_selesai.required' => 'Tanggal selesai kegiatan wajib diisi.',
            'tanggal_selesai.date' => 'Tanggal selesai kegiatan tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.',
            'waktu_mulai.required' => 'Waktu mulai wajib diisi.',
            'waktu_mulai.date_format' => 'Format waktu mulai tidak valid.',
            'waktu_selesai.date_format' => 'Format waktu selesai tidak valid.',
            'waktu_selesai.after_or_equal' => 'Waktu selesai harus sama dengan atau setelah waktu mulai.',
            'tempat.required' => 'Tempat kegiatan wajib diisi.',
            'tempat.max' => 'Nama tempat maksimal 255 karakter.',
            'presensi_mulai.date' => 'Waktu buka presensi tidak valid.',
            'presensi_selesai.date' => 'Waktu tutup presensi tidak valid.',
            'presensi_selesai.after_or_equal' => 'Waktu tutup presensi harus sama dengan atau setelah waktu buka.',
        ];
    }
}
