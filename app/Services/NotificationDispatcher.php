<?php

namespace App\Services;

use App\Models\Hukuman;
use App\Models\Kegiatan;
use App\Models\Notulensi;
use App\Models\PengajuanIzin;
use App\Models\Pengumuman;
use App\Models\Presensi;
use App\Models\User;
use App\Notifications\HukumanNotification;
use App\Notifications\IzinStatusUpdatedNotification;
use App\Notifications\IzinSubmittedNotification;
use App\Notifications\PresensiRecordedNotification;
use App\Notifications\ReminderNotification;
use Illuminate\Support\Facades\Notification;

class NotificationDispatcher
{
    public function presensiRecorded(Presensi $presensi, bool $notifyRangers = true): void
    {
        $presensi->loadMissing(['user.divisi', 'kegiatan']);

        if ($presensi->user) {
            $presensi->user->notify(new PresensiRecordedNotification($presensi, 'self'));
        }

        if (! $notifyRangers) {
            return;
        }

        $rangers = User::activeRangers()
            ->reject(fn (User $ranger) => $ranger->id === $presensi->user_id);

        if ($rangers->isNotEmpty()) {
            Notification::send($rangers, new PresensiRecordedNotification($presensi, 'ranger'));
        }
    }

    public function presensiImported(Kegiatan $kegiatan, int $count, ?int $exceptUserId = null): void
    {
        $rangers = User::activeRangers()
            ->reject(fn (User $ranger) => $ranger->id === $exceptUserId);

        if ($rangers->isEmpty()) {
            return;
        }

        Notification::send($rangers, new ReminderNotification(
            'Impor presensi',
            "{$count} kehadiran diimpor untuk {$kegiatan->nama}.",
            route('presensi.monitoring', ['kegiatan_id' => $kegiatan->id]),
            'presensi',
        ));
    }

    public function izinSubmitted(PengajuanIzin $izin): void
    {
        $izin->loadMissing(['user.divisi', 'user.jabatan', 'kegiatan']);
        $pemohon = $izin->user;

        if (! $pemohon) {
            return;
        }

        if ($izin->status_koordinator === 'approved' || $pemohon->skipsKoordinatorIzinReview()) {
            $this->notifyRangers($izin, $pemohon->id);

            return;
        }

        $koordinators = User::koordinatorsOf($pemohon->divisi_id, $pemohon->id);

        if ($koordinators->isNotEmpty()) {
            Notification::send($koordinators, new IzinSubmittedNotification($izin, 'koordinator'));
        }
    }

    public function izinReviewed(PengajuanIzin $izin, string $step, string $decision): void
    {
        $izin->loadMissing(['user', 'kegiatan']);

        if ($izin->user) {
            $izin->user->notify(new IzinStatusUpdatedNotification($izin, $step, $decision));
        }

        if ($step === 'koordinator' && $decision === 'approved') {
            $this->notifyRangers($izin, $izin->user_id);
        }
    }

    public function pengumumanPublished(Pengumuman $pengumuman, ?int $exceptUserId = null): void
    {
        if (! $pengumuman->isPublished()) {
            return;
        }

        $this->notifyPanitia(new ReminderNotification(
            'Pengumuman baru',
            $pengumuman->judul,
            route('pengumuman.index'),
            'pengumuman',
        ), $exceptUserId);
    }

    public function notulensiCreated(Notulensi $notulensi, ?int $exceptUserId = null): void
    {
        $notulensi->loadMissing('kegiatan');
        $kegiatan = $notulensi->kegiatan?->nama;
        $message = $kegiatan
            ? "Notulensi \"{$notulensi->judul}\" untuk {$kegiatan} sudah tersedia."
            : "Notulensi \"{$notulensi->judul}\" sudah tersedia.";

        $this->notifyPanitia(new ReminderNotification(
            'Notulensi baru',
            $message,
            route('notulensi.index'),
            'notulensi',
        ), $exceptUserId);
    }

    public function kegiatanCreated(Kegiatan $kegiatan, ?int $exceptUserId = null): void
    {
        $tanggal = $kegiatan->tanggal?->translatedFormat('d M Y');
        $waktu = $kegiatan->waktu_mulai ? substr((string) $kegiatan->waktu_mulai, 0, 5) : null;
        $jadwal = collect([$tanggal, $waktu ? "pukul {$waktu}" : null])->filter()->implode(' ');
        $tempat = $kegiatan->tempat ? " di {$kegiatan->tempat}" : '';

        $this->notifyPanitia(new ReminderNotification(
            'Kegiatan baru',
            "{$kegiatan->nama} dijadwalkan {$jadwal}{$tempat}.",
            route('kegiatan.index'),
            'kegiatan',
        ), $exceptUserId);
    }

    public function presensiOpened(Kegiatan $kegiatan, ?int $exceptUserId = null): void
    {
        $this->notifyPanitia(new ReminderNotification(
            'Presensi dibuka',
            "Sesi presensi untuk {$kegiatan->nama} sudah dibuka. Jangan lupa absen.",
            route('presensi.index'),
            'presensi',
        ), $exceptUserId);
    }

    public function hukumanIssued(Hukuman $hukuman): void
    {
        $hukuman->loadMissing('user');

        if ($hukuman->user) {
            $hukuman->user->notify(new HukumanNotification($hukuman, 'issued', 'target'));
        }
    }

    public function hukumanPembelaanSubmitted(Hukuman $hukuman): void
    {
        $this->notifyHukumanIssuer($hukuman, 'pembelaan');
    }

    public function hukumanTugasSubmitted(Hukuman $hukuman): void
    {
        $this->notifyHukumanIssuer($hukuman, 'tugas');
    }

    public function hukumanCompleted(Hukuman $hukuman): void
    {
        $this->notifyHukumanIssuer($hukuman, 'selesai');
    }

    private function notifyPanitia(ReminderNotification $notification, ?int $exceptUserId = null): void
    {
        $recipients = User::activePanitia($exceptUserId);

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, $notification);
    }

    private function notifyRangers(PengajuanIzin $izin, ?int $exceptUserId = null): void
    {
        $rangers = User::activeRangers()
            ->reject(fn (User $ranger) => $ranger->id === $exceptUserId);

        if ($rangers->isEmpty()) {
            return;
        }

        Notification::send($rangers, new IzinSubmittedNotification($izin, 'ranger'));
    }

    private function notifyHukumanIssuer(Hukuman $hukuman, string $event): void
    {
        $hukuman->loadMissing('issuer');

        if (! $hukuman->issuer) {
            return;
        }

        $hukuman->issuer->notify(new HukumanNotification($hukuman, $event, 'issuer'));
    }
}
