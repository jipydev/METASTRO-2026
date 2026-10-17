<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin', 'panitia', 'peserta'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $chiperDivisiId = Divisi::where('nama', 'Chiper')->value('id');
        $anggotaJabatanId = Jabatan::where('nama', 'Anggota')->value('id');
        $ketuaJabatanId = Jabatan::where('nama', 'Ketua')->value('id');

        // 1. Akun Super Admin Utama
        $adminUser = User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@metastro.id')],
            [
                'nim' => '0000001',
                'nama' => 'Administrator METASTRO',
                'password' => bcrypt(env('ADMIN_PASSWORD', 'MetastroAdmin2026!')),
                'divisi_id' => $chiperDivisiId,
                'jabatan_id' => $ketuaJabatanId,
                'qr_token' => Str::uuid(),
                'qr_updated_at' => now(),
                'status' => true,
                'is_initial_setup_completed' => true,
                'email_verified_at' => now(),
                'must_change_password' => false,
            ]
        );
        $adminUser->syncRoles(['admin']);

        // 2. Akun Admin Divisi Chiper (Web Development & Operations)
        $chiperUser = User::updateOrCreate(
            ['email' => env('CHIPER_ADMIN_EMAIL', 'chiper@metastro.id')],
            [
                'nim' => 'CHIPER01',
                'nama' => 'Admin Chiper (Web)',
                'password' => bcrypt(env('CHIPER_ADMIN_PASSWORD', 'ChiperMetastro2026!')),
                'divisi_id' => $chiperDivisiId,
                'jabatan_id' => $anggotaJabatanId,
                'qr_token' => Str::uuid(),
                'qr_updated_at' => now(),
                'status' => true,
                'is_initial_setup_completed' => true,
                'email_verified_at' => now(),
                'must_change_password' => false,
            ]
        );
        $chiperUser->syncRoles(['admin', 'panitia']);

        // 3. Akun Peserta (Jingga Muda)
        $pesertaUser = User::updateOrCreate(
            ['email' => env('PESERTA_EMAIL', 'peserta@metastro.id')],
            [
                'nim' => '2600001',
                'nama' => 'Peserta Jingga Muda',
                'password' => bcrypt(env('PESERTA_PASSWORD', 'PesertaMetastro2026!')),
                'divisi_id' => null,
                'jabatan_id' => null,
                'nomor_hp' => '081234567890',
                'jenis_kelamin' => 'laki-laki',
                'qr_token' => Str::uuid(),
                'qr_updated_at' => now(),
                'status' => true,
                'is_initial_setup_completed' => true,
                'email_verified_at' => now(),
                'must_change_password' => false,
            ]
        );
        $pesertaUser->syncRoles(['peserta']);
    }
}
