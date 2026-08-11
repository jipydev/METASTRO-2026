<?php

namespace App\Http\Controllers;

use App\Models\ListPanitia;
use App\Models\Notulensi;
use App\Models\Pengumuman;
use App\Models\Rapat;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $pengumumanList = Pengumuman::with('pembuat')
            ->latest('tanggal_publish')
            ->paginate(5);

        $rapatTerbaru = Rapat::where('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam', 'asc')
            ->first()
                        ?? Rapat::orderBy('tanggal', 'desc')
                            ->orderBy('jam', 'desc')
                            ->first();

        $totalUserCount = User::where('status_aktif', true)->count();
        $hadirCount = 0;

        if ($rapatTerbaru) {
            $hadirCount = ListPanitia::where('rapat_id', $rapatTerbaru->id)->count();
        }

        return view('dashboard.index', [
            'title' => 'Dashboard',

            // Semua pengumuman
            'pengumumanList' => $pengumumanList,

            // Pengumuman terbaru
            'pengumumanTerbaru' => $pengumumanList->first(),

            'rapatTerbaru' => $rapatTerbaru,

            'totalUserCount' => $totalUserCount,

            'hadirCount' => $hadirCount,

            'notulensi_list' => Notulensi::latest()->get(),
        ]);
    }
}
