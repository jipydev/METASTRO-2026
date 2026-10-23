<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    /** @use HasFactory<\Database\Factories\TugasFactory> */
    use HasFactory;
    public const DAYS = ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'];

    protected $fillable = [
        'nomor',
        'judul',
        'deskripsi',
        'status',
        'jenis',
        'day',
        'tenggat_waktu',
        'pembuat_id'
    ];

    /**
     * Generate the next available 3-digit nomor (e.g. 001, 002, 003).
     */
    public static function generateNextNomor(): string
    {
        $allNumbers = self::query()
            ->whereNotNull('nomor')
            ->where('nomor', '!=', '')
            ->pluck('nomor')
            ->map(function ($num) {
                return is_numeric($num) ? (int) $num : 0;
            })
            ->filter(fn($n) => $n > 0)
            ->all();

        $next = empty($allNumbers) ? 1 : (max($allNumbers) + 1);

        // Ensure 3-digit format and check uniqueness in case of gaps
        while (self::where('nomor', str_pad((string) $next, 3, '0', STR_PAD_LEFT))->exists()) {
            $next++;
        }

        return str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    protected $table = 'tugases';

    protected function casts(): array
    {
        return [
            'tenggat_waktu' => 'datetime',
        ];
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }

    public function pengumpulans()
    {
        return $this->hasMany(PengumpulanTugas::class, 'tugas_id');
    }
}