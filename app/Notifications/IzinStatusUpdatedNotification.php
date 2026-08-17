<?php

namespace App\Notifications;

use App\Models\PengajuanIzin;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class IzinStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public PengajuanIzin $izin,
        public string $step,
        public string $decision,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $this->izin->loadMissing('kegiatan');

        $kegiatan = $this->izin->kegiatan?->nama ?? 'kegiatan';
        $aktor = $this->step === 'ranger' ? 'Ranger' : 'Koordinator';

        if ($this->decision === 'approved' && $this->step === 'koordinator') {
            return [
                'title' => 'Izin diteruskan',
                'message' => "Pengajuan izin Anda untuk {$kegiatan} disetujui Koordinator dan diteruskan ke Ranger.",
                'url' => route('pengajuan-izin.index'),
                'type' => 'izin',
            ];
        }

        if ($this->decision === 'approved') {
            return [
                'title' => 'Izin disetujui',
                'message' => "Pengajuan izin Anda untuk {$kegiatan} telah disetujui oleh {$aktor}.",
                'url' => route('pengajuan-izin.index'),
                'type' => 'izin',
            ];
        }

        return [
            'title' => 'Izin ditolak',
            'message' => "Pengajuan izin Anda untuk {$kegiatan} ditolak oleh {$aktor}.",
            'url' => route('pengajuan-izin.index'),
            'type' => 'izin',
        ];
    }
}
