<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['nim' => '12345678'],
            [
                'name' => 'Administrator',
                'email' => 'admin@metastro.com',
                'password' => bcrypt('kastatertinggi'),
                'status_aktif' => true,
                'email_verified_at' => now(),
            ]
        );

        $admin->syncRoles(['Admin']);
    }
}