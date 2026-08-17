<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\Kegiatan;
use App\Models\PengajuanIzin;
use App\Models\Presensi;
use App\Models\User;
use App\Notifications\IzinStatusUpdatedNotification;
use App\Notifications\IzinSubmittedNotification;
use App\Notifications\PresensiRecordedNotification;
use App\Notifications\ReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private Divisi $rangerDivisi;

    private Divisi $chefDivisi;

    private Divisi $archivistDivisi;

    private Jabatan $ketua;

    private Jabatan $anggota;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rangerDivisi = Divisi::create(['nama' => 'Ranger']);
        $this->chefDivisi = Divisi::create(['nama' => 'Chef']);
        $this->archivistDivisi = Divisi::create(['nama' => 'Archivist']);
        $this->ketua = Jabatan::create(['nama' => 'Ketua']);
        $this->anggota = Jabatan::create(['nama' => 'Anggota']);
    }

    public function test_recording_attendance_notifies_the_attendee_and_rangers(): void
    {
        Notification::fake();

        $scanner = $this->userIn($this->archivistDivisi, $this->anggota);
        $peserta = $this->userIn($this->chefDivisi, $this->anggota);
        $ranger = $this->userIn($this->rangerDivisi, $this->anggota);
        $kegiatan = $this->openKegiatan();

        $response = $this->actingAs($scanner)->postJson(route('api.scan.store'), [
            'user_id' => $peserta->id,
            'kegiatan_id' => $kegiatan->id,
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        Notification::assertSentTo($peserta, PresensiRecordedNotification::class, function (PresensiRecordedNotification $notification) {
            return $notification->audience === 'self';
        });
        Notification::assertSentTo($ranger, PresensiRecordedNotification::class, function (PresensiRecordedNotification $notification) {
            return $notification->audience === 'ranger';
        });
        Notification::assertNotSentTo($scanner, PresensiRecordedNotification::class);
    }

    public function test_submitting_izin_notifies_division_coordinator(): void
    {
        Notification::fake();
        Storage::fake('public');

        $pemohon = $this->userIn($this->chefDivisi, $this->anggota);
        $koordinator = $this->userIn($this->chefDivisi, $this->ketua);
        $ranger = $this->userIn($this->rangerDivisi, $this->anggota);
        $kegiatan = $this->openKegiatan();

        $response = $this->actingAs($pemohon)->post(route('pengajuan-izin.store'), [
            'kegiatan_id' => $kegiatan->id,
            'jenis_izin' => 'izin',
            'alasan' => 'Ada keperluan keluarga.',
            'surat_izin' => UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('pengajuan-izin.index'));

        Notification::assertSentTo($koordinator, IzinSubmittedNotification::class, function (IzinSubmittedNotification $notification) {
            return $notification->audience === 'koordinator';
        });
        Notification::assertNotSentTo($ranger, IzinSubmittedNotification::class);
        Notification::assertNotSentTo($pemohon, IzinSubmittedNotification::class);
    }

    public function test_coordinator_approval_notifies_applicant_and_rangers(): void
    {
        Notification::fake();

        $pemohon = $this->userIn($this->chefDivisi, $this->anggota);
        $koordinator = $this->userIn($this->chefDivisi, $this->ketua);
        $ranger = $this->userIn($this->rangerDivisi, $this->anggota);
        $izin = $this->pendingIzin($pemohon);

        $response = $this->actingAs($koordinator)->post(route('pengajuan-izin.approve', $izin));

        $response->assertRedirect();

        Notification::assertSentTo($pemohon, IzinStatusUpdatedNotification::class, function (IzinStatusUpdatedNotification $notification) {
            return $notification->step === 'koordinator' && $notification->decision === 'approved';
        });
        Notification::assertSentTo($ranger, IzinSubmittedNotification::class, function (IzinSubmittedNotification $notification) {
            return $notification->audience === 'ranger';
        });
    }

    public function test_ranger_approval_notifies_the_applicant(): void
    {
        Notification::fake();

        $pemohon = $this->userIn($this->chefDivisi, $this->anggota);
        $ranger = $this->userIn($this->rangerDivisi, $this->anggota);
        $izin = $this->pendingIzin($pemohon, [
            'status_koordinator' => 'approved',
            'status' => 'diproses',
        ]);

        $response = $this->actingAs($ranger)->post(route('pengajuan-izin.approve', $izin));

        $response->assertRedirect();
        $this->assertSame('approved', $izin->fresh()->status);

        Notification::assertSentTo($pemohon, IzinStatusUpdatedNotification::class, function (IzinStatusUpdatedNotification $notification) {
            return $notification->step === 'ranger' && $notification->decision === 'approved';
        });
    }

    public function test_notification_bell_renders_on_dashboard(): void
    {
        $user = User::factory()->create();
        $kegiatan = $this->openKegiatan();
        $presensi = Presensi::create([
            'user_id' => $user->id,
            'kegiatan_id' => $kegiatan->id,
            'status' => 'hadir',
            'jam_tap' => now()->setTime(7, 50),
        ]);
        $user->notify(new PresensiRecordedNotification($presensi, 'self'));

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Anda hadir di RABES 1 dengan tepat waktu.', false)
            ->assertSee('Notifikasi', false);
    }

    public function test_late_attendance_notification_includes_minutes(): void
    {
        $user = User::factory()->create();
        $kegiatan = $this->openKegiatan();
        $presensi = Presensi::create([
            'user_id' => $user->id,
            'kegiatan_id' => $kegiatan->id,
            'status' => 'hadir',
            'jam_tap' => now()->setTime(8, 17),
        ]);

        $data = (new PresensiRecordedNotification($presensi, 'self'))->toDatabase($user);

        $this->assertSame('Anda hadir di RABES 1 tetapi telat 17 menit.', $data['message']);
    }

    public function test_opening_a_notification_marks_it_read(): void
    {
        $user = User::factory()->create();
        $kegiatan = $this->openKegiatan();
        $presensi = Presensi::create([
            'user_id' => $user->id,
            'kegiatan_id' => $kegiatan->id,
            'status' => 'hadir',
            'jam_tap' => now(),
        ]);
        $user->notify(new PresensiRecordedNotification($presensi, 'self'));
        $notification = $user->notifications()->first();

        $this->actingAs($user)
            ->get(route('notifications.show', $notification->id))
            ->assertRedirect(route('presensi.history'));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_publishing_pengumuman_notifies_other_panitia(): void
    {
        Notification::fake();

        $pembuat = $this->userIn($this->archivistDivisi, $this->anggota);
        $panitia = $this->userIn($this->chefDivisi, $this->anggota);

        $this->actingAs($pembuat)->post(route('pengumuman.store'), [
            'judul' => 'Briefing panitia',
            'isi' => 'Harap hadir tepat waktu.',
            'status' => 'published',
        ])->assertRedirect();

        Notification::assertSentTo($panitia, ReminderNotification::class, function ($notification) {
            return $notification->type === 'pengumuman' && $notification->title === 'Pengumuman baru';
        });
        Notification::assertNotSentTo($pembuat, ReminderNotification::class);
    }

    public function test_creating_kegiatan_notifies_other_panitia(): void
    {
        Notification::fake();

        $pembuat = $this->userIn($this->archivistDivisi, $this->anggota);
        $panitia = $this->userIn($this->chefDivisi, $this->anggota);

        $this->actingAs($pembuat)->post(route('kegiatan.store'), [
            'nama' => 'Rapat Pleno',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '19:00',
            'tempat' => 'Sekretariat',
        ])->assertRedirect();

        Notification::assertSentTo($panitia, ReminderNotification::class, function ($notification) {
            return $notification->type === 'kegiatan';
        });
        Notification::assertNotSentTo($pembuat, ReminderNotification::class);
    }

    public function test_creating_notulensi_notifies_other_panitia(): void
    {
        Notification::fake();

        $pembuat = $this->userIn($this->archivistDivisi, $this->anggota);
        $panitia = $this->userIn($this->chefDivisi, $this->anggota);

        $this->actingAs($pembuat)->post(route('notulensi.store'), [
            'judul' => 'Hasil pleno',
            'isi' => 'Keputusan rapat sudah dicatat.',
        ])->assertRedirect();

        Notification::assertSentTo($panitia, ReminderNotification::class, function ($notification) {
            return $notification->type === 'notulensi';
        });
        Notification::assertNotSentTo($pembuat, ReminderNotification::class);
    }

    private function userIn(Divisi $divisi, Jabatan $jabatan): User
    {
        return User::factory()->create([
            'divisi_id' => $divisi->id,
            'jabatan_id' => $jabatan->id,
        ]);
    }

    private function openKegiatan(): Kegiatan
    {
        return Kegiatan::create([
            'nama' => 'RABES 1',
            'tempat' => 'Aula',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '12:00:00',
            'presensi_mulai' => now()->subHour(),
            'presensi_selesai' => now()->addHour(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function pendingIzin(User $pemohon, array $overrides = []): PengajuanIzin
    {
        return PengajuanIzin::create(array_merge([
            'user_id' => $pemohon->id,
            'kegiatan_id' => $this->openKegiatan()->id,
            'tanggal_izin' => now()->toDateString(),
            'jenis_izin' => 'izin',
            'alasan' => 'Ada keperluan keluarga.',
            'status_koordinator' => 'pending',
            'status_ranger' => 'pending',
            'status' => 'pending',
        ], $overrides));
    }
}
