<?php

namespace App\Notifications;

use App\Models\Hukuman;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class HukumanNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Hukuman $hukuman,
        public string $event,
        public string $audience = 'target',
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
        $this->hukuman->loadMissing(['user', 'issuer']);

        $target = $this->hukuman->user?->nama ?? 'Panitia';
        $kategori = $this->hukuman->kategoriLabel();

        return match ($this->event) {
            'updated' => [
                'title' => 'Hukuman diperbarui',
                'message' => "Detail hukuman kategori {$kategori} telah diperbarui. Periksa kembali alasan dan tugasnya.",
                'url' => route('hukuman.show', $this->hukuman),
                'type' => 'hukuman',
            ],
            'dibatalkan' => [
                'title' => 'Hukuman dibatalkan',
                'message' => "Hukuman kategori {$kategori} yang diberikan kepada Anda telah dibatalkan.",
                'url' => route('hukuman.index'),
                'type' => 'hukuman',
            ],
            'pembelaan' => [
                'title' => 'Pembelaan hukuman',
                'message' => "{$target} mengajukan pembelaan untuk hukuman kategori {$kategori}.",
                'url' => route('hukuman.show', $this->hukuman),
                'type' => 'hukuman',
            ],
            'tugas' => [
                'title' => 'Tugas hukuman dikirim',
                'message' => "{$target} mengirim link tugas hukuman kategori {$kategori}.",
                'url' => route('hukuman.show', $this->hukuman),
                'type' => 'hukuman',
            ],
            'selesai' => [
                'title' => 'Hukuman selesai',
                'message' => "{$target} menandai hukuman kategori {$kategori} sebagai selesai.",
                'url' => route('hukuman.show', $this->hukuman),
                'type' => 'hukuman',
            ],
            default => [
                'title' => 'Hukuman diterbitkan',
                'message' => "Anda menerima hukuman kategori {$kategori}. Segera ajukan pembelaan dan kerjakan tugas dalam 2×24 jam.",
                'url' => route('hukuman.show', $this->hukuman),
                'type' => 'hukuman',
            ],
        };
    }
}
