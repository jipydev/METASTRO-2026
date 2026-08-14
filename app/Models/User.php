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

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $nim
 * @property string|null $password
 * @property string|null $nomor_hp
 * @property string|null $foto
 * @property string|null $tanggal_lahir
 * @property string|null $jenis_kelamin
 * @property string|null $alamat
 * @property int|null $divisi_id
 * @property int|null $jabatan_id
 * @property string|null $qr_token
 * @property bool $status_aktif
 * @property bool $is_initial_setup_completed
 * @property \App\Models\Divisi|null $divisi
 * @property \App\Models\Jabatan|null $jabatan
 */
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
    | Roles & Authorization Helpers
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['admin', 'Admin']) || $this->isChiper();
    }

    public function isPanitia(): bool
    {
        return $this->hasAnyRole(['panitia', 'Panitia']);
    }

    public function isPeserta(): bool
    {
        return $this->hasAnyRole(['peserta', 'Peserta']);
    }

    /*
    |--------------------------------------------------------------------------
    | Divisi Helpers
    |--------------------------------------------------------------------------
    */

    public function isArchivist(): bool
    {
        $divisiName = strtolower($this->divisi?->nama_divisi ?? '');
        if (in_array($divisiName, ['archivist', 'sekretaris', 'scribe'])) {
            return true;
        }

        if ($this->hasAnyRole(['archivist', 'Archivist', 'sekretaris', 'Sekretaris', 'scribe', 'Scribe'])) {
            return true;
        }

        if ($this->divisi_id) {
            $div = Divisi::find($this->divisi_id);
            if ($div && in_array(strtolower($div->nama_divisi), ['archivist', 'sekretaris', 'scribe'])) {
                return true;
            }
        }

        return false;
    }

    public function isChiper(): bool
    {
        $divisiName = strtolower($this->divisi?->nama_divisi ?? '');
        if ($divisiName === 'chiper' || $this->hasAnyRole(['chiper', 'Chiper'])) {
            return true;
        }
        if ($this->divisi_id) {
            $div = Divisi::find($this->divisi_id);
            if ($div && strtolower($div->nama_divisi) === 'chiper') {
                return true;
            }
        }
        return false;
    }

    public function isRanger(): bool
    {
        $divisiName = strtolower($this->divisi?->nama_divisi ?? '');
        if ($divisiName === 'ranger' || $this->hasAnyRole(['ranger', 'Ranger'])) {
            return true;
        }
        if ($this->divisi_id) {
            $div = Divisi::find($this->divisi_id);
            if ($div && strtolower($div->nama_divisi) === 'ranger') {
                return true;
            }
        }
        return false;
    }

    public function isGuider(): bool
    {
        return strtolower($this->divisi?->nama_divisi ?? '') === 'guider';
    }

    public function isStakeholder(): bool
    {
        $divisiName = strtolower($this->divisi?->nama_divisi ?? '');
        if ($divisiName === 'stakeholder' || $this->hasAnyRole(['stakeholder', 'Stakeholder'])) {
            return true;
        }
        if ($this->divisi_id) {
            $div = Divisi::find($this->divisi_id);
            if ($div && strtolower($div->nama_divisi) === 'stakeholder') {
                return true;
            }
        }
        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Jabatan Helpers
    |--------------------------------------------------------------------------
    */

    public function isKetua(): bool
    {
        return strtolower($this->jabatan?->nama_jabatan ?? '') === 'ketua';
    }

    public function isWakil(): bool
    {
        return strtolower($this->jabatan?->nama_jabatan ?? '') === 'wakil';
    }

    public function isKetuaOrWakil(): bool
    {
        return $this->isKetua() || $this->isWakil();
    }

    public function isKetuaPengawas(): bool
    {
        return strtolower($this->jabatan?->nama_jabatan ?? '') === 'ketua pengawas';
    }

    public function isPengawas(): bool
    {
        return strtolower($this->jabatan?->nama_jabatan ?? '') === 'pengawas';
    }

    public function isAnggota(): bool
    {
        return strtolower($this->jabatan?->nama_jabatan ?? '') === 'anggota';
    }

    /*
    |--------------------------------------------------------------------------
    | Privilege Helpers
    |--------------------------------------------------------------------------
    */

    public function canManageArchivistFeatures(): bool
    {
        return $this->isAdmin() || $this->isArchivist();
    }

    public function canManageRangerFeatures(): bool
    {
        return $this->isAdmin() || $this->isRanger();
    }

    public function canViewPanitiaList(): bool
    {
        return $this->isAdmin() || $this->isRanger() || $this->isArchivist() || $this->isStakeholder() || $this->isPengawas() || $this->isKetuaPengawas();
    }

    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }
}
