<?php

namespace Database\Seeders;

use App\Models\PengumpulanTugas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PengumpulanTugasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PengumpulanTugas::factory()
            ->count(10)
            ->create();
    }
}
