<?php

namespace App\Notifications;

use App\Models\PengajuanIzin;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class IzinSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public PengajuanIzin $izin,
        public string $audience = 'koordinator',
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
        $this->izin->loadMissing(['user', 'kegiatan']);

        $nama = $this->izin->user?->nama ?? 'Seseorang';
        $kegiatan = $this->izin->kegiatan?->nama ?? 'kegiatan';

        $message = $this->audience === 'ranger'
            ? "Pengajuan izin dari {$nama} untuk {$kegiatan} menunggu verifikasi Ranger."
            : "Pengajuan izin baru dari {$nama} untuk {$kegiatan} menunggu verifikasi Anda.";

        return [
            'title' => 'Pengajuan izin baru',
            'message' => $message,
            'url' => route('pengajuan-izin.review'),
            'type' => 'izin',
        ];
    }
}
