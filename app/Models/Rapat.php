<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rapat extends Model
{
    protected $fillable = [
        'judul',
        'tanggal',
        'jam',
        'tempat',
        'hadir',
        'total',
        'status_absen',
        'waktu_buka',
        'waktu_telat',
        'waktu_tutup',
    ];

    public function listPanitias()
    {
        return $this->hasMany(ListPanitia::class);
    }

    public function pengajuanIzins()
    {
        return $this->hasMany(PengajuanIzin::class);
    }
}
