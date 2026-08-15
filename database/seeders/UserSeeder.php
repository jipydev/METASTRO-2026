<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan role Spatie tersedia
        foreach (['admin', 'panitia', 'peserta'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // 2. Akun Super Admin (Chiper)
        User::factory()->admin()->create([
            'nim'        => '0000001',
            'nama'       => 'Administrator',
            'email'      => 'admin@metastro.id',
            'divisi_id'  => 3, // Chiper
            'jabatan_id' => 1, // Ketua
        ]);

        // 3. Akun Panitia (Gearmaster)
        User::factory()->panitia()->create([
            'nim'        => '0000002',
            'nama'       => 'Ketua Gearmaster',
            'email'      => 'panitia@metastro.id',
            'divisi_id'  => 6, // Gearmaster
            'jabatan_id' => 1, // Ketua
        ]);

        // 4. Akun Peserta Uji Coba
        User::factory()->peserta()->create([
            'nim'   => '0000003',
            'nama'  => 'Peserta Contoh',
            'email' => 'peserta@metastro.id',
        ]);

        // 5. (Opsional) Buat 10 Peserta Dummy Acak Sekaligus
        User::factory()->count(10)->peserta()->create();
    }
}
