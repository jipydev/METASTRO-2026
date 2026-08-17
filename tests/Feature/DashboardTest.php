<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Hukuman;
use App\Models\Jabatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_ranger_and_admin_see_hukuman_stats_on_dashboard(): void
    {
        $rangerDivisi = Divisi::create(['nama' => 'Ranger']);
        $chiperDivisi = Divisi::create(['nama' => 'Chiper']);
        $chefDivisi = Divisi::create(['nama' => 'Chef']);
        $jabatan = Jabatan::query()->where('nama', 'Anggota')->firstOrFail();

        $ranger = User::factory()->create([
            'divisi_id' => $rangerDivisi->id,
            'jabatan_id' => $jabatan->id,
        ]);
        $admin = User::factory()->create([
            'divisi_id' => $chiperDivisi->id,
            'jabatan_id' => $jabatan->id,
        ]);
        $panitia = User::factory()->create([
            'divisi_id' => $chefDivisi->id,
            'jabatan_id' => $jabatan->id,
        ]);

        Hukuman::create([
            'user_id' => $panitia->id,
            'issued_by' => $ranger->id,
            'kategori' => 'ringan',
            'issuer_mode' => 'ranger',
            'alasan' => 'Terlambat briefing.',
            'deadline_at' => now()->addDays(2),
        ]);

        $this->actingAs($ranger)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Rekapitulasi Hukuman', false)
            ->assertSee('Ringan', false);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Rekapitulasi Hukuman', false)
            ->assertSee('Statistik seluruh hukuman yang tercatat', false);

        $this->actingAs($panitia)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Rekapitulasi Hukuman', false);
    }
}
