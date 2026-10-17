<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $data = [
            // Ambil kegiatan peserta yang SUDAH di-publish untuk timeline utama
            'kegiatans' => Kegiatan::where('jenis', 'peserta')
                ->where('is_published', true)
                ->orderBy('tanggal_mulai', 'asc')
                ->orderBy('waktu_mulai', 'asc')
                ->get(),

            // Cek apakah ada kegiatan peserta yang BELUM di-publish untuk trigger "Coming Soon"
            'hasUnpublished' => Kegiatan::where('jenis', 'peserta')
                ->where('is_published', false)
                ->exists(),

            'title' => 'Home',
        ];

        return view('index', $data);
    }
}
