<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jabatans = [
            ['nama_jabatan' => 'Koordinator', 'deskripsi' => 'Koordinator adalah orang yang bertanggung jawab atas koordinasi dan pengelolaan suatu divisi'],
            ['nama_jabatan' => 'Anggota', 'deskripsi' => 'Anggota adalah individu yang merupakan bagian dari suatu divisi.'],
            ['nama_jabatan' => 'Pengawas', 'deskripsi' => 'Pengawas adalah individu yang bertugas untuk mengawasi dan memastikan bahwa kegiatan atau proses berjalan sesuai dengan aturan dan standar yang telah ditetapkan.'],
        ];

        foreach ($jabatans as $jabatan) {
            Jabatan::firstOrCreate([
                'nama_jabatan' => $jabatan['nama_jabatan'],
                'deskripsi' => $jabatan['deskripsi'],
            ]);
        }
    }
}
