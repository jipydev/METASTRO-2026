<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaTim extends Model
{
    /** @use HasFactory<\Database\Factories\AnggotaTimFactory> */
    use HasFactory;
    
    protected $fillable = ['anggota_id', 'tim_id'];

    public function anggota()
    {
        return $this->belongsTo(User::class, 'anggota_id');
    }

    public function tim()
    {
        return $this->belongsTo(Tim::class, 'tim_id');
    }
}
