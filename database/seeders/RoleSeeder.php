<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Reset cached roles and permissions bawaan Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Daftar Role Utama
        $roles = [
            'admin',
            'panitia',
            'peserta',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name'       => $roleName,
                'guard_name' => 'web',
            ]);
        }

        // 3. (Opsional) Jika nanti ada permission global yang ingin dipakai:
        // $permissions = [
        //     'akses dashboard admin',
        //     'kelola user',
        // ];
        // foreach ($permissions as $permission) {
        //     Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        // }
        // Role::findByName('admin')->givePermissionTo($permissions);
    }
}
