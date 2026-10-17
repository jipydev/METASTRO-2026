<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            ArchivistSeeder::class,
            PathfinderSeeder::class,
            GuiderSeeder::class,
            PesertaSeeder::class,
        ]);
    }
}
