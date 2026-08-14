<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ListPanitia;
use App\Models\Rapat;
use App\Services\QrCodeService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        if (! $user) {
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
                    ? asset('storage/'.$user->foto)
                    : 'https://ui-avatars.com/api/?size=256&background=fe5a1d&color=fff&name='.urlencode($user->name),
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
        ]);

        try {
            // 1. Cari Rapat Besar yang terjadwal HARI INI atau yang statusnya Buka
            $rapatHariIni = Rapat::whereDate('tanggal', Carbon::today())->first();

            if (! $rapatHariIni) {
                $rapatHariIni = Rapat::where('status_absen', 'Buka')->first();
            }

            if (! $rapatHariIni) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada jadwal Rapat hari ini atau absensi belum dibuka.',
                ], 404);
            }

            // 2. Evaluasi status_absen dan jadwal waktu yang diatur Sekretaris
            $waktuSekarang = Carbon::now();
            $currentTimeStr = $waktuSekarang->format('H:i:s');

            if ($rapatHariIni->status_absen === 'Tutup') {
                return response()->json([
                    'success' => false,
                    'message' => 'Absensi untuk '.$rapatHariIni->judul.' telah DITUTUP oleh Sekretaris.',
                ], 403);
            }

            if ($rapatHariIni->waktu_buka && $rapatHariIni->status_absen !== 'Buka') {
                if ($currentTimeStr < $rapatHariIni->waktu_buka) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Absensi belum dibuka. Absensi dijadwalkan buka pada pukul '.substr($rapatHariIni->waktu_buka, 0, 5).' WIB.',
                    ], 403);
                }
            }

            if ($rapatHariIni->waktu_tutup && $rapatHariIni->status_absen !== 'Buka') {
                if ($currentTimeStr > $rapatHariIni->waktu_tutup) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Absensi telah ditutup pada pukul '.substr($rapatHariIni->waktu_tutup, 0, 5).' WIB.',
                    ], 403);
                }
            }

            // 3. Cek apakah user sudah absen untuk rapat ini
            $existing = ListPanitia::where('user_id', $request->user_id)
                ->where('rapat_id', $rapatHariIni->id)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ini sudah melakukan presensi untuk '.$rapatHariIni->judul,
                ], 409);
            }

            // 4. Tentukan status (Hadir vs Telat)
            $status = 'Hadir';
            if ($rapatHariIni->waktu_telat) {
                if ($currentTimeStr >= $rapatHariIni->waktu_telat) {
                    $status = 'Telat';
                }
            } else {
                $jamRapat = Carbon::parse($rapatHariIni->jam);
                $batasWaktuHadir = $jamRapat->copy()->subMinutes(15);
                if ($waktuSekarang->greaterThan($batasWaktuHadir)) {
                    $status = 'Telat';
                }
            }

            // 4. Simpan ke list_panitias
            ListPanitia::create([
                'user_id' => $request->user_id,
                'rapat_id' => $rapatHariIni->id,
                'scanned_by' => auth()->id(),
                'jam_tap' => $waktuSekarang->format('H:i:s'),
                'status' => $status,
            ]);

            // 5. Update jumlah hadir di tabel Rapat
            $rapatHariIni->increment('hadir');

            return response()->json([
                'success' => true,
                'message' => 'Absensi '.$status.' berhasil dicatat untuk '.$rapatHariIni->judul,
            ]);

        } catch (\Exception $e) {
            Log::error('Record attendance error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan absensi.',
            ], 500);
        }
    }
}
