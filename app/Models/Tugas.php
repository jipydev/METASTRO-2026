<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    /** @use HasFactory<\Database\Factories\TugasFactory> */
    use HasFactory;
    
    protected $fillable = ['judul', 'deskripsi', 'status', 'jenis', 'tenggat_waktu', 'pembuat_id'];
    protected $table = 'tugases';

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }
}
