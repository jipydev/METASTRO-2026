<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensis';

    protected $fillable = [
        'user_id',
        'rapat_id',
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
     * Sesi Rapat/Kegiatan
     */
    public function rapat(): BelongsTo
    {
        return $this->belongsTo(Rapat::class);
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
     * @return BelongsTo<PengajuanIzin, Presensi>
     */
    public function pengajuanIzin(): BelongsTo
    {
        return $this->belongsTo(PengajuanIzin::class);
    }
}
