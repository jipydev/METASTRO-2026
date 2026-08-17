<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Hukuman;
use App\Models\Jabatan;
use App\Models\User;
use App\Notifications\HukumanNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class HukumanTest extends TestCase
{
    use RefreshDatabase;

    public function test_ranger_can_punish_panitia_but_not_pengawas(): void
    {
        Notification::fake();

        $rangerDivisi = Divisi::create(['nama' => 'Ranger']);
        $chefDivisi = Divisi::create(['nama' => 'Chef']);
        $stakeholderDivisi = Divisi::create(['nama' => 'Stakeholder']);

        $anggotaJabatan = Jabatan::query()->where('nama', 'Anggota')->firstOrFail();
        $pengawasJabatan = Jabatan::query()->where('nama', 'Pengawas')->firstOrFail();
        $ketuaPengawasJabatan = Jabatan::query()->where('nama', 'Ketua Pengawas')->firstOrFail();

        $ranger = User::factory()->create([
            'divisi_id' => $rangerDivisi->id,
            'jabatan_id' => $anggotaJabatan->id,
        ]);
        $target = User::factory()->create([
            'nama' => 'Panitia Chef',
            'divisi_id' => $chefDivisi->id,
            'jabatan_id' => $anggotaJabatan->id,
        ]);
        $pengawas = User::factory()->create([
            'nama' => 'Pengawas Ops',
            'divisi_id' => $chefDivisi->id,
            'jabatan_id' => $pengawasJabatan->id,
        ]);
        $ketuaPengawas = User::factory()->create([
            'nama' => 'Ketua Pengawas Stakeholder',
            'divisi_id' => $stakeholderDivisi->id,
            'jabatan_id' => $ketuaPengawasJabatan->id,
        ]);

        $this->actingAs($ranger)
            ->post(route('hukuman.store', 'ranger'), [
                'user_id' => $target->id,
                'kategori' => 'ringan',
                'alasan' => 'Terlambat briefing.',
            ])
            ->assertRedirect(route('hukuman.kelola', 'ranger'));

        $this->assertDatabaseHas('hukumans', [
            'user_id' => $target->id,
            'issued_by' => $ranger->id,
            'kategori' => 'ringan',
            'issuer_mode' => 'ranger',
        ]);

        Notification::assertSentTo($target, HukumanNotification::class, function (HukumanNotification $notification) {
            return $notification->event === 'issued' && $notification->audience === 'target';
        });

        $this->actingAs($ranger)
            ->post(route('hukuman.store', 'ranger'), [
                'user_id' => $pengawas->id,
                'kategori' => 'sedang',
                'alasan' => 'Should fail.',
            ])
            ->assertSessionHasErrors('user_id');

        $this->actingAs($ranger)
            ->post(route('hukuman.store', 'ranger'), [
                'user_id' => $ketuaPengawas->id,
                'kategori' => 'sedang',
                'alasan' => 'Should fail too.',
            ])
            ->assertSessionHasErrors('user_id');
    }

    public function test_admin_can_punish_pengawas_in_ranger_mode(): void
    {
        Notification::fake();

        $chiperDivisi = Divisi::create(['nama' => 'Chiper']);
        $chefDivisi = Divisi::create(['nama' => 'Chef']);
        $pengawasJabatan = Jabatan::query()->where('nama', 'Pengawas')->firstOrFail();
        $anggotaJabatan = Jabatan::query()->where('nama', 'Anggota')->firstOrFail();

        $admin = User::factory()->admin()->create([
            'divisi_id' => $chiperDivisi->id,
            'jabatan_id' => $anggotaJabatan->id,
        ]);
        $pengawas = User::factory()->create([
            'nama' => 'Pengawas Ops',
            'divisi_id' => $chefDivisi->id,
            'jabatan_id' => $pengawasJabatan->id,
        ]);

        $this->actingAs($admin)
            ->post(route('hukuman.store', 'ranger'), [
                'user_id' => $pengawas->id,
                'kategori' => 'berat',
                'alasan' => 'Admin menghukum pengawas.',
            ])
            ->assertRedirect(route('hukuman.kelola', 'ranger'));

        $this->assertDatabaseHas('hukumans', [
            'user_id' => $pengawas->id,
            'issued_by' => $admin->id,
            'issuer_mode' => 'ranger',
        ]);
    }

    public function test_pengawas_can_only_punish_other_pengawas(): void
    {
        $chefDivisi = Divisi::create(['nama' => 'Chef']);
        $stakeholderDivisi = Divisi::create(['nama' => 'Stakeholder']);

        $pengawasJabatan = Jabatan::query()->where('nama', 'Pengawas')->firstOrFail();
        $ketuaPengawasJabatan = Jabatan::query()->where('nama', 'Ketua Pengawas')->firstOrFail();
        $anggotaJabatan = Jabatan::query()->where('nama', 'Anggota')->firstOrFail();

        $issuer = User::factory()->create([
            'divisi_id' => $stakeholderDivisi->id,
            'jabatan_id' => $ketuaPengawasJabatan->id,
        ]);
        $targetPengawas = User::factory()->create([
            'nama' => 'Pengawas Lain',
            'divisi_id' => $chefDivisi->id,
            'jabatan_id' => $pengawasJabatan->id,
        ]);
        $panitia = User::factory()->create([
            'nama' => 'Panitia Biasa',
            'divisi_id' => $chefDivisi->id,
            'jabatan_id' => $anggotaJabatan->id,
        ]);

        $this->actingAs($issuer)
            ->post(route('hukuman.store', 'pengawas'), [
                'user_id' => $targetPengawas->id,
                'kategori' => 'berat',
                'alasan' => 'Pelanggaran protokol.',
            ])
            ->assertRedirect(route('hukuman.kelola', 'pengawas'));

        $this->assertDatabaseHas('hukumans', [
            'user_id' => $targetPengawas->id,
            'issuer_mode' => 'pengawas',
        ]);

        $this->actingAs($issuer)
            ->post(route('hukuman.store', 'pengawas'), [
                'user_id' => $panitia->id,
                'kategori' => 'ringan',
                'alasan' => 'Invalid target.',
            ])
            ->assertSessionHasErrors('user_id');
    }

    public function test_target_must_submit_pembelaan_before_completing(): void
    {
        Notification::fake();

        $rangerDivisi = Divisi::create(['nama' => 'Ranger']);
        $chefDivisi = Divisi::create(['nama' => 'Chef']);
        $jabatan = Jabatan::query()->where('nama', 'Anggota')->firstOrFail();

        $ranger = User::factory()->create([
            'divisi_id' => $rangerDivisi->id,
            'jabatan_id' => $jabatan->id,
        ]);
        $target = User::factory()->create([
            'divisi_id' => $chefDivisi->id,
            'jabatan_id' => $jabatan->id,
        ]);

        $this->actingAs($ranger)->post(route('hukuman.store', 'ranger'), [
            'user_id' => $target->id,
            'kategori' => 'sedang',
            'alasan' => 'Tidak hadir rapat.',
        ]);

        $hukuman = Hukuman::firstOrFail();

        $this->actingAs($target)
            ->post(route('hukuman.selesai', $hukuman))
            ->assertForbidden();

        $this->actingAs($target)
            ->post(route('hukuman.pembelaan', $hukuman), [
                'pembelaan' => 'Ada kendala mendadak.',
            ])
            ->assertRedirect(route('hukuman.show', $hukuman));

        Notification::assertSentTo($ranger, HukumanNotification::class, function (HukumanNotification $notification) {
            return $notification->event === 'pembelaan';
        });

        $hukuman->refresh();
        $this->assertTrue($hukuman->sudahPembelaan());

        $this->actingAs($target)
            ->post(route('hukuman.tugas', $hukuman), [
                'tugas_link' => 'https://drive.google.com/file/d/example',
            ])
            ->assertRedirect(route('hukuman.show', $hukuman));

        Notification::assertSentTo($ranger, HukumanNotification::class, function (HukumanNotification $notification) {
            return $notification->event === 'tugas';
        });

        $this->actingAs($target)
            ->post(route('hukuman.selesai', $hukuman))
            ->assertRedirect(route('hukuman.index'));

        Notification::assertSentTo($ranger, HukumanNotification::class, function (HukumanNotification $notification) {
            return $notification->event === 'selesai';
        });

        $hukuman->refresh();
        $this->assertTrue($hukuman->isSelesai());
        $this->assertNotNull($hukuman->tugas_link);
    }

    public function test_hukuman_deadline_is_two_days(): void
    {
        $rangerDivisi = Divisi::create(['nama' => 'Ranger']);
        $chefDivisi = Divisi::create(['nama' => 'Chef']);
        $jabatan = Jabatan::query()->where('nama', 'Anggota')->firstOrFail();

        $ranger = User::factory()->create([
            'divisi_id' => $rangerDivisi->id,
            'jabatan_id' => $jabatan->id,
        ]);
        $target = User::factory()->create([
            'divisi_id' => $chefDivisi->id,
            'jabatan_id' => $jabatan->id,
        ]);

        $this->actingAs($ranger)->post(route('hukuman.store', 'ranger'), [
            'user_id' => $target->id,
            'kategori' => 'khusus',
            'alasan' => 'Pelanggaran berat.',
        ]);

        $hukuman = Hukuman::firstOrFail();

        $this->assertTrue($hukuman->deadline_at->equalTo($hukuman->created_at->copy()->addDays(2)));
    }

    public function test_non_manager_cannot_access_kelola(): void
    {
        $chefDivisi = Divisi::create(['nama' => 'Chef']);
        $jabatan = Jabatan::query()->where('nama', 'Anggota')->firstOrFail();
        $panitia = User::factory()->create([
            'divisi_id' => $chefDivisi->id,
            'jabatan_id' => $jabatan->id,
        ]);

        $this->actingAs($panitia)
            ->get(route('hukuman.kelola', 'ranger'))
            ->assertForbidden();
    }

    public function test_issuer_can_update_and_delete_hukuman(): void
    {
        Notification::fake();

        $rangerDivisi = Divisi::create(['nama' => 'Ranger']);
        $chefDivisi = Divisi::create(['nama' => 'Chef']);
        $jabatan = Jabatan::query()->where('nama', 'Anggota')->firstOrFail();

        $ranger = User::factory()->create([
            'divisi_id' => $rangerDivisi->id,
            'jabatan_id' => $jabatan->id,
        ]);
        $target = User::factory()->create([
            'nama' => 'Target Awal',
            'divisi_id' => $chefDivisi->id,
            'jabatan_id' => $jabatan->id,
        ]);
        $replacement = User::factory()->create([
            'nama' => 'Target Baru',
            'divisi_id' => $chefDivisi->id,
            'jabatan_id' => $jabatan->id,
        ]);

        $this->actingAs($ranger)->post(route('hukuman.store', 'ranger'), [
            'user_id' => $target->id,
            'kategori' => 'ringan',
            'alasan' => 'Terlambat briefing.',
        ]);

        $hukuman = Hukuman::firstOrFail();

        $this->actingAs($ranger)
            ->put(route('hukuman.update', $hukuman), [
                'user_id' => $target->id,
                'kategori' => 'sedang',
                'alasan' => 'Terlambat briefing dan tidak izin.',
            ])
            ->assertRedirect(route('hukuman.show', $hukuman));

        $this->assertDatabaseHas('hukumans', [
            'id' => $hukuman->id,
            'kategori' => 'sedang',
            'alasan' => 'Terlambat briefing dan tidak izin.',
        ]);

        Notification::assertSentTo($target, HukumanNotification::class, function (HukumanNotification $notification) {
            return $notification->event === 'updated';
        });

        $this->actingAs($ranger)
            ->put(route('hukuman.update', $hukuman), [
                'user_id' => $replacement->id,
                'kategori' => 'sedang',
                'alasan' => 'Terlambat briefing dan tidak izin.',
            ])
            ->assertRedirect(route('hukuman.show', $hukuman));

        Notification::assertSentTo($target, HukumanNotification::class, function (HukumanNotification $notification) {
            return $notification->event === 'dibatalkan';
        });
        Notification::assertSentTo($replacement, HukumanNotification::class, function (HukumanNotification $notification) {
            return $notification->event === 'issued';
        });

        $this->actingAs($target)
            ->delete(route('hukuman.destroy', $hukuman))
            ->assertForbidden();

        $this->actingAs($ranger)
            ->delete(route('hukuman.destroy', $hukuman))
            ->assertRedirect(route('hukuman.kelola', 'ranger'));

        $this->assertDatabaseMissing('hukumans', ['id' => $hukuman->id]);
    }
}
