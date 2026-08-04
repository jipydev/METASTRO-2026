<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'permissions' => ['lihat presensi', 'lihat timeline', 'scan presensi', 'upload notulensi', 'hapus notulensi', 'tambah timeline', 'ubah timeline', 'hapus timeline', 'ubah pengumuman']],
            ['name' => 'Panitia'],
            ['name' => 'Peserta'],
            ['name' => 'Ranger', 'permissions' => ['lihat presensi', 'lihat timeline']],
            ['name' => 'Sekretaris', 'permissions' => ['lihat presensi', 'lihat timeline', 'scan presensi', 'upload notulensi', 'hapus notulensi', 'tambah timeline', 'ubah timeline', 'hapus timeline', 'ubah pengumuman']],
            ['name' => 'Pengawas'],
        ];

        foreach ($roles as $role) {
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
