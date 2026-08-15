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
            [
                'nama'      => 'Ketua',
                'deskripsi' => 'Ketua divisi bertugas memimpin divisi dan mereview izin anggota divisinya.',
            ],
            [
                'nama'      => 'Wakil',
                'deskripsi' => 'Wakil divisi membantu ketua dan mereview izin apabila ketua mengajukan izin.',
            ],
            [
                'nama'      => 'Ketua Pengawas',
                'deskripsi' => 'Ketua Pengawas bertugas memimpin divisi pengawas dan dapat melihat list panitia serta rekap presensi.',
            ],
            [
                'nama'      => 'Pengawas',
                'deskripsi' => 'Pengawas bertugas mengawasi divisi dan melihat list panitia serta rekap presensi.',
            ],
            [
                'nama'      => 'Anggota',
                'deskripsi' => 'Anggota divisi yang dapat melihat aktivitas divisi dan mengajukan izin.',
            ],
        ];

        foreach ($jabatans as $jabatan) {
            Jabatan::updateOrCreate(
                ['nama' => $jabatan['nama']],
                ['deskripsi' => $jabatan['deskripsi']]
            );
        }
    }
}
