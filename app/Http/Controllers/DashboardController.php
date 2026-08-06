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



            $rapatTerbaru = Rapat::where('tanggal', '>=', now()->toDateString())
                                ->orderBy('tanggal', 'asc')
                                ->orderBy('jam', 'asc')
                                ->first() 
                            ?? Rapat::orderBy('tanggal', 'desc')
                                ->orderBy('jam', 'desc')
                                ->first();

            $kehadiranDivisi = null;
            if (auth()->check() && auth()->user()->hasAnyRole(['Koordinator Divisi', 'Wakil Koordinator Divisi'])) {
                $user = auth()->user();
                if ($user->divisi_id && $rapatTerbaru) {
                    $totalDivisi = \App\Models\User::role(['Panitia', 'Sekretaris'])
                                    ->where('divisi_id', $user->divisi_id)
                                    ->count();
                    
                    $hadirDivisi = \App\Models\ListPanitia::where('rapat_id', $rapatTerbaru->id)
                                    ->whereHas('user', function($q) use ($user) {
                                        $q->where('divisi_id', $user->divisi_id);
                                    })
                                    ->count();
                                    
                    $kehadiranDivisi = [
                        'nama_divisi' => $user->divisi->nama_divisi ?? 'Divisi',
                        'hadir' => $hadirDivisi,
                        'total' => $totalDivisi
                    ];
                }
            }

        return view('dashboard.index', [
            'title' => 'Dashboard',

            // Semua pengumuman
            'pengumumanList' => $pengumumanList,

            // Pengumuman terbaru
            'pengumumanTerbaru' => $pengumumanList->first(),

            'rapatTerbaru' => $rapatTerbaru,
            'kehadiranDivisi' => $kehadiranDivisi,

            'notulensi_list' => \App\Models\Notulensi::latest()->get(),
        ]);
    }
}
