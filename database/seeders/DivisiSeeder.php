<?php

namespace Database\Seeders;

use App\Models\Divisi;
use Illuminate\Database\Seeder;

class DivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisis = [
            [
                'nama' => 'Archivist',
                'deskripsi' => 'Divisi Archivist atau Sekretaris bertugas mengelola administrasi dan persuratan.',
                'koordinator_id' => null,
            ],
            [
                'nama' => 'Chef',
                'deskripsi' => 'Divisi Chef atau Konsumsi bertugas menyediakan makanan dan minuman untuk kegiatan.',
                'koordinator_id' => null,
            ],
            [
                'nama' => 'Chiper',
                'deskripsi' => 'Divisi Chiper atau Pengelolaan Web bertugas merancang, memelihara, memperbarui, dan mengoptimalkan situs web resmi agar berfungsi secara optimal.',
                'koordinator_id' => null,
            ],
            [
                'nama' => 'Documenter',
                'deskripsi' => 'Divisi Documenter atau Publikasi dan Dokumentasi bertugas merekam dan mendokumentasikan kegiatan.',
                'koordinator_id' => null,
            ],
            [
                'nama' => 'Fundkeeper',
                'deskripsi' => 'Divisi Fundkeeper atau Bendahara bertugas mengelola dana dan keuangan.',
                'koordinator_id' => null,
            ],
            [
                'nama' => 'Gearmaster',
                'deskripsi' => 'Divisi Gearmaster atau Logistik bertugas mengelola perlengkapan dan fasilitas.',
                'koordinator_id' => null,
            ],
            [
                'nama' => 'Guardian',
                'deskripsi' => 'Divisi Guardian atau Keamanan bertugas menjaga keamanan dan kenyamanan peserta.',
                'koordinator_id' => null,
            ],
            [
                'nama' => 'Guider',
                'deskripsi' => 'Divisi Guider atau Mentor kelompok bertugas membimbing, mengarahkan, dan mendampingi anggota baru atau peserta agar bisa beradaptasi dan berkembang dengan baik.',
                'koordinator_id' => null,
            ],
            [
                'nama' => 'Informer',
                'deskripsi' => 'Divisi Informer atau Humas bertugas mengatur komunikasi, membangun citra positif, serta menjaga hubungan baik antara organisasi dan publiknya.',
                'koordinator_id' => null,
            ],
            [
                'nama' => 'Pathfinder',
                'deskripsi' => 'Divisi Pathfinder atau Acara bertugas merancang konsep dan menyusun rangkaian kegiatan sejak awal hingga akhir.',
                'koordinator_id' => null,
            ],
            [
                'nama' => 'Ranger',
                'deskripsi' => 'Divisi Ranger bertugas melakukan pembinaan, pengembangan, dan penyiapan anggota baru agar siap menjadi penerus kepengurusan serta menjaga keberlangsungan visi organisasi.',
                'koordinator_id' => null,
            ],
            [
                'nama' => 'Rescuer',
                'deskripsi' => 'Divisi Rescuer atau Kesehatan bertugas memberikan pertolongan pertama dan penanganan kesehatan.',
                'koordinator_id' => null,
            ],
            [
                'nama' => 'Scribe',
                'deskripsi' => 'Divisi Scribe atau Kestari bertugas mengurus administrasi, surat-menyurat, dan pengarsipan data.',
                'koordinator_id' => null,
            ],
            [
                'nama' => 'Stakeholder',
                'deskripsi' => 'Divisi Stakeholder bertugas mewakili dan mendorong kepentingan berbagai pihak yang terlibat dalam organisasi.',
                'koordinator_id' => null,
            ],
        ];

        foreach ($divisis as $divisi) {
            Divisi::firstOrCreate(
                ['nama' => $divisi['nama']],
                [
                    'deskripsi' => $divisi['deskripsi'],
                    'koordinator_id' => $divisi['koordinator_id'],
                ]
            );
        }
    }
}
