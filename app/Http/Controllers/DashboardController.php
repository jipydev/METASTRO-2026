<?php

namespace App\Http\Controllers;

use App\Models\Hukuman;
use App\Models\Kegiatan;
use App\Models\Notulensi;
use App\Models\Pengumuman;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $pengumumanList = Pengumuman::with('pembuat.divisi')
            ->visibleTo($user)
            ->latest('tanggal_publish')
            ->latest('created_at')
            ->limit(3)
            ->get();

        // 1. Prioritaskan mencari kegiatan yang presensinya sedang BUKA saat ini berdasarkan waktu
        $kegiatanTerbaru = Kegiatan::where('presensi_mulai', '<=', Carbon::now())
            ->where('presensi_selesai', '>=', Carbon::now())
            ->orderBy('tanggal', 'asc')
            ->first();

        // Hanya menghitung user aktif yang memiliki role 'admin' atau 'panitia'
        $totalUserCount = User::where('status', true)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['admin', 'panitia']);
            })
            ->count();

        $hadirCount = 0;
        $terlambatCount = 0;
        $izinCount = 0;
        $sakitCount = 0;
        $belumAbsenCount = $totalUserCount;

        if (! $kegiatanTerbaru) {
            $kegiatanTerbaru = Kegiatan::where('tanggal', '>=', Carbon::today())
                ->orderBy('tanggal', 'asc')
                ->orderBy('waktu_mulai', 'asc')
                ->first()
                ?? Kegiatan::orderBy('tanggal', 'desc')
                    ->orderBy('waktu_mulai', 'desc')
                    ->first();
        }

        if ($kegiatanTerbaru) {
            $rekap = $kegiatanTerbaru->rekapKehadiran($totalUserCount);
            $hadirCount = $rekap['hadir'];
            $terlambatCount = $rekap['terlambat'];
            $izinCount = $rekap['izin'];
            $sakitCount = $rekap['sakit'];
            $belumAbsenCount = $rekap['belum'] + $rekap['alpa'];
        }

        return view('dashboard.index', [
            'title' => 'Dashboard',
            'pengumumanList' => $pengumumanList,
            'pengumumanTerbaru' => $pengumumanList->first(),
            'kegiatanTerbaru' => $kegiatanTerbaru,
            'totalUserCount' => $totalUserCount,
            'hadirCount' => $hadirCount,
            'terlambatCount' => $terlambatCount,
            'izinCount' => $izinCount,
            'sakitCount' => $sakitCount,
            'belumAbsenCount' => $belumAbsenCount,
            'notulensiList' => Notulensi::with(['kegiatan', 'pembuat.divisi'])->latest()->limit(3)->get(),
            'kegiatanOptions' => Kegiatan::orderBy('tanggal', 'desc')->limit(50)->get(['id', 'nama', 'tanggal']),
            'hukumanStats' => $user->canManageHukuman() ? Hukuman::rekapForManager($user) : null,
        ]);
    }
}
