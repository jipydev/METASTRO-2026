<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\User;
use App\Notifications\PresensiRecordedNotification;
use App\Notifications\ReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ManualAttendanceTest extends TestCase
{
    use RefreshDatabase;

    private Divisi $archivistDivisi;

    private Divisi $chefDivisi;

    private Divisi $rangerDivisi;

    private Jabatan $anggota;

    protected function setUp(): void
    {
        parent::setUp();

        $this->archivistDivisi = Divisi::create(['nama' => 'Archivist']);
        $this->chefDivisi = Divisi::create(['nama' => 'Chef']);
        $this->rangerDivisi = Divisi::create(['nama' => 'Ranger']);
        $this->anggota = Jabatan::create(['nama' => 'Anggota']);
    }

    public function test_archivist_can_record_attendance_manually_when_session_is_closed(): void
    {
        Notification::fake();

        $archivist = $this->userIn($this->archivistDivisi);
        $peserta = $this->userIn($this->chefDivisi);
        $kegiatan = $this->closedKegiatan();

        $this->actingAs($archivist)
            ->post(route('presensi.store'), [
                'kegiatan_id' => $kegiatan->id,
                'user_id' => $peserta->id,
                'jam_tap' => '08:15',
            ])
            ->assertRedirect(route('presensi.monitoring', ['kegiatan_id' => $kegiatan->id]));

        $this->assertDatabaseHas('presensis', [
            'user_id' => $peserta->id,
            'kegiatan_id' => $kegiatan->id,
            'scanned_by' => $archivist->id,
            'status' => 'hadir',
        ]);

        $presensi = Presensi::query()->first();
        $this->assertNotNull($presensi);
        $this->assertSame('08:15', $presensi->jam_tap?->format('H:i'));
        $this->assertStringContainsString('Input manual oleh', (string) $presensi->keterangan);

        Notification::assertSentTo($peserta, PresensiRecordedNotification::class);
    }

    public function test_manual_entry_defaults_to_fifteen_minutes_before_kegiatan_starts(): void
    {
        $archivist = $this->userIn($this->archivistDivisi);
        $peserta = $this->userIn($this->chefDivisi);
        $kegiatan = $this->closedKegiatan();

        $this->actingAs($archivist)
            ->post(route('presensi.store'), [
                'kegiatan_id' => $kegiatan->id,
                'user_id' => $peserta->id,
            ])
            ->assertRedirect();

        $presensi = Presensi::query()->first();
        $this->assertNotNull($presensi);
        $this->assertSame('07:45', $presensi->jam_tap?->format('H:i'));
    }

    public function test_regular_panitia_cannot_record_attendance_manually(): void
    {
        $chef = $this->userIn($this->chefDivisi);
        $peserta = $this->userIn($this->chefDivisi);
        $kegiatan = $this->closedKegiatan();

        $this->actingAs($chef)
            ->post(route('presensi.store'), [
                'kegiatan_id' => $kegiatan->id,
                'user_id' => $peserta->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('presensis', 0);
    }

    public function test_manual_entry_is_rejected_when_already_recorded(): void
    {
        $archivist = $this->userIn($this->archivistDivisi);
        $peserta = $this->userIn($this->chefDivisi);
        $kegiatan = $this->closedKegiatan();

        Presensi::create([
            'user_id' => $peserta->id,
            'kegiatan_id' => $kegiatan->id,
            'status' => 'hadir',
            'jam_tap' => now(),
        ]);

        $this->actingAs($archivist)
            ->from(route('presensi.monitoring', ['kegiatan_id' => $kegiatan->id]))
            ->post(route('presensi.store'), [
                'kegiatan_id' => $kegiatan->id,
                'user_id' => $peserta->id,
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseCount('presensis', 1);
    }

    public function test_archivist_can_import_csv_attendance(): void
    {
        Notification::fake();

        $archivist = $this->userIn($this->archivistDivisi);
        $hadir = $this->userIn($this->chefDivisi, nim: '2508394');
        $duplikat = $this->userIn($this->chefDivisi, nim: '2508395');
        $ranger = $this->userIn($this->rangerDivisi);
        $kegiatan = $this->closedKegiatan();

        Presensi::create([
            'user_id' => $duplikat->id,
            'kegiatan_id' => $kegiatan->id,
            'status' => 'hadir',
            'jam_tap' => now(),
        ]);

        $csv = UploadedFile::fake()->createWithContent(
            'presensi.csv',
            implode("\n", [
                'nim,nama,status,waktu',
                "{$hadir->nim},Hadir,hadir,08:20",
                "{$duplikat->nim},Duplikat,hadir,08:21",
                '9999999,Tidak Ada,hadir,08:22',
            ])
        );

        $this->actingAs($archivist)
            ->post(route('presensi.import'), [
                'kegiatan_id' => $kegiatan->id,
                'file' => $csv,
            ])
            ->assertRedirect(route('presensi.monitoring', ['kegiatan_id' => $kegiatan->id]))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('presensis', [
            'user_id' => $hadir->id,
            'kegiatan_id' => $kegiatan->id,
            'scanned_by' => $archivist->id,
        ]);
        $this->assertDatabaseCount('presensis', 2);

        Notification::assertSentTo($hadir, PresensiRecordedNotification::class);
        Notification::assertSentTo($ranger, ReminderNotification::class, function (ReminderNotification $notification) {
            return $notification->title === 'Impor presensi' && $notification->type === 'presensi';
        });
        Notification::assertNotSentTo($ranger, PresensiRecordedNotification::class);
    }

    public function test_monitoring_page_shows_manual_actions_only_for_archivist(): void
    {
        $this->closedKegiatan();

        $this->actingAs($this->userIn($this->archivistDivisi))
            ->get(route('presensi.monitoring'))
            ->assertOk()
            ->assertSee('Tambah Presensi Manual', false)
            ->assertSee('Impor Presensi', false);

        $this->actingAs($this->userIn($this->rangerDivisi))
            ->get(route('presensi.monitoring'))
            ->assertOk()
            ->assertDontSee('Tambah Presensi Manual', false)
            ->assertDontSee('Impor Presensi', false);
    }

    public function test_template_download_is_limited_to_archivist(): void
    {
        $this->actingAs($this->userIn($this->archivistDivisi))
            ->get(route('presensi.template'))
            ->assertOk()
            ->assertHeader('content-disposition');

        $this->actingAs($this->userIn($this->chefDivisi))
            ->get(route('presensi.template'))
            ->assertForbidden();
    }

    private function userIn(Divisi $divisi, ?string $nim = null): User
    {
        return User::factory()->create([
            'divisi_id' => $divisi->id,
            'jabatan_id' => $this->anggota->id,
            'nim' => $nim ?? fake()->unique()->numerify('#######'),
        ]);
    }

    private function closedKegiatan(): Kegiatan
    {
        return Kegiatan::create([
            'nama' => 'RABES 1',
            'tempat' => 'Aula',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '12:00:00',
            'presensi_mulai' => now()->subHours(4),
            'presensi_selesai' => now()->subHour(),
        ]);
    }
}
