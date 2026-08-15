<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Notulensi;
use App\Models\Pengumuman;
use App\Models\Presensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. Ambil pengumuman terbaru
        $pengumumanList = Pengumuman::with('pembuat')
            ->latest('tanggal_publish')
            ->paginate(5);

        // 2. Ambil kegiatan mendatang atau kegiatan terakhir
        $kegiatanTerbaru = Kegiatan::where('tanggal', '>=', Carbon::today())
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->first()
            ?? Kegiatan::orderBy('tanggal', 'desc')
            ->orderBy('waktu_mulai', 'desc')
            ->first();

        // 3. Hitung ringkasan statistik
        $totalUserCount = User::where('status', true)->count();
        $hadirCount = 0;

        if ($kegiatanTerbaru) {
            $hadirCount = Presensi::where('kegiatan_id', $kegiatanTerbaru->id)
                ->whereIn('status_kehadiran', ['hadir', 'terlambat'])
                ->count();
        }

        $data = [
            'title'             => 'Dashboard',
            'pengumumanList'    => $pengumumanList,
            'pengumumanTerbaru' => $pengumumanList->first(),
            'kegiatanTerbaru'   => $kegiatanTerbaru,
            'totalUserCount'    => $totalUserCount,
            'hadirCount'        => $hadirCount,
            'notulensiList'     => Notulensi::with('kegiatan')->latest()->limit(8)->get(),
        ];

        return view('dashboard.index', $data);
    }
}
