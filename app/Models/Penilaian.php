<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use HasFactory;

    protected $table = 'penilaian';

    protected $fillable = [
        'user_id',
        'penilai_id',
        'divisi_id',
        'kategori',
        'disiplin',
        'kehadiran',
        'kerjasama',
        'tanggung_jawab',
        'inisiatif',
        'nilai_akhir',
        'catatan',
    ];

    protected $casts = [
        'nilai_akhir' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function penilai()
    {
        return $this->belongsTo(User::class, 'penilai_id');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }
}
