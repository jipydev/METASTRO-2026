<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;

    protected $table = 'divisi';

    protected $fillable = [
        'nama_divisi',
        'deskripsi',
        'koordinator_divisi_nim',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function koordinator()
    {
        return $this->belongsTo(User::class, 'koordinator_divisi_nim', 'nim');
    }

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class);
    }
}
