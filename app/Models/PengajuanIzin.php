<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PengajuanIzin extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_izins';

    protected $fillable = [
        'user_id',
        'kegiatan_id',
        'tanggal_izin',
        'jenis_izin',
        'alasan',
        'surat_izin',
        'bukti',
        'status_koordinator',
        'reviewed_by_koordinator',
        'reviewed_at_koordinator',
        'catatan_koordinator',
        'status_ranger',
        'reviewed_by_ranger',
        'reviewed_at_ranger',
        'catatan_ranger',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_izin' => 'date',
            'reviewed_at_koordinator' => 'datetime',
            'reviewed_at_ranger' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi Eloquent
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function reviewerKoordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_koordinator');
    }

    public function reviewerRanger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_ranger');
    }

    public function presensi(): HasOne
    {
        return $this->hasOne(Presensi::class, 'pengajuan_izin_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper State Checks
    |--------------------------------------------------------------------------
    */

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function canBeDeletedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $isPanitia = $user->divisi_id || $user->jabatan_id;

        return $isPanitia && $this->user_id === $user->id;
    }

    /**
     * Tahap review yang sedang dipegang user (koordinator / ranger), atau null.
     */
    public function currentReviewStep(?User $user): ?string
    {
        if (! $user || in_array($this->status, ['approved', 'rejected'], true)) {
            return null;
        }

        $this->loadMissing('user');

        if ($user->canApproveKoordinatorIzin($this) && $this->status_koordinator === 'pending') {
            return 'koordinator';
        }

        if ($user->canApproveRangerIzin() && $this->status_koordinator === 'approved' && $this->status_ranger === 'pending') {
            return 'ranger';
        }

        return null;
    }

    /**
     * Data untuk modal detail pengajuan izin.
     */
    public function modalPayload(bool $canAct = false): array
    {
        $auth = auth()->user();

        return [
            'pemohon' => $this->user?->nama ?? 'Pengguna Dihapus',
            'nim' => $this->user?->nim ?? '-',
            'divisi' => $this->user?->formatted_divisi_jabatan ?? ($this->user?->divisi?->nama ?? 'Tanpa Divisi'),
            'kegiatan' => $this->kegiatan?->nama ?? 'Kegiatan Telah Dihapus',
            'tanggal' => $this->tanggal_izin?->translatedFormat('d M Y') ?? '-',
            'jenis' => $this->jenis_izin === 'sakit' ? 'Sakit' : 'Izin',
            'alasan' => $this->alasan,
            'surat' => $this->surat_izin ? asset('storage/'.$this->surat_izin) : null,
            'bukti' => $this->bukti ? asset('storage/'.$this->bukti) : null,
            'statusKoor' => $this->status_koordinator,
            'statusRanger' => $this->status_ranger,
            'status' => $this->status,
            'catatanKoor' => $this->catatan_koordinator,
            'catatanRanger' => $this->catatan_ranger,
            'reviewerKoor' => $this->reviewerKoordinator?->nama,
            'reviewerRanger' => $this->reviewerRanger?->nama,
            'reviewedAtKoor' => $this->reviewed_at_koordinator?->translatedFormat('d M Y H:i'),
            'reviewedAtRanger' => $this->reviewed_at_ranger?->translatedFormat('d M Y H:i'),
            'approveUrl' => route('pengajuan-izin.approve', $this),
            'rejectUrl' => route('pengajuan-izin.reject', $this),
            'deleteUrl' => route('pengajuan-izin.destroy', $this),
            'canAct' => $canAct,
            'canDelete' => $this->canBeDeletedBy($auth instanceof User ? $auth : null),
            'approveLabel' => $this->currentReviewStep($auth instanceof User ? $auth : null) === 'ranger'
                ? 'Setujui sebagai Ranger'
                : 'Setujui sebagai Koordinator',
            'rejectLabel' => $this->currentReviewStep($auth instanceof User ? $auth : null) === 'ranger'
                ? 'Tolak sebagai Ranger'
                : 'Tolak sebagai Koordinator',
            'catatan' => '',
        ];
    }
}
