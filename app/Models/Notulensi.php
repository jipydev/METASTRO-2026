<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notulensi extends Model
{
    use HasFactory;

    protected $table = 'notulensis';

    protected $fillable = [
        'kegiatan_id',
        'pembuat_id',
        'judul',
        'isi',
        'lampiran',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relasi Eloquent
    |--------------------------------------------------------------------------
    */

    /**
     * Kegiatan / rapat yang terkait
     */
    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    /**
     * Notulis / user pembuat catatan
     */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }
}
