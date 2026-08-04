<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class Presensi extends Model
{
    /** @use HasFactory<\Database\Factories\PresensiFactory> */
    use HasFactory;

    protected $fillable = [
        'nim_user',
        'timeline_id',
        'waktu_presensi',
        'status',
        'bukti_foto',
        'surat_izin',
        'scanned_by_user_nim',
        'jenis_izin',
    ];

    public function needsProof(): bool
    {
        return in_array($this->status, ['Izin', 'Sakit']);
    }

    public function isStatus(string $status): bool
    {
        return strcasecmp(trim((string) $this->status), $status) === 0;
    }

    public function resolveStatus(?Timeline $timeline = null): string
    {
        if ($this->status && in_array($this->status, ['Hadir', 'Izin', 'Sakit', 'Alpha'], true)) {
            return $this->status;
        }

        $timeline = $timeline ?? $this->timeline;

        if (! $timeline || ! $timeline->tanggal_mulai || ! $this->waktu_presensi) {
            return 'Alpha';
        }

        $startTime = Carbon::parse($timeline->tanggal_mulai)->setTimezone('Asia/Jakarta');
        $attendanceTime = Carbon::parse($this->waktu_presensi)->setTimezone('Asia/Jakarta');
        $toleranceStart = (clone $startTime)->subMinutes(15);

        if ($attendanceTime->lte($toleranceStart)) {
            return 'Hadir';
        }

        return 'Alpha';
    }

    public function hasProof(): bool
    {
        return !empty($this->bukti_foto) || !empty($this->surat_izin);
    }

    public function getProofUrlAttribute()
    {
        if ($this->bukti_foto) {
            return Storage::url($this->bukti_foto);
        }

        if ($this->surat_izin) {
            return Storage::url($this->surat_izin);
        }

        return null;
    }

    public function timeline()
    {
        return $this->belongsTo(Timeline::class);
    }

    public function panitia()
    {
        return $this->belongsTo(User::class, 'nim_user', 'nim');
    }

    public function scannedByUser()
    {
        return $this->belongsTo(User::class, 'scanned_by_user_nim', 'nim');
    }
}
