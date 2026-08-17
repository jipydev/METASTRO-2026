<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitTugasHukumanRequest extends FormRequest
{
    public function authorize(): bool
    {
        $hukuman = $this->route('hukuman');

        return $hukuman
            && $this->user()?->id === $hukuman->user_id
            && ! $hukuman->isSelesai()
            && $hukuman->sudahPembelaan();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tugas_link' => ['nullable', 'url', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tugas_link.url' => 'Link tugas harus berupa URL yang valid (contoh: Google Drive).',
            'tugas_link.max' => 'Link terlalu panjang.',
        ];
    }
}
