<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $judul
 * @property string|null $isi
 * @property string $status
 * @property string|null $lampiran
 * @property int|null $pembuat_id
 * @property \Illuminate\Support\Carbon|null $tanggal_publish
 * @property-read string|null $lampiran_url
 * @property-read \App\Models\User|null $pembuat
 */
class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';

    protected $fillable = [
        'judul',
        'isi',
        'status',
        'lampiran',
        'pembuat_id',
        'tanggal_publish',
    ];

    protected $casts = [
        'tanggal_publish' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scope
    |--------------------------------------------------------------------------
    */

    public function scopePublish($query)
    {
        return $query->where('status', 'Publish');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor
    |--------------------------------------------------------------------------
    */

    public function getLampiranUrlAttribute()
    {
        if (! $this->lampiran) {
            return null;
        }

        return asset('storage/'.$this->lampiran);
    }
}
