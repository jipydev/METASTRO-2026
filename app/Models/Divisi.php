<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi',
        'koordinator_id',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function koordinator()
    {
        return $this->belongsTo(User::class, 'koordinator_id');
    }

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class);
    }
}
