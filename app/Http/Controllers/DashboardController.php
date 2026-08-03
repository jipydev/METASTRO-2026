<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengumuman; // Panggil Model Pengumuman
use App\Models\Rapat;      // Panggil Model Rapat

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Data standar halaman
        $data = [
            'title' => "Dashboard"
        ];

        // 2. Ambil pengumuman yang statusnya 'Publish' paling terbaru
        $pengumuman = Pengumuman::where('status', 'Publish')->latest()->first();

        // 3. Ambil rapat terbaru untuk data Presensi & Timeline
        $rapatTerbaru = Rapat::latest()->first(); 
        
        // 4. Ambil semua data rapat untuk card Notulensi
        $notulensi_list = Rapat::all(); 

        // Kirim semua variabel ke view dashboard.index
        return view('dashboard.index', compact('data', 'pengumuman', 'rapatTerbaru', 'notulensi_list'));
    }
}