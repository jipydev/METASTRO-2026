<?php

namespace Database\Seeders;

use App\Models\Timeline;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimelineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timelines = [
            [
                'judul' => 'Rabes 1',
                'slug' => 'rabes-1',
                'tanggal_mulai' => '2026-07-28 08:00:00',
                'tanggal_selesai' => '2026-07-28 10:00:00',
                'ruangan' => 'Ruang PGSD 4',
            ],
            [
                'judul' => 'Rabes 2',
                'slug'=> 'rabes-2',
                'tanggal_mulai' => '2026-07-29 09:00:00',
                'tanggal_selesai' => '2026-07-29 11:00:00',
                'ruangan' => 'Ruang PGSD 5',
            ],
            [
                'judul' => 'Rabes 3',
                'slug' => 'rabes-3',
                'tanggal_mulai' => '2026-07-30 10:00:00',
                'tanggal_selesai' => '2026-07-30 12:00:00',
                'ruangan' => 'Ruang PGSD 6',
            ],
        ];

        foreach ($timelines as $timeline) {
            Timeline::create($timeline);
        }
    }
}
