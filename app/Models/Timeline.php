<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeline extends Model
{
    /** @use HasFactory<\Database\Factories\TimelineFactory> */
    use HasFactory;

    protected $fillable = [
        'judul',
        'slug',
        'tanggal_mulai',
        'tanggal_selesai',
        'ruangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    public function presensi()
    {
        return $this->hasMany(Presensi::class);
    }
}
