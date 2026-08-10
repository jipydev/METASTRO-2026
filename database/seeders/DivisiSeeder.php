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
                'nama_divisi' => 'Stakeholder',
                'deskripsi' => 'Divisi Stakeholder bertugas mewakili dan mendorong kepentingan berbagai pihak yang terlibat dalam organisasi.',
                'koordinator_divisi_nim' => null,
            ],
            [
                'nama_divisi' => 'Pathfinder',
                'deskripsi' => 'Divisi Pathfinder atau Acara bertugas merancang konsep dan menyusun rangkaian kegiatan sejak awal hingga akhir.',
                'koordinator_divisi_nim' => null,
            ],
            [
                'nama_divisi' => 'Archivist',
                'deskripsi' => 'Divisi Archivist atau Sekretaris bertugas mengelola administrasi, persuratan.',
                'koordinator_divisi_nim' => null,
            ],
            [
                'nama_divisi' => 'Fundkeeper',
                'deskripsi' => 'Divisi Fundkeeper atau Bendahara bertugas mengelola dana dan keuangan.',
                'koordinator_divisi_nim' => null,
            ],
            [
                'nama_divisi' => 'Guardian',
                'deskripsi' => 'Divisi Guardian atau Keamanan bertugas menjaga keamanan dan kenyamanan peserta.',
                'koordinator_divisi_nim' => null,
            ],
            [
                'nama_divisi' => 'Gearmaster',
                'deskripsi' => 'Divisi Gearmaster atau Logistik bertugas mengelola perlengkapan dan fasilitas.',
                'koordinator_divisi_nim' => null,
            ],
            [
                'nama_divisi' => 'Informer',
                'deskripsi' => 'Divisi Informer atau Humas bertugas mengatur komunikasi, membangun citra positif, serta menjaga hubungan baik antara organisasi dan publiknya.',
                'koordinator_divisi_nim' => null,
            ],
            [
                'nama_divisi' => 'Documenter',
                'deskripsi' => 'Divisi Documenter atau Publikasi dan Dokumentasi bertugas merekam dan mendokumentasikan kegiatan.',
                'koordinator_divisi_nim' => null,
            ],
            [
                'nama_divisi' => 'Chef',
                'deskripsi' => 'Divisi Chef atau Konsumsi bertugas menyediakan makanan dan minuman untuk kegiatan.',
                'koordinator_divisi_nim' => null,
            ],
            [
                'nama_divisi' => 'Guider',
                'deskripsi' => 'Divisi Guider atau Mentor kelompok tugas di dalam sebuah organisasi atau acara yang bertugas membimbing, mengarahkan, dan mendampingi anggota baru atau peserta agar bisa beradaptasi dan berkembang dengan baik.',
                'koordinator_divisi_nim' => null,
            ],
            [
                'nama_divisi' => 'Scribe',
                'deskripsi' => 'Divisi Scribe atau Kestari ertugas mengurus administrasi, surat-menyurat, dan pengarsipan data.',
                'koordinator_divisi_nim' => null,
            ],
            [
                'nama_divisi' => 'Chiper',
                'deskripsi' => 'Divisi Chiper atau Pengelolaan Web bertugas merancang, memelihara, memperbarui, dan mengoptimalkan situs web resmi agar berfungsi secara optimal.',
                'koordinator_divisi_nim' => null,
            ],
            [
                'nama_divisi' => 'Ranger',
                'deskripsi' => 'Divisi Ranger bertugas melakukan pembinaan, pengembangan, dan penyiapan anggota baru agar siap menjadi penerus kepengurusan serta menjaga keberlangsungan visi organisasi.',
                'koordinator_divisi_nim' => null,
            ],
        ];

        foreach ($divisis as $divisi) {
            Divisi::firstOrCreate([
                'nama_divisi' => $divisi['nama_divisi'],
                'deskripsi' => $divisi['deskripsi'],
                'koordinator_divisi_nim' => $divisi['koordinator_divisi_nim'],
            ]);
        }
    }
}
