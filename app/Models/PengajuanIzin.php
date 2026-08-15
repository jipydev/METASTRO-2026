<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PengajuanIzin extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_izins';

    protected $fillable = [
        'user_id',
        'kegiatan_id',
        'tanggal_izin',
        'jenis_izin',
        'alasan',
        'bukti',
        'status_koordinator',
        'reviewed_by_koordinator',
        'reviewed_at_koordinator',
        'catatan_koordinator',
        'status_ranger',
        'reviewed_by_ranger',
        'reviewed_at_ranger',
        'catatan_ranger',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_izin'            => 'date',
            'reviewed_at_koordinator' => 'datetime',
            'reviewed_at_ranger'      => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi Eloquent
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function reviewerKoordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_koordinator');
    }

    public function reviewerRanger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_ranger');
    }

    public function presensi(): HasOne
    {
        return $this->hasOne(Presensi::class, 'pengajuan_izin_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper State Checks
    |--------------------------------------------------------------------------
    */

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
