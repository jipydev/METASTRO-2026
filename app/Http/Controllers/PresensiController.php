<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Services\QrCodeService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PresensiController extends Controller
{
    /**
     * Rekap matriks presensi per kegiatan (monitoring kehadiran semua panitia/peserta).
     */
    public function monitoring(Request $request): View
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (! $currentUser->canViewPanitiaList()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat rekap kehadiran.');
        }

        $kegiatans = Kegiatan::orderBy('tanggal', 'desc')->limit(25)->get();
        $selectedKegiatanId = $request->query('kegiatan_id', $kegiatans->first()?->id);
        $selectedKegiatan = $kegiatans->firstWhere('id', $selectedKegiatanId);

        $statusFilter = $request->query('status'); // hadir, terlambat, izin, alpa
        $divisiFilter = $request->query('divisi_id');

        $usersData = [];
        if ($selectedKegiatan) {
            // Ambil semua user aktif
            $users = User::where('status', true)
                ->with(['divisi', 'jabatan'])
                ->when($divisiFilter, fn($q) => $q->where('divisi_id', $divisiFilter))
                ->orderBy('nama')
                ->get();

            // Ambil record presensi untuk kegiatan ini (O(1) Map Lookup)
            $presensiMap = Presensi::where('kegiatan_id', $selectedKegiatan->id)
                ->get()
                ->keyBy('user_id');

            // Evaluasi status setiap user
            $waktuSelesaiKegiatan = $selectedKegiatan->waktu_selesai
                ? Carbon::parse($selectedKegiatan->tanggal->format('Y-m-d') . ' ' . $selectedKegiatan->waktu_selesai)
                : Carbon::parse($selectedKegiatan->tanggal->format('Y-m-d') . ' ' . $selectedKegiatan->waktu_mulai)->addHours(3);

            $isKegiatanPassed = now()->greaterThan($waktuSelesaiKegiatan);

            foreach ($users as $user) {
                $presensi = $presensiMap->get($user->id);

                if ($presensi) {
                    $status = $presensi->status_kehadiran; // hadir, terlambat, izin
                    $waktuPresensi = optional($presensi->waktu_presensi)->format('H:i');
                } else {
                    $status = $isKegiatanPassed ? 'alpa' : 'belum_hadir';
                    $waktuPresensi = '-';
                }

                // Filter status
                if ($statusFilter && $status !== $statusFilter) {
                    continue;
                }

                $usersData[] = [
                    'id'             => $user->id,
                    'nama'           => $user->nama,
                    'nim'            => $user->nim,
                    'divisi'         => $user->divisi?->nama ?? 'Umum',
                    'jabatan'        => $user->jabatan?->nama ?? 'Anggota',
                    'status'         => $status,
                    'waktu_presensi' => $waktuPresensi,
                    'keterangan'     => $presensi?->keterangan ?? '-',
                ];
            }
        }

        return view('presensi.monitoring', [
            'title'            => 'Monitoring Kehadiran',
            'kegiatans'        => $kegiatans,
            'selectedKegiatan' => $selectedKegiatan,
            'usersData'        => $usersData,
            'statusFilter'     => $statusFilter,
            'divisiFilter'     => $divisiFilter,
        ]);
    }

    /**
     * Menampilkan halaman QR Code Absensi milik user yang sedang login.
     */
    public function qr(QrCodeService $qrService): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load(['divisi', 'jabatan']);

        // Pastikan file QR Code SVG sudah digenerate di storage
        $qrUrl = $qrService->getQrUrl($user);
        if (! $qrUrl) {
            $qrService->generateForUser($user);
            $qrUrl = $qrService->getQrUrl($user);
        }

        return view('presensi.qr', [
            'title' => 'QR Absensi Saya',
            'user'  => $user,
            'qrUrl' => $qrUrl,
        ]);
    }

    /**
     * Menampilkan halaman Scanner Barcode Kamera untuk Petugas / Panitia.
     */
    public function scan(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->canScanPresensi()) {
            abort(403, 'Akses scan presensi hanya untuk Admin dan Divisi Ranger / Presensi.');
        }

        // Ambil kegiatan aktif hari ini atau sesi yang sedang dibuka
        $kegiatanAktif = Kegiatan::where('status_presensi', 'buka')
            ->whereDate('tanggal', Carbon::today())
            ->first() ?? Kegiatan::where('status_presensi', 'buka')->first();

        return view('presensi.scan', [
            'title'         => 'Scanner Presensi',
            'kegiatanAktif' => $kegiatanAktif,
        ]);
    }

    /**
     * Mengubah status sesi absensi (buka / tutup / dijadwalkan) oleh Archivist / Admin.
     */
    public function toggleAbsen(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->canManageSekretariat()) {
            abort(403, 'Hanya Sekretaris/Archivist dan Admin yang dapat mengatur sesi presensi.');
        }

        $validated = $request->validate([
            'status_presensi' => 'required|in:buka,tutup,dijadwalkan',
        ]);

        $kegiatan->update([
            'status_presensi' => $validated['status_presensi'],
        ]);

        $statusLabel = match ($validated['status_presensi']) {
            'buka'        => 'DIBUKA',
            'tutup'       => 'DITUTUP',
            'dijadwalkan' => 'DIJADWALKAN',
        };

        return back()->with('success', "Sesi presensi untuk '{$kegiatan->judul}' berhasil {$statusLabel}.");
    }

    /**
     * Rekap riwayat presensi per kegiatan atau presensi pribadi.
     */
    public function history(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $presensis = Presensi::with(['kegiatan', 'user.divisi'])
            ->when(! $user->isAdmin() && ! $user->canScanPresensi(), function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest('waktu_presensi')
            ->paginate(20)
            ->withQueryString();

        return view('presensi.history', [
            'title'     => 'Rekap Riwayat Presensi',
            'presensis' => $presensis,
        ]);
    }
}
