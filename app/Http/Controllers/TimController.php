<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TimController extends Controller
{
    private $dummyTims = [
        [
            'id' => 1,
            'nama' => 'Tim Andromeda 01',
            'slug' => 'tim-andromeda-01',
            'created_at' => '2026-09-27 08:00:00',
            'updated_at' => '2026-09-27 08:00:00',
            'guiders' => [
                [
                    'id' => 1,
                    'pembimbing_id' => 21,
                    'nama' => 'Farhan Aryasuta',
                    'email' => 'farhan.guider@metastro.id',
                    'nomor_hp' => '081234567890',
                ],
                [
                    'id' => 2,
                    'pembimbing_id' => 23,
                    'nama' => 'Nadya Maharani',
                    'email' => 'nadya.guider@metastro.id',
                    'nomor_hp' => '081234567891',
                ],
            ],
            'members' => [
                [
                    'id' => 1,
                    'anggota_id' => 101,
                    'nama' => 'Budi Santoso',
                    'nim' => '2601001',
                    'jenis_kelamin' => 'laki-laki',
                ],
                [
                    'id' => 2,
                    'anggota_id' => 102,
                    'nama' => 'Siti Nurhaliza',
                    'nim' => '2601002',
                    'jenis_kelamin' => 'perempuan',
                ],
            ],
        ],
        [
            'id' => 2,
            'nama' => 'Tim Centaurus 02',
            'slug' => 'tim-centaurus-02',
            'created_at' => '2026-09-27 08:30:00',
            'updated_at' => '2026-09-27 08:30:00',
            'guiders' => [
                [
                    'id' => 3,
                    'pembimbing_id' => 22,
                    'nama' => 'Dina Rahmawati',
                    'email' => 'dina.guider@metastro.id',
                    'nomor_hp' => '081298765432',
                ],
                [
                    'id' => 4,
                    'pembimbing_id' => 24,
                    'nama' => 'Reza Pratama',
                    'email' => 'reza.guider@metastro.id',
                    'nomor_hp' => '081298765433',
                ],
            ],
            'members' => [
                [
                    'id' => 3,
                    'anggota_id' => 103,
                    'nama' => 'Ahmad Rizky Pratama',
                    'nim' => '2601003',
                    'jenis_kelamin' => 'laki-laki',
                ],
            ],
        ],
    ];

    private $dummyPengumpulanTugases = [
        [
            'id' => 1,
            'tugas_id' => 1,
            'judul_tugas' => 'Resume Materi Kepemimpinan dan Etika Metastro',
            'jenis_tugas' => 'individu',
            'user_id' => 101,
            'tim_id' => null,
            'nama_pengumpul' => 'Budi Santoso',
            'perwakilan_label' => 'Individu',
            'nim_pengumpul' => '2601001',
            'asal_tim' => 'Tim Andromeda 01',
            'tautan_berkas' => 'https://drive.google.com/file/d/mock-resume-budi/view',
            'catatan_peserta' => 'Saya telah menyusun resume materi kepemimpinan lengkap 2 halaman PDF.',
            'catatan_pemeriksa' => null,
            'is_anonim' => true,
            'status' => 'pending',
            'dikumpulkan_at' => '2026-10-04 14:20:15',
        ],
        [
            'id' => 2,
            'tugas_id' => 2,
            'judul_tugas' => 'Video Yel-Yel dan Karya Maket Inovasi',
            'jenis_tugas' => 'tim',
            'user_id' => 101,
            'tim_id' => 1,
            'nama_pengumpul' => 'Budi Santoso (Perwakilan Tim)',
            'perwakilan_label' => 'Perwakilan Tim Andromeda 01',
            'nim_pengumpul' => '2601001',
            'asal_tim' => 'Tim Andromeda 01',
            'tautan_berkas' => 'https://youtube.com/watch?v=mock-video-andromeda',
            'catatan_peserta' => 'Tautan video yel-yel dan dokumentasi maket tim kami yang telah disepakati bersama.',
            'catatan_pemeriksa' => 'Kekompakan kelompok sangat baik, isi maket inovatif dan alur pengerjaan sesuai instruksi.',
            'is_anonim' => true,
            'status' => 'reviewed',
            'dikumpulkan_at' => '2026-10-08 19:30:00',
        ],
        [
            'id' => 3,
            'tugas_id' => 1,
            'judul_tugas' => 'Resume Materi Kepemimpinan dan Etika Metastro',
            'jenis_tugas' => 'individu',
            'user_id' => 103,
            'tim_id' => null,
            'nama_pengumpul' => 'Ahmad Rizky Pratama',
            'perwakilan_label' => 'Individu',
            'nim_pengumpul' => '2601003',
            'asal_tim' => 'Tim Centaurus 02',
            'tautan_berkas' => 'https://storage.metastro.id/tugas/ahmad_rizky_draft.pdf',
            'catatan_peserta' => 'Mohon koreksi jika ada kekurangan format penulisan resume.',
            'catatan_pemeriksa' => 'Format tulisan belum memenuhi instruksi (panjang resume kurang dari 2 halaman dan belum menyertakan sitasi). Mohon disesuaikan kembali.',
            'is_anonim' => true,
            'status' => 'rejected',
            'dikumpulkan_at' => '2026-10-03 11:10:00',
        ],
        [
            'id' => 4,
            'tugas_id' => 3,
            'judul_tugas' => 'Penyusunan Formasi Koreografi Angkatan',
            'jenis_tugas' => 'angkatan',
            'user_id' => 101,
            'tim_id' => null,
            'nama_pengumpul' => 'Budi Santoso (Perwakilan Angkatan)',
            'perwakilan_label' => 'Koordinator Angkatan 2026',
            'nim_pengumpul' => '2601001',
            'asal_tim' => 'Tim Andromeda 01',
            'tautan_berkas' => 'https://drive.google.com/file/d/mock-koreografi-angkatan/view',
            'catatan_peserta' => 'Denah formasi koreografi resmi angkatan telah selesai disusun bersama perwakilan seluruh tim.',
            'catatan_pemeriksa' => null,
            'is_anonim' => true,
            'status' => 'pending',
            'dikumpulkan_at' => '2026-10-14 16:45:00',
        ],
    ];

    private $dummyUnassignedMembers = [
        ['id' => 104, 'nama' => 'Dewi Lestari', 'nim' => '2601004', 'jenis_kelamin' => 'perempuan'],
        ['id' => 105, 'nama' => 'Eko Prasetyo', 'nim' => '2601005', 'jenis_kelamin' => 'laki-laki'],
        ['id' => 106, 'nama' => 'Fadhil Rahman', 'nim' => '2601006', 'jenis_kelamin' => 'laki-laki'],
    ];

    private $dummyAvailableGuiders = [
        ['id' => 25, 'nama' => 'Hendra Saputra', 'email' => 'hendra.guider@metastro.id'],
        ['id' => 26, 'nama' => 'Indah Permata', 'email' => 'indah.guider@metastro.id'],
    ];

    public function index()
    {
        $tims = $this->dummyTims;
        return view('tim.index', compact('tims'));
    }

    public function store(Request $request)
    {
        return redirect()->route('tim-guider.index')->with('success', 'Tim bimbingan baru berhasil dibuat (dummy).');
    }

    public function show($id)
    {
        $tim = collect($this->dummyTims)->firstWhere('id', (int) $id) ?? $this->dummyTims[0];
        $unassignedMembers = $this->dummyUnassignedMembers;
        $availableGuiders = $this->dummyAvailableGuiders;

        return view('tim.show', compact('tim', 'unassignedMembers', 'availableGuiders'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->back()->with('success', 'Data tim berhasil diperbarui (dummy).');
    }

    public function destroy($id)
    {
        return redirect()->route('tim-guider.index')->with('success', 'Tim berhasil dihapus (dummy).');
    }

    public function reviewIndex()
    {
        $submisiList = $this->dummyPengumpulanTugases;
        return view('review-tugas.index', compact('submisiList'));
    }

    public function reviewUpdate(Request $request, $id)
    {
        return redirect()->route('review-tugas.index')->with('success', 'Hasil reviu tugas dan komentar anonim berhasil disimpan (dummy).');
    }
}
