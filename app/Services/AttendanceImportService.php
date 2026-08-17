<?php

namespace App\Services;

use App\Exceptions\AttendanceAlreadyRecordedException;
use App\Models\Kegiatan;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class AttendanceImportService
{
    public function __construct(
        private AttendanceImportParser $parser,
        private AttendanceRecorder $recorder,
        private NotificationDispatcher $notifications,
    ) {}

    public function import(UploadedFile $file, Kegiatan $kegiatan, User $petugas): AttendanceImportResult
    {
        $result = new AttendanceImportResult;
        $rows = $this->parser->parse($file);

        foreach ($rows as $row) {
            if (in_array($row['status'], ['izin', 'sakit'], true)) {
                $result->errors[] = "Baris {$row['row']}: NIM {$row['nim']} berstatus {$row['status']} — gunakan pengajuan izin.";

                continue;
            }

            if ($row['status'] === 'alpa') {
                $result->errors[] = "Baris {$row['row']}: NIM {$row['nim']} berstatus alpa dilewati (hanya hadir yang diimpor).";

                continue;
            }

            if ($row['status'] !== 'hadir') {
                $result->errors[] = "Baris {$row['row']}: Status '{$row['status']}' tidak dikenali.";

                continue;
            }

            $peserta = User::query()
                ->where('nim', $row['nim'])
                ->first();

            if (! $peserta) {
                $result->errors[] = "Baris {$row['row']}: NIM {$row['nim']} tidak ditemukan.";

                continue;
            }

            if (! $peserta->status) {
                $result->errors[] = "Baris {$row['row']}: Akun {$peserta->nama} sedang dinonaktifkan.";

                continue;
            }

            try {
                $jamTap = $this->resolveJamTap($row['waktu'], $kegiatan);
            } catch (InvalidArgumentException $e) {
                $result->errors[] = "Baris {$row['row']}: ".$e->getMessage();

                continue;
            }

            try {
                $this->recorder->record(
                    $peserta,
                    $kegiatan,
                    $petugas,
                    'import',
                    $jamTap,
                    notifyRangers: false,
                );
                $result->imported++;
            } catch (AttendanceAlreadyRecordedException) {
                $result->skipped++;
            }
        }

        if ($result->imported > 0) {
            $this->notifications->presensiImported($kegiatan, $result->imported, $petugas->id);
        }

        return $result;
    }

    private function resolveJamTap(mixed $value, Kegiatan $kegiatan): CarbonInterface
    {
        if ($value === null || (is_string($value) && trim($value) === '')) {
            return $kegiatan->defaultJamTap();
        }

        if (is_numeric($value)) {
            $number = (float) $value;

            if ($number > 0 && $number < 1) {
                return Carbon::parse($kegiatan->tanggal)
                    ->startOfDay()
                    ->addSeconds((int) round($number * 86400));
            }

            $unix = (int) round(($number - 25569) * 86400);

            return Carbon::createFromTimestamp($unix, (string) config('app.timezone'));
        }

        $raw = trim((string) $value);
        $timeFormats = ['H:i:s', 'H:i', 'G:i'];
        $dateFormats = ['Y-m-d H:i:s', 'Y-m-d H:i', 'd/m/Y H:i', 'd-m-Y H:i', 'd/m/Y H:i:s'];

        foreach ($timeFormats as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $raw);
            } catch (InvalidFormatException) {
                continue;
            }

            return Carbon::parse($kegiatan->tanggal)->setTimeFrom($parsed);
        }

        foreach ($dateFormats as $format) {
            try {
                return Carbon::createFromFormat($format, $raw);
            } catch (InvalidFormatException) {
                continue;
            }
        }

        try {
            return Carbon::parse($raw);
        } catch (\Throwable) {
            throw new InvalidArgumentException("Waktu '{$raw}' tidak valid.");
        }
    }
}
