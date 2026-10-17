<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\ArchivistSeeder;
use Database\Seeders\GuiderSeeder;
use Database\Seeders\PathfinderSeeder;
use Database\Seeders\PesertaSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_seeder_creates_all_standard_roles(): void
    {
        $this->seed(RoleSeeder::class);

        $expectedRoles = ['admin', 'panitia', 'peserta', 'archivist', 'pathfinder', 'guider'];

        foreach ($expectedRoles as $role) {
            $this->assertDatabaseHas('roles', [
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }
    }

    public function test_archivist_seeder_creates_valid_archivist(): void
    {
        $this->seed(ArchivistSeeder::class);

        $archivist = User::where('email', 'archivist@metastro.id')->first();

        $this->assertNotNull($archivist);
        $this->assertSame('0000002', $archivist->nim);
        $this->assertTrue(Hash::check('password', $archivist->password));
        $this->assertTrue($archivist->hasRole('panitia'));
        $this->assertTrue($archivist->hasRole('archivist'));
        $this->assertTrue($archivist->isArchivist());
        $this->assertTrue($archivist->canScanPresensi());
        $this->assertTrue($archivist->canManageSekretariat());
        $this->assertSame('MTR-0002', $archivist->kode_unik);
    }

    public function test_pathfinder_seeder_creates_valid_pathfinder(): void
    {
        $this->seed(PathfinderSeeder::class);

        $pathfinder = User::where('email', 'pathfinder@metastro.id')->first();

        $this->assertNotNull($pathfinder);
        $this->assertSame('0000003', $pathfinder->nim);
        $this->assertTrue(Hash::check('password', $pathfinder->password));
        $this->assertTrue($pathfinder->hasRole('panitia'));
        $this->assertTrue($pathfinder->hasRole('pathfinder'));
        $this->assertTrue($pathfinder->isPathfinder());
        $this->assertTrue($pathfinder->canManageKegiatan());
        $this->assertSame('MTR-0003', $pathfinder->kode_unik);
    }

    public function test_guider_seeder_creates_valid_guider(): void
    {
        $this->seed(GuiderSeeder::class);

        $guider = User::where('email', 'guider@metastro.id')->first();

        $this->assertNotNull($guider);
        $this->assertSame('0000004', $guider->nim);
        $this->assertTrue(Hash::check('password', $guider->password));
        $this->assertTrue($guider->hasRole('panitia'));
        $this->assertTrue($guider->hasRole('guider'));
        $this->assertTrue($guider->isGuider());
        $this->assertTrue($guider->canReviewTugas());
        $this->assertTrue($guider->canViewTim());
        $this->assertSame('MTR-0004', $guider->kode_unik);
    }

    public function test_peserta_seeder_creates_valid_peserta(): void
    {
        $this->seed(PesertaSeeder::class);

        $peserta = User::where('email', 'peserta@metastro.id')->first();

        $this->assertNotNull($peserta);
        $this->assertSame('2500001', $peserta->nim);
        $this->assertTrue(Hash::check('password', $peserta->password));
        $this->assertTrue($peserta->hasRole('peserta'));
        $this->assertTrue($peserta->isPeserta());
        $this->assertNull($peserta->divisi_id);
        $this->assertNull($peserta->jabatan_id);
        $this->assertSame('MTR-1001', $peserta->kode_unik);
    }

    public function test_user_seeder_seeds_all_accounts(): void
    {
        $this->seed(UserSeeder::class);

        $this->assertDatabaseHas('users', ['email' => 'admin@metastro.id']);
        $this->assertDatabaseHas('users', ['email' => 'archivist@metastro.id']);
        $this->assertDatabaseHas('users', ['email' => 'pathfinder@metastro.id']);
        $this->assertDatabaseHas('users', ['email' => 'guider@metastro.id']);
        $this->assertDatabaseHas('users', ['email' => 'peserta@metastro.id']);
    }
}
