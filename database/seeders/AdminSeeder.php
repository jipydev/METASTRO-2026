<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            [
                'email' => 'adminmetastro@gmail.com',
            ],
            [
                'name' => 'Administrator',
                'password' => bcrypt('kastatertinggi'),
                'status_aktif' => true,
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('Admin');
    }
}