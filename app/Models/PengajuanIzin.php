<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanIzin extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_izin';

    protected $fillable = [
        'user_id',
        'tanggal_izin',
        'jenis_izin',
        'alasan',
        'surat_izin',
        'bukti',
        'status_koordinator',
        'reviewed_by_koordinator',
        'reviewed_at_koordinator',
        'catatan_koordinator',
        'status_ranger',
        'reviewed_by_ranger',
        'reviewed_at_ranger',
        'catatan_ranger',
        'status',
    ];

    protected $casts = [
        'tanggal_izin' => 'date',
        'reviewed_at_koordinator' => 'datetime',
        'reviewed_at_ranger' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewerKoordinator()
    {
        return $this->belongsTo(User::class, 'reviewed_by_koordinator');
    }

    public function reviewerRanger()
    {
        return $this->belongsTo(User::class, 'reviewed_by_ranger');
    }
}
