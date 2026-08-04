<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListPanitia extends Model
{
    protected $fillable = ['nama', 'divisi', 'jam_tap', 'tanggal', 'status'];
}