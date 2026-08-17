<?php

namespace App\Http\Requests;

use App\Services\PengumumanPublisher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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

    protected function prepareForValidation(): void
    {
        if ($this->has('status')) {
            $this->merge([
                'status' => app(PengumumanPublisher::class)->normalizeStatus((string) $this->input('status')),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isPublished = $this->input('status') === 'published';

        return [
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string'],
            'tanggal_publish' => [
                Rule::requiredIf(! $isPublished),
                'nullable',
                'date',
                Rule::when(! $isPublished, ['after_or_equal:'.now()->startOfMinute()->toDateTimeString()]),
            ],
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
            'tanggal_publish.required' => 'Tanggal rilis wajib diisi untuk draft terjadwal.',
            'tanggal_publish.date' => 'Tanggal rilis tidak valid.',
            'tanggal_publish.after_or_equal' => 'Tanggal rilis minimal waktu sekarang.',
            'status.required' => 'Status pengumuman wajib dipilih.',
            'status.in' => 'Status pengumuman harus Draft atau Publish.',
            'lampiran.mimes' => 'Lampiran harus berupa file PDF.',
            'lampiran.max' => 'Ukuran lampiran maksimal 5 MB.',
        ];
    }
}
