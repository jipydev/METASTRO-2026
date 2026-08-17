<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $nama
 * @property string|null $deskripsi
 */
class Jabatan extends Model
{
    use HasFactory;

    /** @var list<string> */
    public const OPERATIONAL = [
        'Ketua',
        'Wakil',
        'Anggota',
        'Pengawas',
    ];

    /** @var list<string> */
    public const STAKEHOLDER = [
        'Person in Charge',
        'Ketua Pelaksana',
        'Wakil Ketua Pelaksana',
        'Ketua Pengawas',
        'Wakil Ketua Pengawas',
        'Steering Committee',
    ];

    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /** @param Builder<self> $query */
    public function scopeOperational(Builder $query): Builder
    {
        return $query->whereIn('nama', self::OPERATIONAL);
    }

    /** @param Builder<self> $query */
    public function scopeStakeholder(Builder $query): Builder
    {
        return $query->whereIn('nama', self::STAKEHOLDER);
    }

    public static function isStakeholderName(?string $nama): bool
    {
        return in_array((string) $nama, self::STAKEHOLDER, true);
    }

    public static function isOperationalName(?string $nama): bool
    {
        return in_array((string) $nama, self::OPERATIONAL, true);
    }

    public static function matchesDivisi(?string $divisiNama, ?string $jabatanNama): bool
    {
        if ($jabatanNama === null || $jabatanNama === '') {
            return true;
        }

        if (strcasecmp((string) $divisiNama, 'Stakeholder') === 0) {
            return self::isStakeholderName($jabatanNama);
        }

        return self::isOperationalName($jabatanNama);
    }

    /** @return Collection<int, self> */
    public static function orderedOperational(): Collection
    {
        return self::query()
            ->operational()
            ->get()
            ->sortBy(fn (self $jabatan) => array_search($jabatan->nama, self::OPERATIONAL, true))
            ->values();
    }

    /** @return Collection<int, self> */
    public static function orderedStakeholder(): Collection
    {
        return self::query()
            ->stakeholder()
            ->get()
            ->sortBy(fn (self $jabatan) => array_search($jabatan->nama, self::STAKEHOLDER, true))
            ->values();
    }
}
