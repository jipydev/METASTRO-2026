<?php

namespace App\Services;

class AttendanceImportResult
{
    /**
     * @param  list<string>  $errors
     */
    public function __construct(
        public int $imported = 0,
        public int $skipped = 0,
        public array $errors = [],
    ) {}

    public function message(): string
    {
        $parts = ["{$this->imported} kehadiran berhasil diimpor."];

        if ($this->skipped > 0) {
            $parts[] = "{$this->skipped} dilewati karena sudah tercatat.";
        }

        if ($this->errors !== []) {
            $shown = array_slice($this->errors, 0, 8);
            $parts[] = implode(' ', $shown);

            $remaining = count($this->errors) - count($shown);
            if ($remaining > 0) {
                $parts[] = "Dan {$remaining} baris lainnya gagal.";
            }
        }

        return implode(' ', $parts);
    }
}
