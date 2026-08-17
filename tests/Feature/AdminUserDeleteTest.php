<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Hukuman;
use App\Models\Jabatan;
use App\Models\PengajuanIzin;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_user_with_related_records(): void
    {
        $admin = User::factory()->admin()->create();
        $divisi = Divisi::create(['nama' => 'Chef']);
        $jabatan = Jabatan::query()->where('nama', 'Anggota')->firstOrFail();
        $target = User::factory()->panitia()->create([
            'divisi_id' => $divisi->id,
            'jabatan_id' => $jabatan->id,
        ]);

        Hukuman::create([
            'user_id' => $target->id,
            'issued_by' => $admin->id,
            'kategori' => 'ringan',
            'issuer_mode' => 'ranger',
            'alasan' => 'Terlambat briefing.',
            'deadline_at' => now()->addDays(2),
        ]);

        PengajuanIzin::create([
            'user_id' => $target->id,
            'tanggal_izin' => now()->toDateString(),
            'jenis_izin' => 'izin',
            'alasan' => 'Acara keluarga.',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $target))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
        $this->assertDatabaseMissing('hukumans', ['user_id' => $target->id]);
        $this->assertDatabaseMissing('pengajuan_izins', ['user_id' => $target->id]);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_delete_user_who_issued_hukuman(): void
    {
        $admin = User::factory()->admin()->create();
        $issuer = User::factory()->panitia()->create();
        $target = User::factory()->panitia()->create();

        Hukuman::create([
            'user_id' => $target->id,
            'issued_by' => $issuer->id,
            'kategori' => 'sedang',
            'issuer_mode' => 'ranger',
            'alasan' => 'Issued by deletable user.',
            'deadline_at' => now()->addDays(2),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $issuer))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $issuer->id]);
        $this->assertDatabaseMissing('hukumans', ['issued_by' => $issuer->id]);
        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }
}
