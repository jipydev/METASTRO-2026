<?php

namespace Database\Seeders;

use App\Models\Pengumuman;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PengumumanSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $pembuatId = $admin ? $admin->id : 1;

        Pengumuman::create([
            'judul' => 'Pengumuman Rapat Penting Ospek',
            'isi' => 'Mohon kehadirannya pada RABES 2 tanggal 2 Agustus 2026 pukul 08.00.',
            'lampiran' => 'dokumen-rabes.pdf',
            'tanggal_publish' => Carbon::now(),
            'status' => 'Publish', // <-- Sesuaikan dengan Enum di migration
            'pembuat_id' => $pembuatId,
        ]);

        Pengumuman::create([
            'judul' => 'Perubahan Jadwal Gladi Bersih',
            'isi' => 'Gladi bersih dimajukan menjadi pukul 13.00 di lapangan utama.',
            'lampiran' => null,
            'tanggal_publish' => Carbon::now()->subDays(2),
            'status' => 'Draft', // <-- Sesuaikan dengan Enum di migration
            'pembuat_id' => $pembuatId,
        ]);
    }
}
