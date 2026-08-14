<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $requested_role
 * @property int|null $requested_divisi_id
 * @property int|null $requested_jabatan_id
 * @property string $status
 * @property string|null $admin_note
 * @property int|null $reviewed_by
 * @property \Illuminate\Support\Carbon|null $reviewed_at
 * @property \App\Models\User $user
 * @property \App\Models\Divisi|null $requestedDivisi
 * @property \App\Models\Jabatan|null $requestedJabatan
 * @property \App\Models\User|null $reviewer
 */
class RoleRequest extends Model
{
    use HasFactory;

    protected $table = 'role_requests';

    protected $fillable = [
        'user_id',
        'requested_role',
        'requested_divisi_id',
        'requested_jabatan_id',
        'status',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requestedDivisi()
    {
        return $this->belongsTo(Divisi::class, 'requested_divisi_id');
    }

    public function requestedJabatan()
    {
        return $this->belongsTo(Jabatan::class, 'requested_jabatan_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
