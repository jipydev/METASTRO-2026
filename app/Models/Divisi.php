<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi',
        'koordinator_id',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function koordinator()
    {
        return $this->belongsTo(User::class, 'koordinator_id');
    }

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class);
    }

    public function badgeClasses(): string
    {
        return self::badgeClassesFor($this->nama);
    }

    public static function badgeClassesFor(?string $nama): string
    {
        return match (strtolower(trim((string) $nama))) {
            'archivist' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border-blue-200 dark:border-blue-800',
            'chef' => 'bg-orange-50 text-orange-700 dark:bg-orange-950/60 dark:text-orange-300 border-orange-200 dark:border-orange-800',
            'chiper' => 'bg-brand-50 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300 border-brand-200 dark:border-brand-800',
            'documenter' => 'bg-pink-50 text-pink-700 dark:bg-pink-950/60 dark:text-pink-300 border-pink-200 dark:border-pink-800',
            'fundkeeper' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
            'gearmaster' => 'bg-stone-100 text-stone-700 dark:bg-stone-900/60 dark:text-stone-300 border-stone-200 dark:border-stone-700',
            'guardian' => 'bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-300 border-red-200 dark:border-red-800',
            'guider' => 'bg-teal-50 text-teal-700 dark:bg-teal-950/60 dark:text-teal-300 border-teal-200 dark:border-teal-800',
            'informer' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300 border-sky-200 dark:border-sky-800',
            'pathfinder' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border-purple-200 dark:border-purple-800',
            'ranger' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
            'rescuer' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border-rose-200 dark:border-rose-800',
            'scribe' => 'bg-amber-50 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800',
            'stakeholder' => 'bg-fuchsia-50 text-fuchsia-700 dark:bg-fuchsia-950/60 dark:text-fuchsia-300 border-fuchsia-200 dark:border-fuchsia-800',
            default => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-600',
        };
    }
}
