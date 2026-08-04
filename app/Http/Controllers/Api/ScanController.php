<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\QrCodeService;
use App\Models\absensi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ScanController extends Controller
{
    /**
     * Lookup user data from QR token (called by scanner).
     */
    public function lookup(Request $request, QrCodeService $qrService): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $user = $qrService->lookupByToken($request->token);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau user tidak aktif.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'nama' => $user->name,
                'divisi' => $user->divisi?->nama_divisi ?? 'Belum ada divisi',
                'photo' => $user->foto
                    ? asset('storage/' . $user->foto)
                    : 'https://ui-avatars.com/api/?size=256&background=065E75&color=fff&name=' . urlencode($user->name),
            ],
        ]);
    }

    /**
     * Record attendance after scan is accepted.
     */
    public function recordAttendance(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'jadwal_id' => ['nullable', 'integer', 'exists:jadwal,id'],
        ]);

        try {
            // If jadwal_id is provided, check if already recorded for this schedule
            if ($request->jadwal_id) {
                $existing = absensi::where('user_id', $request->user_id)
                    ->where('jadwal_id', $request->jadwal_id)
                    ->first();

                if ($existing) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User sudah absen untuk jadwal ini.',
                    ], 409);
                }

                absensi::create([
                    'jadwal_id' => $request->jadwal_id,
                    'user_id' => $request->user_id,
                    'status' => 'Hadir',
                    'waktu_absen' => now(),
                ]);
            } else {
                // Cari jadwal yang sedang berlangsung hari ini
                $jadwalHariIni = \App\Models\Jadwal::where('tanggal', now()->toDateString())
                    ->where('status', 'Berlangsung')
                    ->first();

                if (!$jadwalHariIni) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada jadwal yang sedang berlangsung saat ini.',
                    ], 404);
                }

                $existing = absensi::where('user_id', $request->user_id)
                    ->where('jadwal_id', $jadwalHariIni->id)
                    ->first();

                if ($existing) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User sudah absen untuk jadwal ini.',
                    ], 409);
                }

                absensi::create([
                    'jadwal_id' => $jadwalHariIni->id,
                    'user_id' => $request->user_id,
                    'status' => 'Hadir',
                    'waktu_absen' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil dicatat.',
            ]);
        } catch (\Exception $e) {
            Log::error('Record attendance error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan absensi.',
            ], 500);
        }
    }
}
