<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

trait ProfileValidationRules
{
    /**
     * Get the validation rules used to validate user profiles.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    public function profileRules(?int $userId = null): array
    {
        return [
            'name' => $this->nameRules(),
            'nim' => $this->nimRules($userId),
            'divisi_id' => ['required', 'exists:divisi,id'],
            'jabatan_id' => ['required', 'exists:jabatan,id'],
        ];
    }

    /**
     * Get the validation rules used to validate user names.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function nameRules(): array
    {
        return [
            'required',
            'string',
            'max:255',
        ];
    }

    /**
     * Get the validation rules used to validate NIM.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function nimRules(?int $userId = null): array
    {
        return [
            'required',
            'string',
            'max:30',
            $userId === null
                ? Rule::unique(User::class, 'nim')
                : Rule::unique(User::class, 'nim')->ignore($userId),
        ];
    }
}
