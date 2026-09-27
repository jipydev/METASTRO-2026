<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guider extends Model
{
    /** @use HasFactory<\Database\Factories\GuiderFactory> */
    use HasFactory;

    protected $fillable = ['pembimbing_id', 'tim_id'];

    public function pembimbing()
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }

    public function tim()
    {
        return $this->belongsTo(Tim::class, 'tim_id');
    }
}
