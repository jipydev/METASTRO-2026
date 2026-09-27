<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Routing\Controller;

class TugasController extends Controller
{
    /** Dummy master data for tugas */
    private $dummyTugases = [
        [
            'id' => 1,
            'judul' => 'Resume Materi Kepemimpinan dan Etika Metastro',
            'deskripsi' => 'Buat rangkuman materi kepemimpinan minimal 2 halaman format PDF.',
            'jenis' => 'individu',
            'nilai_maksimal' => 100,
            'tenggat_waktu' => '2026-10-05 23:59:00',
            'pembuat_nama' => 'Panitia Divisi Acara',
            'created_at' => '2026-09-27 08:30:00',
        ],
        [
            'id' => 2,
            'judul' => 'Video Yel-Yel dan Karya Maket Inovasi',
            'deskripsi' => 'Unggah rekaman video yel-yel kelompok ke media penyimpanan daring dan sertakan tautan berkas deskripsi rancangan maket.',
            'jenis' => 'tim',
            'nilai_maksimal' => 100,
            'tenggat_waktu' => '2026-10-10 18:00:00',
            'pembuat_nama' => 'Panitia Divisi Acara',
            'created_at' => '2026-09-27 09:15:00',
        ],
        [
            'id' => 3,
            'judul' => 'Penyusunan Formasi Koreografi Angkatan',
            'deskripsi' => 'Koordinasi pembuatan denah dan pola gerakan koreografi angkatan oleh koordinator angkatan bersama seluruh peserta.',
            'jenis' => 'angkatan',
            'nilai_maksimal' => 100,
            'tenggat_waktu' => '2026-10-15 20:00:00',
            'pembuat_nama' => 'Panitia Divisi Acara',
            'created_at' => '2026-09-27 10:00:00',
        ],
    ];

    public function index()
    {
        $tugases = $this->dummyTugases;
        return view('tugas.index', compact('tugases'));
    }

    public function create()
    {
        return view('tugas.create');
    }

    public function store(Request $request)
    {
        // No persistence – flash success and redirect back to index
        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil dibuat (dummy).');
    }

    public function edit($id)
    {
        $tugas = collect($this->dummyTugases)->firstWhere('id', (int) $id);
        return view('tugas.edit', compact('tugas'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil diperbarui (dummy).');
    }

    public function destroy($id)
    {
        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil dihapus (dummy).');
    }
}
