<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hukuman extends Model
{
    use HasFactory;

    protected $table = 'hukuman';

    protected $fillable = [
        'user_id',
        'pemberi_id',
        'kategori',
        'pelanggaran',
        'konsekuensi',
        'tanggal_hukuman',
        'status',
        'catatan'
    ];

    protected $casts = [
        'tanggal_hukuman' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pemberi()
    {
        return $this->belongsTo(User::class, 'pemberi_id');
    }
}