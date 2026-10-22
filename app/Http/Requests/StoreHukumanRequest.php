<?php

namespace App\Http\Requests;

use App\Models\Hukuman;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreHukumanRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();

        if (! $user) {
            return false;
        }

        $hukuman = $this->route('hukuman');

        if ($hukuman instanceof Hukuman) {
            return $user->canManageHukumanRecord($hukuman);
        }

        return $user->canManageHukuman();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'kategori' => ['required', Rule::in(Hukuman::KATEGORI)],
            'alasan' => ['required', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Target hukuman wajib dipilih.',
            'user_id.exists' => 'Target yang dipilih tidak ditemukan.',
            'kategori.required' => 'Kategori hukuman wajib dipilih.',
            'kategori.in' => 'Kategori hukuman tidak valid.',
            'alasan.required' => 'Alasan hukuman wajib diisi.',
            'alasan.max' => 'Alasan terlalu panjang. Maksimal 2000 karakter.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            /** @var User|null $user */
            $user = $this->user();

            $target = User::query()
                ->with('jabatan')
                ->find($this->input('user_id'));

            if (! $target) {
                return;
            }

            if ($target->isTargetHukumanPengawas()) {
                if (! $user?->canIssueHukumanPengawas() && ! $user?->isAdmin()) {
                    $validator->errors()->add('user_id', 'Hanya pengawas atau admin yang dapat menghukum pengawas.');
                }
            } else {
                if (! $user?->canIssueHukumanRanger() && ! $user?->isAdmin()) {
                    $validator->errors()->add('user_id', 'Target tidak valid atau Anda tidak memiliki hak untuk menghukum target ini.');
                }
            }
        });
    }
}
