<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
