<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListPanitia extends Model
{
    protected $fillable = [
        'user_id',
        'rapat_id',
        'scanned_by',
        'jam_tap',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rapat()
    {
        return $this->belongsTo(Rapat::class);
    }

    public function scanner()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
