<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jabatans = [
            ['nama_jabatan' => 'Ketua', 'deskripsi' => 'Ketua adalah pemimpin divisi yang bertanggung jawab atas keseluruhan pelaksanaan kegiatan divisinya.'],
            ['nama_jabatan' => 'Wakil', 'deskripsi' => 'Wakil membantu Ketua dalam memimpin dan mengelola divisi.'],
            ['nama_jabatan' => 'Ketua Pengawas', 'deskripsi' => 'Ketua Pengawas adalah pemimpin dari divisi pengawas yang bertugas mengawasi jalannya kegiatan kepanitiaan.'],
            ['nama_jabatan' => 'Pengawas', 'deskripsi' => 'Pengawas adalah individu yang bertugas untuk mengawasi dan memastikan bahwa kegiatan atau proses berjalan sesuai dengan aturan.'],
            ['nama_jabatan' => 'Anggota', 'deskripsi' => 'Anggota adalah bagian dari suatu divisi yang melaksanakan tugas-tugas divisi tersebut.'],
        ];

        foreach ($jabatans as $jabatan) {
            Jabatan::firstOrCreate([
                'nama_jabatan' => $jabatan['nama_jabatan'],
                'deskripsi' => $jabatan['deskripsi'],
            ]);
        }
    }
}
