<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengumpulanTugas extends Model
{
    /** @use HasFactory<\Database\Factories\PengumpulanTugasFactory> */
    use HasFactory;

    protected $fillable = [
        'tugas_id',
        'user_id',
        'tim_id',
        'file_path',
        'catatan_peserta',
        'catatan_pemeriksa',
        'pemeriksa_id',
        'status',
        'tanggal_pengumpulan',
    ];

    protected $table = 'pengumpulan_tugases';

    public function tugas()
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }

    public function peserta()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tim()
    {
        return $this->belongsTo(Tim::class, 'tim_id');
    }

    public function pemeriksa()
    {
        return $this->belongsTo(User::class, 'pemeriksa_id');
    }


}
