<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hukuman extends Model
{
    /** @var list<string> */
    public const KATEGORI = ['ringan', 'sedang', 'berat', 'khusus'];

    protected $table = 'hukumans';

    protected $fillable = [
        'user_id',
        'issued_by',
        'kategori',
        'issuer_mode',
        'alasan',
        'pembelaan',
        'pembelaan_at',
        'tugas_link',
        'tugas_submitted_at',
        'deadline_at',
        'selesai_at',
    ];

    protected function casts(): array
    {
        return [
            'pembelaan_at' => 'datetime',
            'tugas_submitted_at' => 'datetime',
            'deadline_at' => 'datetime',
            'selesai_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function isSelesai(): bool
    {
        return $this->selesai_at !== null;
    }

    public function isExpired(): bool
    {
        return ! $this->isSelesai() && now()->greaterThan($this->deadline_at);
    }

    public function sudahPembelaan(): bool
    {
        return $this->pembelaan_at !== null;
    }

    public function kategoriLabel(): string
    {
        return match ($this->kategori) {
            'ringan' => 'Ringan',
            'sedang' => 'Sedang',
            'berat' => 'Berat',
            'khusus' => 'Khusus',
            default => ucfirst((string) $this->kategori),
        };
    }

    public function kategoriBadgeClasses(): string
    {
        return match ($this->kategori) {
            'ringan' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300',
            'sedang' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300',
            'berat' => 'bg-orange-100 text-orange-700 dark:bg-orange-950/50 dark:text-orange-300',
            'khusus' => 'bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300',
            default => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300',
        };
    }

    public function statusBadgeClasses(): string
    {
        if ($this->isSelesai()) {
            return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300';
        }

        if ($this->isExpired()) {
            return 'bg-rose-100 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300';
        }

        if (! $this->sudahPembelaan()) {
            return 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300';
        }

        return 'bg-sky-100 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300';
    }

    public function statusLabel(): string
    {
        if ($this->isSelesai()) {
            return 'Selesai';
        }

        if ($this->isExpired()) {
            return 'Melewati Deadline';
        }

        if (! $this->sudahPembelaan()) {
            return 'Menunggu Pembelaan';
        }

        return 'Sedang Dikerjakan';
    }

    /** @param Builder<self> $query */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->whereNull('selesai_at');
    }
}
