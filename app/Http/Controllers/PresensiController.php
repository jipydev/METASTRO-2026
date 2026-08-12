<?php

namespace App\Http\Controllers;

use App\Models\Rapat;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    public function index(QrCodeService $qrService)
    {
        $user = Auth::user();
        $user->load('divisi');

        // Generate QR if not exists yet
        $qrUrl = $qrService->getQrUrl($user);
        if (! $qrUrl) {
            $qrService->generateForUser($user);
            $qrUrl = $qrService->getQrUrl($user);
        }

        return view('kegiatan.QR', [
            'user' => $user,
            'qrUrl' => $qrUrl,
        ]);
    }

    public function lihat()
    {
        return view('kegiatan.lihat');
    }

    public function listPanitia()
    {
        return view('kegiatan.listPanitia');
    }

    /**
     * Mengubah status absensi (Buka / Tutup) secara manual oleh Sekretaris.
     */
    public function toggleAbsen(Request $request, Rapat $rapat)
    {
        $request->validate([
            'status_absen' => 'required|in:Buka,Tutup,Dijadwalkan',
        ]);

        $rapat->update([
            'status_absen' => $request->status_absen,
        ]);

        $statusMessage = match($request->status_absen) {
            'Buka' => 'Absensi untuk ' . $rapat->judul . ' telah DIBUKA.',
            'Tutup' => 'Absensi untuk ' . $rapat->judul . ' telah DITUTUP.',
            'Dijadwalkan' => 'Absensi untuk ' . $rapat->judul . ' diubah ke mode DIJADWALKAN.',
        };

        return redirect()->back()->with('success', $statusMessage);
    }

    /**
     * Memperbarui jadwal absensi (Waktu Buka, Telat, dan Tutup) oleh Sekretaris.
     */
    public function updateJadwalAbsen(Request $request, Rapat $rapat)
    {
        $validated = $request->validate([
            'status_absen' => 'nullable|in:Buka,Tutup,Dijadwalkan',
            'waktu_buka' => 'nullable|date_format:H:i',
            'waktu_telat' => 'nullable|date_format:H:i',
            'waktu_tutup' => 'nullable|date_format:H:i',
        ]);

        $rapat->update([
            'status_absen' => $validated['status_absen'] ?? 'Dijadwalkan',
            'waktu_buka' => $validated['waktu_buka'] ?? null,
            'waktu_telat' => $validated['waktu_telat'] ?? null,
            'waktu_tutup' => $validated['waktu_tutup'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Jadwal absensi ' . $rapat->judul . ' berhasil diperbarui!');
    }
}
