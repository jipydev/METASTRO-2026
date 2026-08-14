<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Core roles for the new simplified system
        $roles = [
            ['name' => 'admin'],
            ['name' => 'panitia'],
            ['name' => 'peserta'],
        ];
        // Preserve legacy roles for backward compatibility (they remain in the DB but are not used in new logic)
        $legacyRoles = [
            ['name' => 'Admin', 'permissions' => ['lihat presensi', 'scan presensi', 'upload notulensi', 'hapus notulensi', 'tambah timeline', 'ubah timeline', 'hapus timeline', 'ubah pengumuman']],
            ['name' => 'Panitia'],
            ['name' => 'Peserta'],
            ['name' => 'Ranger', 'permissions' => ['lihat presensi']],
            ['name' => 'Sekretaris', 'permissions' => ['lihat presensi', 'scan presensi', 'upload notulensi', 'hapus notulensi', 'tambah timeline', 'ubah timeline', 'hapus timeline', 'ubah pengumuman']],
            ['name' => 'Pengawas'],
            ['name' => 'Stakeholder'],
            ['name' => 'Koordinator'],
        ];

        // Create core roles
        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role['name'],
                'guard_name' => 'web',
            ]);
        }

        // Ensure legacy roles still exist (no permissions are altered here)
        foreach ($legacyRoles as $role) {
            $roleModel = Role::firstOrCreate([
                'name' => $role['name'],
                'guard_name' => 'web',
            ]);
            if ($role['permissions'] ?? false) {
                foreach ($role['permissions'] as $permission) {
                    $roleModel->givePermissionTo($permission);
                }
            }
        }
    }
}
