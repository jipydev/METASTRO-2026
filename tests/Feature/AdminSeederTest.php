<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_seeder_creates_valid_admin_account(): void
    {
        $this->seed(AdminSeeder::class);

        $admin = User::where('email', 'admin@metastro.id')->first();

        $this->assertNotNull($admin);
        $this->assertSame('Administrator Metastro', $admin->nama);
        $this->assertSame('0000001', $admin->nim);
        $this->assertTrue(Hash::check('password', $admin->password));
        $this->assertTrue($admin->status);
        $this->assertTrue($admin->is_initial_setup_completed);
        $this->assertNotNull($admin->email_verified_at);
        $this->assertSame('MTR-0001', $admin->kode_unik);
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertTrue($admin->isChiper());
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->canScanPresensi());
    }
}
