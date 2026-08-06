<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class IzinStatusUpdatedNotification extends Notification
{
    use Queueable;

    protected $absensi;
    protected $validator; // 'Koordinator Divisi' or 'Ranger'

    public function __construct($absensi, $validator)
    {
        $this->absensi = $absensi;
        $this->validator = $validator;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $status = str_contains($this->absensi->status_validasi, 'ditolak') ? 'Ditolak' : 'Disetujui';
        
        return [
            'absensi_id' => $this->absensi->id,
            'message' => "Pengajuan izin Anda telah $status oleh {$this->validator}.",
            'url' => route('dashboard'), // Staff check dashboard
        ];
    }
}
