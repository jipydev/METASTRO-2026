<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table='pengumuman';

    protected $fillable=[
        'judul',
        'isi',
        'lampiran',
        'tanggal_publish',
        'status',
        'pembuat_id'
    ];

    public function pembuat()
    {
        return $this->belongsTo(User::class,'pembuat_id');
    }
}