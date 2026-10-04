<?php

namespace App\Http\Controllers;

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

        // Jika user adalah Peserta, tampilkan Dashboard khusus Peserta
        if ($user && $user->isPeserta()) {
            return $this->pesertaDashboard($request);
        }

        $pengumumanList = Pengumuman::with('pembuat.divisi')
            ->visibleTo($user)
            ->latest('tanggal_publish')
            ->latest('created_at')
            ->limit(3)
            ->get();

        // 1. Prioritaskan mencari kegiatan yang presensinya sedang BUKA saat ini berdasarkan waktu
        $kegiatanTerbaru = Kegiatan::where('presensi_mulai', '<=', Carbon::now())
            ->where('presensi_selesai', '>=', Carbon::now())
            ->orderBy('tanggal_mulai', 'asc')
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
            $kegiatanTerbaru = Kegiatan::where('tanggal_mulai', '>=', Carbon::today())
                ->orderBy('tanggal_mulai', 'asc')
                ->orderBy('waktu_mulai', 'asc')
                ->first()
                ?? Kegiatan::orderBy('tanggal_mulai', 'desc')
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
            'kegiatanOptions' => Kegiatan::orderBy('tanggal_mulai', 'desc')->limit(50)->get(['id', 'nama', 'tanggal_mulai', 'tanggal_selesai']),
        ]);
    }

    public function pesertaDashboard(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $kegiatans = collect();
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('kegiatans')) {
                $kegiatans = Kegiatan::orderBy('tanggal', 'asc')->get();
            }
        } catch (\Throwable $e) {
            $kegiatans = collect();
        }

        $tugases = collect();
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('tugases')) {
                $tugases = \App\Models\Tugas::orderBy('tenggat_waktu', 'asc')->get();
            }
        } catch (\Throwable $e) {
            $tugases = collect();
        }

        return view('peserta.dashboard', [
            'title' => 'Dashboard Peserta',
            'user' => $user,
            'kegiatans' => $kegiatans,
            'tugases' => $tugases,
        ]);
    }
}
