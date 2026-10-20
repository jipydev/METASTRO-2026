<?php

namespace App\Services;

use App\Models\Hukuman;
use App\Models\Kegiatan;
use App\Models\Notulensi;
use App\Models\PengajuanIzin;
use App\Models\PengumpulanTugas;
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
            ->reject(fn(User $ranger) => $ranger->id === $presensi->user_id);

        if ($rangers->isNotEmpty()) {
            Notification::send($rangers, new PresensiRecordedNotification($presensi, 'ranger'));
        }
    }

    public function presensiImported(Kegiatan $kegiatan, int $count, ?int $exceptUserId = null): void
    {
        $rangers = User::activeRangers()
            ->reject(fn(User $ranger) => $ranger->id === $exceptUserId);

        if ($rangers->isEmpty()) {
            return;
        }

        Notification::send($rangers, new ReminderNotification(
            'Impor presensi',
            "{$count} kehadiran diimpor untuk {$kegiatan->nama}.",
            route('dashboard.presensi.monitoring', ['kegiatan_id' => $kegiatan->id]),
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
            route('dashboard.pengumuman.index'),
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
            route('dashboard.notulensi.index'),
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
            route('dashboard.kegiatan.index'),
            'kegiatan',
        ), $exceptUserId);
    }

    public function presensiOpened(Kegiatan $kegiatan, ?int $exceptUserId = null): void
    {
        $this->notifyPanitia(new ReminderNotification(
            'Presensi dibuka',
            "Sesi presensi untuk {$kegiatan->nama} sudah dibuka. Jangan lupa absen.",
            route('dashboard.presensi.index'),
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

    public function hukumanUpdated(Hukuman $hukuman): void
    {
        $hukuman->loadMissing('user');

        if ($hukuman->user) {
            $hukuman->user->notify(new HukumanNotification($hukuman, 'updated', 'target'));
        }
    }

    public function hukumanCancelled(Hukuman $hukuman, User $previousTarget): void
    {
        $previousTarget->notify(new HukumanNotification($hukuman, 'dibatalkan', 'target'));
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

    // =========================================================================
    // TUGAS NOTIFICATIONS (METHODS BARU)
    // =========================================================================

    public function tugasUniversalSubmitted(PengumpulanTugas $submission, User $recipient): void
    {
        $submission->loadMissing('tugas');
        $judulTugas = $submission->tugas?->judul ?? 'Tugas';
        $namaPengumpul = $submission->original_participant_name ?? 'Peserta';

        $recipient->notify(new ReminderNotification(
            'Pengumpulan Tugas Baru',
            "{$namaPengumpul} telah mengumpulkan tugas \"{$judulTugas}\".",
            route('dashboard.pengumpulan-tugas.index', ['tugas_id' => $submission->tugas_id]),
            'tugas',
        ));
    }

    public function tugasIndividuSubmitted(PengumpulanTugas $submission, User $guider): void
    {
        $submission->loadMissing('tugas');
        $judulTugas = $submission->tugas?->judul ?? 'Tugas';
        $namaPengumpul = $submission->original_participant_name ?? 'Peserta';

        $guider->notify(new ReminderNotification(
            'Tugas Individu Dikumpulkan',
            "Bimbingan Anda, {$namaPengumpul}, mengumpulkan tugas \"{$judulTugas}\".",
            route('dashboard.pengumpulan-tugas.index', ['tugas_id' => $submission->tugas_id]),
            'tugas',
        ));
    }

    public function tugasTimSubmitted(PengumpulanTugas $submission, User $recipient): void
    {
        $submission->loadMissing(['tugas', 'tim']);
        $judulTugas = $submission->tugas?->judul ?? 'Tugas';
        $namaTim = $submission->tim?->nama ?? 'Tim';

        $recipient->notify(new ReminderNotification(
            'Tugas Tim Dikumpulkan',
            "Tim {$namaTim} telah mengumpulkan tugas \"{$judulTugas}\".",
            route('dashboard.pengumpulan-tugas.index', ['tugas_id' => $submission->tugas_id]),
            'tugas',
        ));
    }

    public function tugasAngkatanSubmitted(PengumpulanTugas $submission, User $recipient): void
    {
        $submission->loadMissing('tugas');
        $judulTugas = $submission->tugas?->judul ?? 'Tugas';

        $recipient->notify(new ReminderNotification(
            'Tugas Angkatan Dikumpulkan',
            "Tugas angkatan \"{$judulTugas}\" telah dikumpulkan.",
            route('dashboard.pengumpulan-tugas.index', ['tugas_id' => $submission->tugas_id]),
            'tugas',
        ));
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

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
            ->reject(fn(User $ranger) => $ranger->id === $exceptUserId);

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

    public function tugasReviewed(PengumpulanTugas $submission): void
    {
        $submission->loadMissing(['tugas', 'peserta']);
        $peserta = $submission->peserta;

        if (! $peserta) {
            return;
        }

        $judulTugas = $submission->tugas?->judul ?? 'Tugas';
        $statusLabel = $submission->status === 'reviewed' ? 'disetujui (reviewed)' : 'ditolak / perlu revisi';

        $peserta->notify(new ReminderNotification(
            'Status Tugas Diperbarui',
            "Pengumpulan untuk tugas \"{$judulTugas}\" telah {$statusLabel}.",
            route('peserta.tugas-kumpulkan', $submission->tugas_id),
            'tugas',
        ));
    }
}
