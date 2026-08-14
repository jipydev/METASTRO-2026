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
            ['nama_jabatan' => 'Ketua', 'deskripsi' => 'Ketua divisi bertugas memimpin divisi dan mereview izin anggota divisinya.'],
            ['nama_jabatan' => 'Wakil', 'deskripsi' => 'Wakil divisi membantu ketua dan mereview izin apabila ketua mengajukan izin.'],
            ['nama_jabatan' => 'Ketua Pengawas', 'deskripsi' => 'Ketua Pengawas bertugas memimpin divisi pengawas dan dapat melihat list panitia/rekap absen pengawas.'],
            ['nama_jabatan' => 'Pengawas', 'deskripsi' => 'Pengawas bertugas mengawasi divisi masing-masing dan melihat list panitia/rekap absen divisi.'],
            ['nama_jabatan' => 'Anggota', 'deskripsi' => 'Anggota biasa yang dapat melihat dan mengajukan izin.'],
            // Compatibility aliases
            ['nama_jabatan' => 'Koordinator', 'deskripsi' => 'Alias untuk Ketua'],
            ['nama_jabatan' => 'Staff', 'deskripsi' => 'Alias untuk Anggota'],
        ];

        foreach ($jabatans as $jabatan) {
            Jabatan::updateOrCreate(
                ['nama_jabatan' => $jabatan['nama_jabatan']],
                ['deskripsi' => $jabatan['deskripsi']]
            );
        }
    }
}
