<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'permissions' => ['lihat presensi' , 'scan presensi', 'upload notulensi', 'hapus notulensi', 'tambah timeline', 'ubah timeline', 'hapus timeline', 'ubah pengumuman']],
            ['name' => 'Panitia'],
            ['name' => 'Peserta'],
            ['name' => 'Koordinator Divisi', 'permissions' => ['lihat presensi']],
            ['name' => 'Wakil Koordinator Divisi', 'permissions' => ['lihat presensi']],
            ['name' => 'Ranger', 'permissions' => ['lihat presensi']],
            ['name' => 'Sekretaris', 'permissions' => [ 'lihat presensi','scan presensi', 'upload notulensi', 'hapus notulensi', 'tambah timeline', 'ubah timeline', 'hapus timeline', 'ubah pengumuman']],
            ['name' => 'Pengawas'],
            ['name' => 'Divisi Bendahara'],
            ['name' => 'Divisi Kestari'],
            ['name' => 'Divisi Acara'],
            ['name' => 'Divisi Logistik'],
            ['name' => 'Divisi Hubungan Masyarakat'],
            ['name' => 'Divisi Keamanan'],
            ['name' => 'Divisi Media Publikasi'],
            ['name' => 'Divisi Kesehatan'],
            ['name' => 'Divisi Konsumsi'],
            ['name' => 'Divisi Mentor'],
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
