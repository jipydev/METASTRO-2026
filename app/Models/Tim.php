<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tim extends Model
{
    /** @use HasFactory<\Database\Factories\TimFactory> */
    use HasFactory;

    protected $fillable = ['nama', 'slug'];
}
