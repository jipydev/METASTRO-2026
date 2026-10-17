<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    /** @use HasFactory<\Database\Factories\TugasFactory> */
    use HasFactory;
    public const DAYS = ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'];

    protected $fillable = [
        'judul', 
        'deskripsi', 
        'status', 
        'jenis', 
        'day', 
        'tenggat_waktu', 
        'pembuat_id'
    ];
    
    protected $table = 'tugases';

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }
}