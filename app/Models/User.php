<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'nama',
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
    'status',
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
            'password'                   => 'hashed',
            'email_verified_at'          => 'datetime',
            'tanggal_lahir'              => 'date',
            'status'                     => 'boolean',
            'is_initial_setup_completed' => 'boolean',
        ];
    }

    /**
     * Auto-generate UUID qr_token saat pembuatan user baru
     */
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->qr_token)) {
                $user->qr_token = (string) Str::uuid();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi Eloquent
    |--------------------------------------------------------------------------
    */

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }

    public function notulensi(): HasMany
    {
        return $this->hasMany(Notulensi::class, 'pembuat_id');
    }

    public function pengumuman(): HasMany
    {
        return $this->hasMany(Pengumuman::class, 'pembuat_id');
    }

    public function pengajuanIzin(): HasMany
    {
        return $this->hasMany(PengajuanIzin::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Core Matchers (ABAC Engine)
    |--------------------------------------------------------------------------
    */

    public function isDivisi(string|array $divisi): bool
    {
        $current = strtolower($this->divisi?->nama ?? '');
        $targets = array_map('strtolower', (array) $divisi);

        return in_array($current, $targets, true);
    }

    public function isJabatan(string|array $jabatan): bool
    {
        $current = strtolower($this->jabatan?->nama ?? '');
        $targets = array_map('strtolower', (array) $jabatan);

        return in_array($current, $targets, true);
    }

    /*
    |--------------------------------------------------------------------------
    | Spatie Roles (RBAC)
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->isChiper();
    }

    public function isPanitia(): bool
    {
        return $this->hasRole('panitia');
    }

    public function isPeserta(): bool
    {
        return $this->hasRole('peserta');
    }

    /*
    |--------------------------------------------------------------------------
    | Shortcut Divisi (Alphabetical A-Z)
    |--------------------------------------------------------------------------
    */

    public function isArchivist(): bool
    {
        return $this->isDivisi('archivist');
    }
    public function isChef(): bool
    {
        return $this->isDivisi('chef');
    }
    public function isChiper(): bool
    {
        return $this->isDivisi('chiper');
    }
    public function isDocumenter(): bool
    {
        return $this->isDivisi('documenter');
    }
    public function isFundkeeper(): bool
    {
        return $this->isDivisi('fundkeeper');
    }
    public function isGearmaster(): bool
    {
        return $this->isDivisi('gearmaster');
    }
    public function isGuardian(): bool
    {
        return $this->isDivisi('guardian');
    }
    public function isGuider(): bool
    {
        return $this->isDivisi('guider');
    }
    public function isInformer(): bool
    {
        return $this->isDivisi('informer');
    }
    public function isPathfinder(): bool
    {
        return $this->isDivisi('pathfinder');
    }
    public function isRanger(): bool
    {
        return $this->isDivisi('ranger');
    }
    public function isRescuer(): bool
    {
        return $this->isDivisi('rescuer');
    }
    public function isScribe(): bool
    {
        return $this->isDivisi('scribe');
    }
    public function isStakeholder(): bool
    {
        return $this->isDivisi('stakeholder');
    }

    /*
    |--------------------------------------------------------------------------
    | Shortcut Jabatan
    |--------------------------------------------------------------------------
    */

    public function isKetua(): bool
    {
        return $this->isJabatan('ketua');
    }
    public function isWakil(): bool
    {
        return $this->isJabatan('wakil');
    }
    public function isKetuaOrWakil(): bool
    {
        return $this->isJabatan(['ketua', 'wakil']);
    }
    public function isPengawas(): bool
    {
        return $this->isJabatan('pengawas');
    }
    public function isKetuaPengawas(): bool
    {
        return $this->isPengawas() && $this->isStakeholder();
    }
    public function isAnggota(): bool
    {
        return $this->isJabatan('anggota');
    }

    /*
    |--------------------------------------------------------------------------
    | Hak Akses Fitur Khusus & Utilitas
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Hak Akses Fitur Khusus & Utilitas
    |--------------------------------------------------------------------------
    */

    public function canScanPresensi(): bool
    {
        return $this->isAdmin() || $this->isArchivist();
    }

    public function canManageSekretariat(): bool
    {
        return $this->isAdmin() || $this->isArchivist();
    }

    public function initials(): string
    {
        $initials = Str::initials($this->nama, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1) . Str::substr($initials, -1)
            : $initials;
    }
}
