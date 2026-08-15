<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Services\QrCodeService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ScanController extends Controller
{
    /**
     * Lookup data user dari QR token (dipanggil saat kamera mendeteksi barcode).
     */
    public function lookup(Request $request, QrCodeService $qrService): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $user = $qrService->lookupByToken($request->token);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau akun pengguna dinonaktifkan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id'     => $user->id,
                'nama'   => $user->nama,
                'nim'    => $user->nim,
                'divisi' => $user->divisi?->nama ?? 'Tanpa Divisi',
                'photo'  => $user->foto
                    ? asset('storage/' . $user->foto)
                    : 'https://ui-avatars.com/api/?size=256&background=6366f1&color=fff&name=' . urlencode($user->nama),
            ],
        ]);
    }

    /**
     * Catat kehadiran ke tabel presensis setelah scan QR disetujui.
     */
    public function recordAttendance(Request $request): JsonResponse
    {
        $request->validate([
            'user_id'     => ['required', 'integer', 'exists:users,id'],
            'kegiatan_id' => ['nullable', 'integer', 'exists:kegiatans,id'],
        ]);

        try {
            // 1. Cari kegiatan yang aktif: berdasarkan kegiatan_id yang dikirim ATAU kegiatan hari ini yang status presensinya 'buka'
            $kegiatan = null;
            if ($request->filled('kegiatan_id')) {
                $kegiatan = Kegiatan::find($request->kegiatan_id);
            } else {
                $kegiatan = Kegiatan::where('status_presensi', 'buka')
                    ->whereDate('tanggal', Carbon::today())
                    ->first() ?? Kegiatan::where('status_presensi', 'buka')->first();
            }

            if (! $kegiatan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada kegiatan aktif atau sesi presensi belum dibuka.',
                ], 404);
            }

            // 2. Validasi status presensi kegiatan
            if ($kegiatan->status_presensi === 'tutup') {
                return response()->json([
                    'success' => false,
                    'message' => "Presensi untuk kegiatan '{$kegiatan->judul}' sudah DITUTUP.",
                ], 403);
            }

            $waktuSekarang = Carbon::now();

            // 3. Cek apakah user sudah pernah absen pada kegiatan ini
            $existing = Presensi::where('user_id', $request->user_id)
                ->where('kegiatan_id', $kegiatan->id)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => "Pengguna ini sudah melakukan presensi sebelumnya pada kegiatan '{$kegiatan->judul}'.",
                ], 409);
            }

            // 4. Tentukan status kehadiran (hadir vs terlambat)
            $statusKehadiran = 'hadir';
            if ($kegiatan->waktu_mulai) {
                $jadwalMulai = Carbon::parse($kegiatan->tanggal->format('Y-m-d') . ' ' . $kegiatan->waktu_mulai);
                if ($waktuSekarang->greaterThan($jadwalMulai)) {
                    $statusKehadiran = 'terlambat';
                }
            }

            // 5. Simpan catatan kehadiran ke tabel presensis
            Presensi::create([
                'user_id'          => $request->user_id,
                'kegiatan_id'      => $kegiatan->id,
                'status_kehadiran' => $statusKehadiran,
                'waktu_presensi'   => $waktuSekarang,
                'metode_presensi'  => 'qr_code',
                'keterangan'       => 'Presensi via Scan QR oleh ' . (Auth::user()?->nama ?? 'Petugas'),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Presensi ({$statusKehadiran}) berhasil dicatat untuk kegiatan '{$kegiatan->judul}'.",
            ]);
        } catch (\Throwable $e) {
            Log::error('Record attendance error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat menyimpan presensi.',
            ], 500);
        }
    }
}
