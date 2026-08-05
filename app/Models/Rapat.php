<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rapat extends Model
{
    protected $fillable = ['judul', 'tanggal', 'jam', 'tempat', 'hadir', 'total'];

    public function listPanitias()
    {
        return $this->hasMany(ListPanitia::class);
    }
}