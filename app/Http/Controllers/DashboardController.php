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
            'title' => "Dashboard",
            'pengumuman' => Pengumuman::where('status', 'Publish')->latest()->first(),
            'rapatTerbaru' => Rapat::latest()->first(),
            'notulensi_list' => Rapat::all(),
        ];

        // Kirim semua variabel ke view dashboard.index
        return view('dashboard.index', $data);
    }
}