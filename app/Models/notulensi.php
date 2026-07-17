<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notulensi extends Model
{
    use HasFactory;

    protected $table = 'notulensi';

    protected $fillable = [
        'jadwal_id',
        'pembuat_id',
        'judul',
        'isi_notulensi',
        'lampiran',
        'keputusan_rapat',
        'tindak_lanjut'
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }
}