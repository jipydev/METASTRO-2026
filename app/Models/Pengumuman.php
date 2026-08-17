<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumumans';

    protected $fillable = [
        'judul',
        'isi',
        'lampiran',
        'tanggal_publish',
        'status',
        'pembuat_id',
    ];

    protected $casts = [
        'tanggal_publish' => 'datetime',
    ];

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }

    public function isPublished(): bool
    {
        return in_array(strtolower((string) $this->status), ['published', 'publish'], true);
    }

    /**
     * @param  Builder<Pengumuman>  $query
     * @return Builder<Pengumuman>
     */
    public function scopeVisibleTo($query, ?User $user)
    {
        if ($user && $user->isAdmin()) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->whereIn('status', ['published', 'Publish']);

            if ($user) {
                $q->orWhere(function ($draft) use ($user) {
                    $draft->whereIn('status', ['draft', 'Draft'])
                        ->where(function ($sub) use ($user) {
                            $sub->where('pembuat_id', $user->id);
                            if ($user->divisi_id) {
                                $sub->orWhereHas('pembuat', function ($pembuatQ) use ($user) {
                                    $pembuatQ->where('divisi_id', $user->divisi_id);
                                });
                            }
                        });
                });
            }
        });
    }

    /**
     * Edit/hapus pengumuman:
     * Admin selalu bisa. Selain itu hanya ketua, wakil, dan anggota
     * satu divisi dengan pembuat.
     */
    public function canBeManagedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        $this->loadMissing('pembuat');

        return $user->canManagePengumumanDivisi($this->pembuat?->divisi_id);
    }
}
