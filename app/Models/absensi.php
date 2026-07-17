<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absensi extends Model
{
    use HasFactory;

    protected $table='absensi';

    protected $fillable=[
        'jadwal_id',
        'user_id',
        'status',
        'keterangan',
        'bukti',
        'waktu_absen',
        'persentase_kehadiran'
    ];

    protected $casts=[
        'waktu_absen'=>'datetime'
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}