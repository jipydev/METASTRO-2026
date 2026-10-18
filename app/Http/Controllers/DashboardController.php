<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Notulensi;
use App\Models\Pengumuman;
use App\Models\Tugas;
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
            return $this->peserta($request);
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

    public function peserta(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        // Cari tugas dengan deadline terdekat yang masih aktif / masa mendatang
        $deadlineTerdekat = null;
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('tugases')) {
                $deadlineTerdekat = Tugas::whereNotNull('tenggat_waktu')
                    ->where('tenggat_waktu', '>=', Carbon::now())
                    ->orderBy('tenggat_waktu', 'asc')
                    ->first();

                // Fallback jika semua deadline sudah lewat, ambil yang paling akhir atau terbaru
                if (!$deadlineTerdekat) {
                    $deadlineTerdekat = Tugas::whereNotNull('tenggat_waktu')
                        ->orderBy('tenggat_waktu', 'desc')
                        ->first();
                }
            }
        } catch (\Throwable $e) {
            $deadlineTerdekat = null;
        }

        $kegiatans = collect();
        $hasUnpublished = false;
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('kegiatans')) {
                $kegiatans = Kegiatan::where('jenis', 'peserta')
                    ->where('is_published', true)
                    ->orderBy('tanggal_mulai', 'asc')
                    ->get();

                $hasUnpublished = Kegiatan::where('jenis', 'peserta')
                    ->where('is_published', false)
                    ->exists();
            }
        } catch (\Throwable $e) {
            $kegiatans = collect();
        }

        $days = collect();
        $dayProgressData = [];

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('tugases')) {
                // Ambil daftar day unik yang ada di tabel tugas
                $days = Tugas::whereNotNull('day')
                    ->where('day', '!=', '')
                    ->distinct()
                    ->pluck('day')
                    ->sort(SORT_NATURAL | SORT_FLAG_CASE)
                    ->values();

                if ($days->isEmpty()) {
                    // Fallback menggunakan konstanta DAYS jika tabel kosong
                    $days = collect(Tugas::DAYS);
                }

                foreach ($days as $day) {
                    // Hitung total tugas untuk day ini (mendukung format "1", "Day 1", "DAY 1")
                    $numericDay = preg_replace('/[^0-9]/', '', $day);

                    $tugasList = Tugas::where('day', $day)
                        ->orWhere('day', 'Day ' . $numericDay)
                        ->orWhere('day', 'DAY ' . $numericDay)
                        ->orWhere('day', $numericDay)
                        ->get();

                    mw:
                    $totalTugas = $tugasList->count();
                    $tugasIds = $tugasList->pluck('id');

                    $tugasSelesai = 0;
                    if ($totalTugas > 0) {
                        // Hitung langsung ke tabel pengumpulan_tugases
                        $tugasSelesai = \App\Models\PengumpulanTugas::where('user_id', $user->id)
                            ->whereIn('tugas_id', $tugasIds)
                            ->count();

                        $percent = round(($tugasSelesai / $totalTugas) * 100);
                    } else {
                        $percent = 0;
                    }

                    // Format label agar rapi di view (misal: "DAY 1")
                    $labelKey = strtoupper($day);
                    if (!str_starts_with($labelKey, 'DAY')) {
                        $labelKey = 'DAY ' . $numericDay;
                    }

                    $dayProgressData[$day] = [
                        'total' => $totalTugas,
                        'selesai' => $tugasSelesai,
                        'percent' => $percent,
                    ];
                }
            }
        } catch (\Throwable $e) {
            $days = collect(['Day 1']);
            $dayProgressData['Day 1'] = ['total' => 0, 'selesai' => 0, 'percent' => 0];
        }

        $pengumumanTerbaru = null;
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('pengumumans')) {
                $pengumumanTerbaru = Pengumuman::with('pembuat.divisi')
                    ->visibleTo($user)
                    ->latest('tanggal_publish')
                    ->latest('created_at')
                    ->first();
            }
        } catch (\Throwable $e) {
            $pengumumanTerbaru = null;
        }

        return view('dashboard.peserta', [
            'title' => 'Dashboard',
            'user' => $user,
            'kegiatans' => $kegiatans,
            'hasUnpublished' => $hasUnpublished,
            'days' => $days,
            'dayProgressData' => $dayProgressData,
            'pengumumanTerbaru' => $pengumumanTerbaru,
            'deadlineTerdekat' => $deadlineTerdekat,
        ]);
    }
}