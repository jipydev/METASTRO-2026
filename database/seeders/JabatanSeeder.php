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
                'nama' => 'Ketua',
                'deskripsi' => 'Ketua divisi bertugas memimpin divisi dan mereview izin anggota divisinya.',
            ],
            [
                'nama' => 'Wakil',
                'deskripsi' => 'Wakil divisi membantu ketua dan mereview izin apabila ketua mengajukan izin.',
            ],
            [
                'nama' => 'Pengawas',
                'deskripsi' => 'Pengawas bertugas mengawasi divisi dan melihat list panitia serta rekap presensi.',
            ],
            [
                'nama' => 'Anggota',
                'deskripsi' => 'Anggota divisi yang dapat melihat aktivitas divisi dan mengajukan izin.',
            ],
            [
                'nama' => 'Person in Charge',
                'deskripsi' => 'Person in Charge (Kahim) memimpin steering committee Stakeholder.',
            ],
            [
                'nama' => 'Ketua Pelaksana',
                'deskripsi' => 'Ketua Pelaksana (Chief Executive) memimpin pelaksanaan kegiatan panitia.',
            ],
            [
                'nama' => 'Wakil Ketua Pelaksana',
                'deskripsi' => 'Wakil Ketua Pelaksana (Deputy Chief Executive) membantu ketua pelaksana.',
            ],
            [
                'nama' => 'Ketua Pengawas',
                'deskripsi' => 'Ketua Pengawas (Head of Supervisor) memimpin pengawasan panitia.',
            ],
            [
                'nama' => 'Wakil Ketua Pengawas',
                'deskripsi' => 'Wakil Ketua Pengawas (Deputy Head of Supervisor) membantu ketua pengawas.',
            ],
            [
                'nama' => 'Steering Committee',
                'deskripsi' => 'Anggota steering committee Stakeholder.',
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
