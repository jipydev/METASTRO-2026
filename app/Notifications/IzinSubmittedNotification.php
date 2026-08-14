<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class IzinSubmittedNotification extends Notification
{
    use Queueable;

    protected $absensi;

    public function __construct($absensi)
    {
        $this->absensi = $absensi;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $status_msg = '';
        if ($this->absensi->status_validasi == 'menunggu_koordinator') {
            $status_msg = 'Pengajuan izin baru dari ' . $this->absensi->user->name . ' menunggu validasi Anda.';
        } elseif ($this->absensi->status_validasi == 'menunggu_ranger') {
            $status_msg = 'Pengajuan izin dari ' . $this->absensi->user->name . ' telah disetujui Koordinator dan menunggu validasi Anda.';
        }

        return [
            'absensi_id' => $this->absensi->id,
            'message' => $status_msg,
            'url' => $this->absensi->status_validasi == 'menunggu_koordinator' ? route('izin.koordinator') : route('izin.ranger'),
        ];
    }
}
