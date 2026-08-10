<?php

namespace App\Http\Controllers;

use App\Models\Notulensi;
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

            'rapatTerbaru' => Rapat::where('tanggal', '>=', now()->toDateString())
                ->orderBy('tanggal', 'asc')
                ->orderBy('jam', 'asc')
                ->first()
                            ?? Rapat::orderBy('tanggal', 'desc')
                                ->orderBy('jam', 'desc')
                                ->first(),

            'notulensi_list' => Notulensi::latest()->get(),
        ]);
    }
}
