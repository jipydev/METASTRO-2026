<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Notulensi;
use App\Models\Pengumuman;
use App\Models\Presensi;
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
            $rekap = Presensi::where('kegiatan_id', $kegiatanTerbaru->id)
                ->selectRaw("
            SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir,
            SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as izin,
            SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as sakit
        ")
                ->first();

            $hadirCount = (int) ($rekap->hadir ?? 0);
            $izinCount = (int) ($rekap->izin ?? 0);
            $sakitCount = (int) ($rekap->sakit ?? 0);
            $belumAbsenCount = max(0, $totalUserCount - $hadirCount - $izinCount - $sakitCount);
        }

        return view('dashboard.index', [
            'title' => 'Dashboard',
            'pengumumanList' => $pengumumanList,
            'pengumumanTerbaru' => $pengumumanList->first(),
            'kegiatanTerbaru' => $kegiatanTerbaru,
            'totalUserCount' => $totalUserCount,
            'hadirCount' => $hadirCount,
            'izinCount' => $izinCount,
            'sakitCount' => $sakitCount,
            'belumAbsenCount' => $belumAbsenCount,
            'notulensiList' => Notulensi::with(['kegiatan', 'pembuat.divisi'])->latest()->limit(3)->get(),
            'kegiatanOptions' => Kegiatan::orderBy('tanggal', 'desc')->limit(50)->get(['id', 'nama', 'tanggal']),
        ]);
    }
}
