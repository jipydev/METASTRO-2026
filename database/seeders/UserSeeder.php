<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Try to use roles seeded by RoleSeeder; fall back to a default list
        $roles = Role::pluck('name')->toArray();
        if (empty($roles)) {
            $roles = ['Admin', 'Panitia', 'Peserta', 'Ranger', 'Sekretaris', 'Pengawas'];
        }

        foreach ($roles as $i => $roleName) {
            $slug = Str::slug($roleName, '_');
            // $email = $slug . '@metastro.id';
            $nim = '0000' . ($i + 1);

            $user = User::firstOrCreate(
                ['nim' => $nim],
                [
                    'name' => $roleName . ' User',
                    'password' => Hash::make('metastro2026'),
                    'divisi_id' => 1,
                    'jabatan_id' => 1,
                    // keep role_id null or default if your app relies on it
                    'role_id' => 1,
                    'status_aktif' => true,
                    'email_verified_at' => now(),
                ]
            );



            // Assign Spatie role if available
            try {
                if (! $user->hasRole($roleName)) {
                    $user->assignRole($roleName);
                }
            } catch (\Throwable $e) {
                // If Spatie isn't configured yet, ignore role assignment
            }
        }
    }
}
