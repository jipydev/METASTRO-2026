<?php

namespace Database\Seeders;

use App\Models\ListPanitia;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ListPanitiaSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $divisiList = ['Acara', 'Humas', 'Konsumsi', 'Perlengkapan', 'Dokumentasi', 'Keamanan'];
        $statusList = ['Hadir', 'Telat', 'Alpha', 'Izin', 'Sakit'];

        for ($i = 0; $i < 20; $i++) {
            $status = $faker->randomElement($statusList);

            $jamTap = match ($status) {
                'Hadir' => $faker->dateTimeBetween('06:00:00', '07:30:00')->format('H:i'),
                'Telat' => $faker->dateTimeBetween('07:31:00', '09:00:00')->format('H:i'),
                default => '-'
            };

            ListPanitia::create([
                'nama' => $faker->name,
                'divisi' => $faker->randomElement($divisiList),
                'jam_tap' => $jamTap,
                'tanggal' => 'Jumat, 31 Juli 2026',
                'status' => $status,
            ]);
        }
    }
}
