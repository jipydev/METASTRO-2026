<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Divisi extends Model
{
    use HasFactory;

    protected $table = 'divisi';

    protected $fillable = [
        'nama_divisi',
        'deskripsi',
        'ketua_divisi_id',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function ketua()
    {
        return $this->belongsTo(User::class, 'ketua_divisi_id');
    }

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class);
    }
}