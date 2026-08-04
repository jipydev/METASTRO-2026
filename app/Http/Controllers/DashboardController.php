<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\Rapat;

class DashboardController extends Controller
{
    public function index()
    {
        $pengumumanList = Pengumuman::with('pembuat')
            ->latest('tanggal_publish')
            ->paginate(5);



        return view('dashboard.index', [
            'title' => 'Dashboard',

            // Semua pengumuman
            'pengumumanList' => $pengumumanList,

            // Pengumuman terbaru
            'pengumumanTerbaru' => $pengumumanList->first(),

            'rapatTerbaru' => Rapat::latest()->first(),

            'notulensi_list' => Rapat::latest()->get(),
        ]);
    }
}
