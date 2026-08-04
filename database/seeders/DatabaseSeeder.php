<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            JabatanSeeder::class,
            DivisiSeeder::class,
            AdminSeeder::class,
            TimelineSeeder::class,
            UserSeeder::class,
            PresensiSeeder::class,
            PengumumanSeeder::class,
            RapatSeeder::class,
        ]);
    }
}
