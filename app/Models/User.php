<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
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
    'qr_updated_at',
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
            'email_verified_at' => 'datetime',
            'tanggal_lahir' => 'date',
            'status' => 'boolean',
            'is_initial_setup_completed' => 'boolean',
            'qr_updated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (User $user): void {
            $user->prepareForDeletion();
        });
    }

    /**
     * Bersihkan relasi sebelum user dihapus agar tidak gagal di FK hosting.
     */
    public function prepareForDeletion(): void
    {
        Divisi::query()
            ->where('koordinator_id', $this->id)
            ->update(['koordinator_id' => null]);

        if (Schema::hasTable('hukumans')) {
            Hukuman::query()
                ->where('user_id', $this->id)
                ->orWhere('issued_by', $this->id)
                ->delete();
        }

        Presensi::query()
            ->where('scanned_by', $this->id)
            ->update(['scanned_by' => null]);

        PengajuanIzin::query()
            ->where('reviewed_by_koordinator', $this->id)
            ->update(['reviewed_by_koordinator' => null]);

        PengajuanIzin::query()
            ->where('reviewed_by_ranger', $this->id)
            ->update(['reviewed_by_ranger' => null]);

        if (Schema::hasTable('kegiatans')) {
            Kegiatan::query()
                ->where('created_by', $this->id)
                ->update(['created_by' => null]);
        }

        $this->notifications()->delete();
        $this->syncRoles([]);

        if ($this->foto) {
            Storage::disk('public')->delete($this->foto);
        }
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

    public function notulensis(): HasMany
    {
        return $this->hasMany(Notulensi::class, 'pembuat_id');
    }

    public function pengumumans(): HasMany
    {
        return $this->hasMany(Pengumuman::class, 'pembuat_id');
    }

    public function pengajuanIzins(): HasMany
    {
        return $this->hasMany(PengajuanIzin::class);
    }

    public function hukumans(): HasMany
    {
        return $this->hasMany(Hukuman::class);
    }

    public function hukumansDiterbitkan(): HasMany
    {
        return $this->hasMany(Hukuman::class, 'issued_by');
    }

    /**
     * Panitia aktif (punya divisi atau jabatan).
     *
     * @return Collection<int, self>
     */
    public static function activePanitia(?int $exceptUserId = null): Collection
    {
        return static::query()
            ->where('status', true)
            ->where(function (Builder $q) {
                $q->whereNotNull('divisi_id')->orWhereNotNull('jabatan_id');
            })
            ->when($exceptUserId, fn (Builder $q) => $q->where('id', '!=', $exceptUserId))
            ->get();
    }

    /**
     * Anggota aktif Divisi Ranger.
     *
     * @return Collection<int, self>
     */
    public static function activeRangers(): Collection
    {
        return static::query()
            ->where('status', true)
            ->whereHas('divisi', fn (Builder $q) => $q->whereRaw('LOWER(nama) = ?', ['ranger']))
            ->get();
    }

    /**
     * Ketua/wakil aktif di suatu divisi (koordinator izin).
     *
     * @return Collection<int, self>
     */
    public static function koordinatorsOf(?int $divisiId, ?int $exceptUserId = null): Collection
    {
        if (! $divisiId) {
            return collect();
        }

        return static::query()
            ->where('status', true)
            ->where('divisi_id', $divisiId)
            ->when($exceptUserId, fn (Builder $q) => $q->where('id', '!=', $exceptUserId))
            ->whereHas('jabatan', fn (Builder $q) => $q->whereRaw('LOWER(nama) in (?, ?)', ['ketua', 'wakil']))
            ->get();
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
        return $this->isJabatan('pengawas')
            || $this->isKetuaPengawas()
            || $this->isWakilKetuaPengawas();
    }

    public function isKetuaPengawas(): bool
    {
        return $this->isStakeholder() && $this->isJabatan('Ketua Pengawas');
    }

    public function isWakilKetuaPengawas(): bool
    {
        return $this->isStakeholder() && $this->isJabatan('Wakil Ketua Pengawas');
    }

    public function isPersonInCharge(): bool
    {
        return $this->isStakeholder() && $this->isJabatan('Person in Charge');
    }

    public function isAnggota(): bool
    {
        return $this->isJabatan('anggota');
    }

    /*
    |--------------------------------------------------------------------------
    | Hak Akses Fitur Khusus & Utilitas (ABAC Rules)
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isPanitia(): bool
    {
        return (bool) ($this->divisi_id || $this->jabatan_id);
    }

    /**
     * Ketua / wakil divisi yang mereview izin anggota divisinya.
     * Stakeholder tidak mereview tahap koordinator — izin mereka langsung ke Ranger.
     */
    public function isKoordinatorDivisi(): bool
    {
        return $this->isKetuaOrWakil() && ! $this->isStakeholder();
    }

    public function canCreatePengumuman(): bool
    {
        return $this->isAdmin() || $this->isPanitia();
    }

    public function canManagePengumumanDivisi(?int $divisiId): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (! $divisiId || $this->divisi_id !== $divisiId) {
            return false;
        }

        return $this->isJabatan(['ketua', 'wakil', 'anggota']);
    }

    public function canScanPresensi(): bool
    {
        return $this->isAdmin() || $this->isArchivist();
    }

    public function canManageSekretariat(): bool
    {
        return $this->isAdmin() || $this->isArchivist();
    }

    public function canManageKegiatan(): bool
    {
        return $this->isAdmin() || $this->isArchivist() || $this->isStakeholder() || $this->isPathfinder();
    }

    public function canTogglePresensi(): bool
    {
        return $this->canManageKegiatan() || $this->isRanger();
    }

    public function canViewAllIzinReviews(): bool
    {
        return $this->isAdmin() || $this->isRanger() || $this->isArchivist() || $this->isStakeholder();
    }

    public function canReviewIzin(): bool
    {
        return $this->canViewAllIzinReviews() || $this->isKoordinatorDivisi();
    }

    public function canApproveKoordinatorIzin(?PengajuanIzin $izin = null): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (! $this->isKoordinatorDivisi() || ! $izin?->user) {
            return false;
        }

        $applicant = $izin->user;

        return $applicant->divisi_id
            && $applicant->divisi_id === $this->divisi_id
            && $applicant->id !== $this->id;
    }

    public function canApproveRangerIzin(): bool
    {
        return $this->isAdmin() || $this->isRanger();
    }

    public function skipsKoordinatorIzinReview(): bool
    {
        return $this->isKetua() || $this->isStakeholder();
    }

    public function canViewPanitiaList(): bool
    {
        return $this->isAdmin() || $this->isArchivist() || $this->isRanger() || $this->isStakeholder();
    }

    public function canIssueHukumanRanger(): bool
    {
        return $this->isAdmin() || $this->isRanger();
    }

    public function canIssueHukumanPengawas(): bool
    {
        return $this->isPengawas();
    }

    public function canManageHukuman(): bool
    {
        return $this->canIssueHukumanRanger() || $this->canIssueHukumanPengawas();
    }

    public function canManageHukumanRecord(Hukuman $hukuman): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->id !== $hukuman->issued_by) {
            return false;
        }

        return $hukuman->issuer_mode === 'pengawas'
            ? $this->canIssueHukumanPengawas()
            : $this->canIssueHukumanRanger();
    }

    public function isTargetHukumanPengawas(): bool
    {
        if (! $this->jabatan) {
            return false;
        }

        return in_array($this->jabatan->nama, ['Pengawas', 'Ketua Pengawas', 'Wakil Ketua Pengawas'], true);
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeHukumanTargetRanger(Builder $query): Builder
    {
        return $query
            ->where('status', true)
            ->whereNotNull('divisi_id')
            ->where(function (Builder $inner) {
                $inner->whereNull('jabatan_id')
                    ->orWhereHas('jabatan', fn (Builder $jabatan) => $jabatan->whereNotIn('nama', [
                        'Pengawas',
                        'Ketua Pengawas',
                        'Wakil Ketua Pengawas',
                    ]));
            });
    }

    /**
     * Admin issuer: semua panitia aktif termasuk pengawas.
     *
     * @param  Builder<self>  $query
     */
    public function scopeHukumanTargetAdmin(Builder $query): Builder
    {
        return $query
            ->where('status', true)
            ->whereNotNull('divisi_id');
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeHukumanTargetPengawas(Builder $query): Builder
    {
        return $query
            ->where('status', true)
            ->whereHas('jabatan', fn (Builder $jabatan) => $jabatan->whereIn('nama', [
                'Pengawas',
                'Ketua Pengawas',
                'Wakil Ketua Pengawas',
            ]));
    }

    public function initials(): string
    {
        $initials = Str::initials($this->nama, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    /**
     * Label role utama untuk tampilan UI.
     */
    public function displayRole(): string
    {
        return ucfirst($this->roles->first()?->name ?? 'peserta');
    }

    /**
     * Mendapatkan format label jabatan & divisi yang dinamis sesuai aturan kepanitiaan.
     */
    public function getFormattedDivisiJabatanAttribute(): string
    {
        if (! $this->divisi) {
            return '— / —';
        }

        if ($this->isStakeholder() && $this->jabatan) {
            return $this->jabatan->nama;
        }

        $jabatanName = strtolower($this->jabatan?->nama ?? 'anggota');

        $prefix = match ($jabatanName) {
            'ketua' => 'Koordinator ',
            'wakil' => 'Wakil Koordinator ',
            'pengawas' => 'Pengawas ',
            default => 'Anggota ',
        };

        return $prefix.$this->divisi->nama;
    }
}
