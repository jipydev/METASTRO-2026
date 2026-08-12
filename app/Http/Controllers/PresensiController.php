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

    public function listPanitia()
    {
        return view('kegiatan.listPanitia');
    }

    /**
     * Mengubah status absensi (Buka / Tutup) secara manual oleh Sekretaris.
     */
    public function toggleAbsen(Request $request, $rapat)
    {
        $rapatModel = $rapat instanceof Rapat ? $rapat : Rapat::findOrFail($rapat);

        $request->validate([
            'status_absen' => 'required|in:Buka,Tutup,Dijadwalkan',
        ]);

        $rapatModel->update([
            'status_absen' => $request->status_absen,
        ]);

        $statusMessage = match($request->status_absen) {
            'Buka' => 'Absensi untuk ' . $rapatModel->judul . ' telah DIBUKA.',
            'Tutup' => 'Absensi untuk ' . $rapatModel->judul . ' telah DITUTUP.',
            'Dijadwalkan' => 'Absensi untuk ' . $rapatModel->judul . ' diubah ke mode DIJADWALKAN.',
            default => 'Status absensi ' . $rapatModel->judul . ' berhasil diperbarui.',
        };

        return redirect()->back()->with('success', $statusMessage);
    }

    /**
     * Memperbarui jadwal absensi (Waktu Buka, Telat, dan Tutup) oleh Sekretaris.
     */
    public function updateJadwalAbsen(Request $request, $rapat)
    {
        $rapatModel = $rapat instanceof Rapat ? $rapat : Rapat::findOrFail($rapat);

        $validated = $request->validate([
            'status_absen' => 'nullable|in:Buka,Tutup,Dijadwalkan',
            'waktu_buka' => 'nullable',
            'waktu_telat' => 'nullable',
            'waktu_tutup' => 'nullable',
        ]);

        $rapatModel->update([
            'status_absen' => $validated['status_absen'] ?? 'Dijadwalkan',
            'waktu_buka' => $validated['waktu_buka'] ?? null,
            'waktu_telat' => $validated['waktu_telat'] ?? null,
            'waktu_tutup' => $validated['waktu_tutup'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Jadwal absensi ' . $rapatModel->judul . ' berhasil diperbarui!');
    }
}
