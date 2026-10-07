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

        $user = User::firstOrCreate(
            ['email' => 'admin@metastro.id'],
            [
                'nim' => '0000001',
                'nama' => 'Administrator',
                'password' => 'password',
                'divisi_id' => Divisi::where('nama', '=', 'Chiper', 'and')->value('id'),
                'jabatan_id' => Jabatan::where('nama', '=', 'Anggota', 'and')->value('id'),
                'qr_token' => Str::uuid(),
                'qr_updated_at' => now(),
            ],
        );

        $user->update([
            'status' => true,
            'is_initial_setup_completed' => true,
            'email_verified_at' => now(),
        ]);
        $user->syncRoles(['admin']);
    }
}
