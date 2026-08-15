<?php

namespace App\Models;

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
        'waktu_mulai',
        'waktu_selesai',
        'status_presensi',
        'presensi_mulai',
        'presensi_toleransi',
        'presensi_selesai',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'waktu_mulai'   => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
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
}
