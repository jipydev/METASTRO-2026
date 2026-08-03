<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rapat; // Panggil modelnya

class RapatSeeder extends Seeder
{
    public function run(): void
    {
        Rapat::create([
            'judul' => 'Rapat Besar 1',
            'tanggal' => '2026-07-20',
            'jam' => '08:00:00',
            'tempat' => 'Ruang PGSD 4',
            'hadir' => 5,
            'total' => 120
        ]);
        
        Rapat::create([
            'judul' => 'Rapat Besar 2',
            'tanggal' => '2026-08-06',
            'jam' => '08:00:00',
            'tempat' => 'Ruang PGSD 4',
            'hadir' => 0,
            'total' => 120
        ]);
    }
}