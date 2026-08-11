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
    // 'email',
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
            // 'email_verified_at' => 'datetime',
            'password' => 'hashed',
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

    public function isKoordinator(): bool
    {
        return strtolower($this->jabatan?->nama_jabatan ?? '') === 'koordinator' || $this->hasRole('Koordinator');
    }

    public function isStaff(): bool
    {
        return strtolower($this->jabatan?->nama_jabatan ?? '') === 'staff';
    }

    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }
}
