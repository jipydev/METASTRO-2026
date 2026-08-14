<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name',
    'email',
    'nim',
    'password',
    'nomor_hp',
    'foto',
    'tanggal_lahir',
    'jenis_kelamin',
    'alamat',
    'divisi_id',
    'jabatan_id',
    'qr_token',
    'status_aktif',
    'is_initial_setup_completed',
])]

#[Hidden([
    'password',
    'remember_token',
    'two_factor_secret',
    'two_factor_recovery_codes',
])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory,
        HasRoles,
        Notifiable,
        PasskeyAuthenticatable,
        TwoFactorAuthenticatable;

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_initial_setup_completed' => 'boolean',
            'status_aktif' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    public function roleName(): string
    {
        return $this->role ?? 'Belum ada role';
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function hukuman()
    {
        return $this->hasMany(Hukuman::class);
    }

    public function notulensi()
    {
        return $this->hasMany(Notulensi::class, 'pembuat_id');
    }

    public function pengumuman()
    {
        return $this->hasMany(Pengumuman::class, 'pembuat_id');
    }

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class);
    }

    public function penilaianDibuat()
    {
        return $this->hasMany(Penilaian::class, 'penilai_id');
    }

    public function pengajuanIzin()
    {
        return $this->hasMany(PengajuanIzin::class);
    }

    public function roleRequests()
    {
        return $this->hasMany(RoleRequest::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    public function isKetuaOrWakil(): bool
    {
        $jabatan = strtolower($this->jabatan?->nama_jabatan ?? '');
        return in_array($jabatan, ['ketua', 'wakil']);
    }

    public function isAnggota(): bool
    {
        return strtolower($this->jabatan?->nama_jabatan ?? '') === 'anggota';
    }

    public function isRanger(): bool
    {
        return strtolower($this->divisi?->nama_divisi ?? '') === 'ranger';
    }

    public function isStakeholder(): bool
    {
        return strtolower($this->divisi?->nama_divisi ?? '') === 'stakeholder';
    }

    public function isArchivist(): bool
    {
        return strtolower($this->divisi?->nama_divisi ?? '') === 'archivist';
    }

    public function isPengawas(): bool
    {
        return strtolower($this->jabatan?->nama_jabatan ?? '') === 'pengawas';
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isPanitia(): bool
    {
        return $this->hasRole('panitia');
    }

    public function isPeserta(): bool
    {
        return $this->hasRole('peserta');
    }


    public function isKetuaPengawas(): bool
    {
        return strtolower($this->jabatan?->nama_jabatan ?? '') === 'ketua pengawas';
    }

    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }
}