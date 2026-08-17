<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\AttendanceAlreadyRecordedException;
use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Kegiatan;
use App\Models\User;
use App\Services\AttendanceRecorder;
use App\Services\QrCodeService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ScanController extends Controller
{
    public function __construct(private AttendanceRecorder $recorder) {}

    /**
     * Lookup data user dari QR token (dipanggil saat kamera mendeteksi barcode).
     */
    public function lookup(Request $request, QrCodeService $qrService): JsonResponse
    {
        if (! Auth::user()?->canScanPresensi()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak akses untuk memindai QR.',
            ], 403);
        }

        $request->validate(
            ['token' => ['required', 'string']],
            ['token.required' => 'QR Code tidak terbaca. Coba pindai ulang.']
        );

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
                'id' => $user->id,
                'nama' => $user->nama,
                'nim' => $user->nim,
                'divisi' => $user->divisi instanceof Divisi ? $user->divisi->nama : 'Tanpa Divisi',
                'photo' => $user->foto
                    ? asset('storage/'.$user->foto)
                    : 'https://ui-avatars.com/api/?size=256&background=6366f1&color=fff&name='.urlencode($user->nama),
            ],
        ]);
    }

    /**
     * Catat kehadiran ke tabel presensis setelah scan QR disetujui.
     */
    public function store(Request $request): JsonResponse
    {
        if (! Auth::user()?->canScanPresensi()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak akses untuk memindai QR.',
            ], 403);
        }

        $request->validate(
            [
                'user_id' => ['required', 'integer', 'exists:users,id'],
                'kegiatan_id' => ['nullable', 'integer', 'exists:kegiatans,id'],
            ],
            [
                'user_id.required' => 'Pengguna tidak ditemukan.',
                'user_id.exists' => 'Pengguna tidak ditemukan.',
                'kegiatan_id.exists' => 'Kegiatan yang dipilih tidak ditemukan.',
            ]
        );

        try {
            $peserta = User::with(['divisi', 'jabatan'])
                ->whereKey($request->integer('user_id'))
                ->first();
            if (! $peserta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna tidak ditemukan.',
                ], 404);
            }

            $waktuSekarang = Carbon::now();

            $kegiatan = $request->filled('kegiatan_id')
                ? Kegiatan::query()->whereKey($request->integer('kegiatan_id'))->first()
                : (Kegiatan::query()
                    ->where('presensi_mulai', '<=', $waktuSekarang)
                    ->where('presensi_selesai', '>=', $waktuSekarang)
                    ->orderBy('tanggal', 'asc')
                    ->first()
                    ?? Kegiatan::query()->whereDate('tanggal', Carbon::today())->first());

            if (! $kegiatan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada kegiatan aktif atau sesi presensi belum dibuka.',
                ], 404);
            }

            if ($kegiatan->presensi_mulai && $waktuSekarang->lt($kegiatan->presensi_mulai)) {
                return response()->json([
                    'success' => false,
                    'message' => "Sesi presensi untuk kegiatan '{$kegiatan->nama}' belum dimulai.",
                ], 403);
            }

            if ($kegiatan->presensi_selesai && $waktuSekarang->gt($kegiatan->presensi_selesai)) {
                return response()->json([
                    'success' => false,
                    'message' => "Presensi untuk kegiatan '{$kegiatan->nama}' sudah DITUTUP.",
                ], 403);
            }

            $petugas = Auth::user();

            try {
                $presensi = $this->recorder->record($peserta, $kegiatan, $petugas, 'scan', $waktuSekarang);
            } catch (AttendanceAlreadyRecordedException $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 409);
            }

            $presensi->setRelation('kegiatan', $kegiatan);
            $presensi->setRelation('user', $peserta);

            return response()->json([
                'success' => true,
                'message' => "Presensi {$peserta->nama} ({$peserta->formatted_divisi_jabatan}) berhasil dicatat untuk kegiatan '{$kegiatan->nama}' dengan status {$presensi->status_tampilan}.",
            ]);
        } catch (\Throwable $e) {
            Log::error('Record attendance error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat menyimpan presensi.',
            ], 500);
        }
    }
}
