<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatans';

    protected $fillable = [
        'nama',
        'deskripsi',
        'tipe',
        'tempat',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'presensi_mulai',
        'presensi_selesai',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'waktu_mulai' => 'string',
            'waktu_selesai' => 'string',
            'presensi_mulai' => 'datetime',
            'presensi_selesai' => 'datetime',
        ];
    }

    /**
     * Alias kolom lama `judul` agar pemanggilan $kegiatan->judul tetap menampilkan nama.
     */
    public function getJudulAttribute(): ?string
    {
        return $this->attributes['nama'] ?? null;
    }

    public function isPresensiOpen(): bool
    {
        return $this->status_presensi === 'buka';
    }

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }

    public function notulensis(): HasMany
    {
        return $this->hasMany(Notulensi::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Jam tap default untuk input manual / impor tanpa kolom waktu: 15 menit sebelum mulai.
     */
    public function defaultJamTap(): CarbonInterface
    {
        if (! $this->waktu_mulai) {
            return now();
        }

        return Carbon::parse($this->tanggal)
            ->setTimeFromTimeString(substr((string) $this->waktu_mulai, 0, 8))
            ->subMinutes(15);
    }

    /**
     * Accessor untuk mendeteksi status presensi aktif berdasarkan waktu.
     */
    public function getStatusPresensiAktifAttribute(): string
    {
        if (! $this->presensi_mulai || ! $this->presensi_selesai) {
            return 'dijadwalkan';
        }

        return now()->between($this->presensi_mulai, $this->presensi_selesai) ? 'buka' : 'tutup';
    }
}
