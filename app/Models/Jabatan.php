<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nama_jabatan
 * @property string|null $deskripsi
 */
class Jabatan extends Model
{
    use HasFactory;

    protected $table = 'jabatan';

    protected $fillable = [
        'nama_jabatan',
        'deskripsi',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
