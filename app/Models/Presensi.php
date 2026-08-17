<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensis';

    protected $fillable = [
        'user_id',
        'kegiatan_id',
        'pengajuan_izin_id',
        'scanned_by',
        'jam_tap',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'jam_tap' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi Eloquent
    |--------------------------------------------------------------------------
    */

    /**
     * Panitia/Peserta yang hadir
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sesi Kegiatan
     */
    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    /**
     * Panitia/Scanner yang melakukan tap
     */
    public function scanner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    /**
     * Panitia mengajukan izin
     *
     * @return BelongsTo<PengajuanIzin, Presensi>
     */
    public function pengajuanIzin(): BelongsTo
    {
        return $this->belongsTo(PengajuanIzin::class);
    }

    public function isTerlambat(): bool
    {
        if ($this->status !== 'hadir' || ! $this->jam_tap) {
            return false;
        }

        $kegiatan = $this->relationLoaded('kegiatan') ? $this->kegiatan : $this->kegiatan()->first();
        if (! $kegiatan?->waktu_mulai) {
            return false;
        }

        $jadwalMulai = Carbon::parse(
            Carbon::parse($kegiatan->tanggal)->format('Y-m-d').' '.$kegiatan->waktu_mulai
        );

        return $this->jam_tap->greaterThan($jadwalMulai);
    }

    public function getStatusTampilanAttribute(): string
    {
        return $this->isTerlambat() ? 'terlambat' : (string) $this->status;
    }

    public function isIzinAtauSakit(): bool
    {
        return in_array($this->status, ['izin', 'sakit'], true);
    }
}
