<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'ubah pengumuman',
            'scan presensi',
            'lihat presensi',
            'tambah timeline',
            'ubah timeline',
            'hapus timeline',
            'upload notulensi',
            'hapus notulensi',
            'lihat notulensi',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
