<?php

namespace Database\Seeders;

use App\Models\Tim;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TimSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            'Axis',
            'Vector',
            'Torque',
            'Momentum',
            'Force',
            'Impulse',
            'Inertia',
            'Flux',
            'Accel',
            'Radius',
        ] as $nama) {
            Tim::updateOrCreate(
                ['slug' => Str::slug($nama)],
                ['nama' => $nama],
            );
        }
    }
}
