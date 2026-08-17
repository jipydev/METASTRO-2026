<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitPembelaanHukumanRequest extends FormRequest
{
    public function authorize(): bool
    {
        $hukuman = $this->route('hukuman');

        return $hukuman
            && $this->user()?->id === $hukuman->user_id
            && ! $hukuman->isSelesai()
            && ! $hukuman->sudahPembelaan();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'pembelaan' => ['required', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'pembelaan.required' => 'Pembelaan wajib diisi sebelum mengerjakan tugas.',
            'pembelaan.max' => 'Pembelaan terlalu panjang. Maksimal 2000 karakter.',
        ];
    }
}
