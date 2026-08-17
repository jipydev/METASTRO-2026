<?php

namespace App\Http\Requests\Admin;

use App\Models\Divisi;
use App\Models\Jabatan;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

abstract class UserFormRequest extends FormRequest
{
    protected function jabatanDivisiRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (! $value) {
                return;
            }

            $divisi = Divisi::query()->find($this->input('divisi_id'));
            $jabatan = Jabatan::query()->find($value);

            if (! $jabatan) {
                return;
            }

            if (! Jabatan::matchesDivisi($divisi?->nama, $jabatan->nama)) {
                $fail('Jabatan tidak sesuai dengan divisi yang dipilih.');
            }
        };
    }
}
