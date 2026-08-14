<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $jadwal_id
 * @property int|null $pembuat_id
 * @property string $judul
 * @property string|null $isi_notulensi
 * @property string|null $lampiran
 * @property string|null $keputusan_rapat
 * @property string|null $tindak_lanjut
 */
class Notulensi extends Model
{
    use HasFactory;

    protected $table = 'notulensi';

    protected $fillable = [
        'jadwal_id',
        'pembuat_id',
        'judul',
        'isi_notulensi',
        'lampiran',
        'keputusan_rapat',
        'tindak_lanjut',
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }
}
