<?php

namespace App\Services;

use App\Exceptions\AttendanceAlreadyRecordedException;
use App\Models\Divisi;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\User;
use Carbon\CarbonInterface;

class AttendanceRecorder
{
    public function __construct(private NotificationDispatcher $notifications) {}

    public function record(
        User $peserta,
        Kegiatan $kegiatan,
        User $petugas,
        string $sumber = 'scan',
        ?CarbonInterface $jamTap = null,
        bool $notifyRangers = true,
    ): Presensi {
        $alreadyRecorded = Presensi::query()
            ->where('user_id', $peserta->id)
            ->where('kegiatan_id', $kegiatan->id)
            ->exists();

        if ($alreadyRecorded) {
            throw new AttendanceAlreadyRecordedException((string) $kegiatan->nama);
        }

        $petugas->loadMissing('divisi');

        $label = match ($sumber) {
            'manual' => 'Input manual oleh',
            'import' => 'Impor berkas oleh',
            default => 'Di scan oleh',
        };

        $divisiNama = $petugas->divisi instanceof Divisi
            ? $petugas->divisi->nama
            : 'Umum';

        $presensi = Presensi::create([
            'user_id' => $peserta->id,
            'kegiatan_id' => $kegiatan->id,
            'scanned_by' => $petugas->id,
            'status' => 'hadir',
            'jam_tap' => $jamTap ?? now(),
            'keterangan' => $label.': '.$petugas->nama.' ('.$divisiNama.')',
        ]);

        $this->notifications->presensiRecorded($presensi, notifyRangers: $notifyRangers);

        return $presensi;
    }
}
