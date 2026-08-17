<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PresensiHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_shows_only_the_current_users_attendance(): void
    {
        $me = User::factory()->create(['nama' => 'Riyan Panitia']);
        $other = User::factory()->create(['nama' => 'Orang Lain']);
        $mine = $this->kegiatan('RABES 1');
        $theirs = $this->kegiatan('Rapat Rahasia');

        Presensi::create([
            'user_id' => $me->id,
            'kegiatan_id' => $mine->id,
            'status' => 'hadir',
            'jam_tap' => now()->setTime(7, 50),
        ]);
        Presensi::create([
            'user_id' => $other->id,
            'kegiatan_id' => $theirs->id,
            'status' => 'hadir',
            'jam_tap' => now()->setTime(8, 20),
        ]);

        $this->actingAs($me)
            ->get(route('presensi.history'))
            ->assertOk()
            ->assertSee('RABES 1', false)
            ->assertDontSee('Rapat Rahasia', false);
    }

    public function test_scanners_also_only_see_their_own_history(): void
    {
        $archivistDivisi = Divisi::create(['nama' => 'Archivist']);
        $jabatan = Jabatan::create(['nama' => 'Anggota']);
        $scanner = User::factory()->create([
            'nama' => 'Petugas Scan',
            'divisi_id' => $archivistDivisi->id,
            'jabatan_id' => $jabatan->id,
        ]);
        $peserta = User::factory()->create(['nama' => 'Peserta Chef']);
        $kegiatan = $this->kegiatan('Briefing Saya');
        $kegiatanLain = $this->kegiatan('Briefing Orang Lain');

        Presensi::create([
            'user_id' => $scanner->id,
            'kegiatan_id' => $kegiatan->id,
            'status' => 'hadir',
            'jam_tap' => now()->setTime(7, 45),
        ]);
        Presensi::create([
            'user_id' => $peserta->id,
            'kegiatan_id' => $kegiatanLain->id,
            'status' => 'hadir',
            'jam_tap' => now()->setTime(8, 30),
        ]);

        $this->actingAs($scanner)
            ->get(route('presensi.history'))
            ->assertOk()
            ->assertSee('Briefing Saya', false)
            ->assertDontSee('Briefing Orang Lain', false);
    }

    public function test_late_filter_uses_derived_status(): void
    {
        $user = User::factory()->create();
        $kegiatan = $this->kegiatan('Pleno');

        Presensi::create([
            'user_id' => $user->id,
            'kegiatan_id' => $kegiatan->id,
            'status' => 'hadir',
            'jam_tap' => now()->setTime(8, 25),
        ]);

        $this->actingAs($user)
            ->get(route('presensi.history', ['status' => 'terlambat']))
            ->assertOk()
            ->assertSee('Pleno', false)
            ->assertSee('25 menit', false);

        $this->actingAs($user)
            ->get(route('presensi.history', ['status' => 'hadir']))
            ->assertOk()
            ->assertDontSee('Pleno', false);
    }

    private function kegiatan(string $nama): Kegiatan
    {
        return Kegiatan::create([
            'nama' => $nama,
            'tempat' => 'Aula',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '12:00:00',
            'presensi_mulai' => now()->subHour(),
            'presensi_selesai' => now()->addHour(),
        ]);
    }
}
