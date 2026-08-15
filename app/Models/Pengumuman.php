<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumumans';

    protected $fillable = [
        'judul',
        'isi',
        'lampiran',
        'target',
        'tanggal_publish',
        'status',
        'pembuat_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_publish' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi Eloquent
    |--------------------------------------------------------------------------
    */

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Local Scopes (Memudahkan Query di Controller)
    |--------------------------------------------------------------------------
    */

    /**
     * Scope hanya pengumuman yang sudah terbit
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where('tanggal_publish', '<=', now());
    }

    /**
     * Scope berdasarkan target pembaca user yang login
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->isAdmin() || $user->isPanitia()) {
            return $query->whereIn('target', ['semua', 'panitia']);
        }

        return $query->whereIn('target', ['semua', 'peserta']);
    }
}
