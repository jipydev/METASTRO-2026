<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tim extends Model
{
    /** @use HasFactory<\Database\Factories\TimFactory> */
    use HasFactory;

    protected $fillable = ['nama', 'slug'];

    public function guiders(): HasMany
    {
        return $this->hasMany(Guider::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'anggota_tims', 'tim_id', 'anggota_id');
    }
}
