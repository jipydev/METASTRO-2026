<?php

namespace Tests\Unit;

use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StakeholderJabatanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\JabatanSeeder::class);
    }

    public function test_stakeholder_user_uses_jabatan_label_directly(): void
    {
        $divisi = Divisi::create(['nama' => 'Stakeholder']);
        $jabatan = Jabatan::query()->where('nama', 'Person in Charge')->firstOrFail();

        $user = User::factory()->create([
            'divisi_id' => $divisi->id,
            'jabatan_id' => $jabatan->id,
        ]);

        $this->assertSame('Person in Charge', $user->formatted_divisi_jabatan);
        $this->assertTrue($user->isPersonInCharge());
    }

    public function test_operational_divisi_still_uses_koordinator_prefix(): void
    {
        $divisi = Divisi::create(['nama' => 'Ranger']);
        $jabatan = Jabatan::query()->where('nama', 'Ketua')->firstOrFail();

        $user = User::factory()->create([
            'divisi_id' => $divisi->id,
            'jabatan_id' => $jabatan->id,
        ]);

        $this->assertSame('Koordinator Ranger', $user->formatted_divisi_jabatan);
    }

    public function test_jabatan_matches_divisi_rules(): void
    {
        $this->assertTrue(Jabatan::matchesDivisi('Stakeholder', 'Steering Committee'));
        $this->assertFalse(Jabatan::matchesDivisi('Stakeholder', 'Ketua'));
        $this->assertTrue(Jabatan::matchesDivisi('Ranger', 'Ketua'));
        $this->assertFalse(Jabatan::matchesDivisi('Ranger', 'Ketua Pelaksana'));
    }
}
