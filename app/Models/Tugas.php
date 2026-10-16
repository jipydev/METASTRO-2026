<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    /** @use HasFactory<\Database\Factories\TugasFactory> */
    use HasFactory;
    
    protected $fillable = ['judul', 'deskripsi', 'status', 'jenis', 'hari', 'tenggat_waktu', 'pembuat_id'];
    protected $table = 'tugases';

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }

    public function pengumpulans()
    {
        return $this->hasMany(PengumpulanTugas::class, 'tugas_id');
    }

    public function submissionFor(?User $user): ?PengumpulanTugas
    {
        if (! $user) {
            return null;
        }

        if ($this->jenis === 'tim') {
            $timId = AnggotaTim::where('anggota_id', $user->id)->value('tim_id');
            if ($timId) {
                $teamSubmisi = $this->pengumpulans()->where('tim_id', $timId)->latest('tanggal_pengumpulan')->first();
                if ($teamSubmisi) {
                    return $teamSubmisi;
                }
            }

            return $this->pengumpulans()->where('user_id', $user->id)->latest('tanggal_pengumpulan')->first();
        }

        if ($this->jenis === 'angkatan') {
            return $this->pengumpulans()->latest('tanggal_pengumpulan')->first();
        }

        return $this->pengumpulans()->where('user_id', $user->id)->latest('tanggal_pengumpulan')->first();
    }
}
