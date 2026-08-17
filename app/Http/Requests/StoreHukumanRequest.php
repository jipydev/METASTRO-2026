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
        $mode = $this->issuerMode();

        if ($mode === 'ranger') {
            return $this->user()?->canIssueHukumanRanger() ?? false;
        }

        return $this->user()?->canIssueHukumanPengawas() ?? false;
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
            'user_id.required' => 'Panitia yang dihukum wajib dipilih.',
            'user_id.exists' => 'Panitia yang dipilih tidak ditemukan.',
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

            $target = User::query()
                ->with('jabatan')
                ->find($this->input('user_id'));

            if (! $target) {
                return;
            }

            $mode = $this->issuerMode();

            if ($mode === 'ranger' && ! $this->isValidRangerTarget($target)) {
                $message = $this->user()?->isAdmin()
                    ? 'Target tidak valid untuk hukuman.'
                    : 'Pengawas tidak dapat dihukum melalui mode Ranger.';

                $validator->errors()->add('user_id', $message);
            }

            if ($mode === 'pengawas' && ! $target->isTargetHukumanPengawas()) {
                $validator->errors()->add('user_id', 'Hanya pengawas yang dapat dihukum melalui mode Pengawas.');
            }
        });
    }

    public function issuerMode(): string
    {
        return $this->route('mode', 'ranger') === 'pengawas' ? 'pengawas' : 'ranger';
    }

    private function isValidRangerTarget(User $target): bool
    {
        if (! $target->status || ! $target->divisi_id) {
            return false;
        }

        if ($this->user()?->isAdmin()) {
            return true;
        }

        return ! $target->isTargetHukumanPengawas();
    }
}
