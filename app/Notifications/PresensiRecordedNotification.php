<?php

namespace App\Notifications;

use App\Models\Presensi;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Route;

class PresensiRecordedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Presensi $presensi,
        public string $audience = 'self',
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
        $this->presensi->loadMissing(['user', 'kegiatan']);

        $kegiatan = $this->presensi->kegiatan?->nama ?? 'kegiatan';

        if ($this->audience === 'self') {
            $url = Route::has('dashboard.presensi.history')
                ? route('dashboard.presensi.history')
                : (Route::has('presensi.history') ? route('presensi.history') : url('/dashboard/presensi/riwayat'));

            return [
                'title' => 'Presensi berhasil',
                'message' => $this->selfMessage($kegiatan),
                'url' => $url,
                'type' => 'presensi',
            ];
        }

        $nama = $this->presensi->user?->nama ?? 'Seseorang';
        $params = array_filter(['kegiatan_id' => $this->presensi->kegiatan_id]);
        $monitoringUrl = Route::has('dashboard.presensi.monitoring')
            ? route('dashboard.presensi.monitoring', $params)
            : (Route::has('presensi.monitoring') ? route('presensi.monitoring', $params) : url('/dashboard/presensi/monitoring'));

        return [
            'title' => 'Presensi baru',
            'message' => "{$nama} telah absen pada kegiatan {$kegiatan}.",
            'url' => $monitoringUrl,
            'type' => 'presensi',
        ];
    }

    private function selfMessage(string $kegiatan): string
    {
        if ($this->presensi->status !== 'hadir') {
            return "Anda berhasil absen pada kegiatan {$kegiatan}.";
        }

        if ($this->presensi->isTerlambat()) {
            $menit = $this->presensi->menitTerlambat();

            return "Anda hadir di {$kegiatan} tetapi telat {$menit} menit.";
        }

        return "Anda hadir di {$kegiatan} dengan tepat waktu.";
    }
}
