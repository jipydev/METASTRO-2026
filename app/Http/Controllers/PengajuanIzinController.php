<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengajuanIzinRequest;
use App\Models\Kegiatan;
use App\Models\PengajuanIzin;
use App\Models\Presensi;
use App\Models\User;
use App\Services\FileCompressionService;
use App\Services\NotificationDispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PengajuanIzinController extends Controller
{
    public function __construct(private NotificationDispatcher $notifications) {}

    /**
     * Menampilkan riwayat pengajuan izin milik user yang sedang login.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        // if(!$user->isPanitia()) {
        //     abort(404, 'Halaman tidak ditemukan.');
        // }

        $pengajuanList = PengajuanIzin::where('user_id', $user->id)
            ->with(['kegiatan', 'user.divisi', 'user.jabatan', 'reviewerKoordinator', 'reviewerRanger'])
            ->latest()
            ->paginate(10);

        return view('pengajuan-izin.history', [
            'title' => 'Riwayat Izin Saya',
            'pengajuanList' => $pengajuanList,
        ]);
    }

    /**
     * Menampilkan form pengajuan izin baru.
     */
    public function create()
    {

        $user = Auth::user();

        $kegiatans = Kegiatan::query()
            ->where('is_published', true) // Hanya tampilkan kegiatan yang sudah dipublish
            // Sesuaikan logika filter role pengguna di bawah ini:
            ->when($user->isPanitia(), function ($query) {
                $query->whereIn('tipe', ['panitia', 'umum']);
            })
            ->when(!$user->isPanitia(), function ($query) {
                $query->where('tipe', 'umum');
            })
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('pengajuan-izin.create', compact('kegiatans'));
    }

    /**
     * Menyimpan data pengajuan izin.
     */
    public function store(StorePengajuanIzinRequest $request, FileCompressionService $files): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validated();
        $kegiatan = Kegiatan::findOrFail($validated['kegiatan_id']);

        // --------------------------------------------------------------------------
        // 1. CEK DUPLIKASI ATAU BENTROK JADWAL IZIN
        // --------------------------------------------------------------------------

        // A. Cek apakah sudah pernah mengajukan izin untuk KEGIATAN YANG SAMA
        $existingIzinKegiatan = PengajuanIzin::where('user_id', $user->id)
            ->where('kegiatan_id', $kegiatan->id)
            ->first();

        if ($existingIzinKegiatan) {
            // Jika statusnya bukan pending (misal: disetujui / ditolak), tidak boleh kirim lagi
            if ($existingIzinKegiatan->status !== 'pending') {
                return back()
                    ->withInput()
                    ->with('error', "Anda sudah pernah mengajukan izin untuk kegiatan '{$kegiatan->nama}'.");
            }
        }

        // B. Cek apakah ada BENTROK WAKTU (Tanggal & Jam yang sama) dengan kegiatan lain
        $waktuMulai = $kegiatan->waktu_mulai;
        $waktuSelesai = $kegiatan->waktu_selesai;

        $bentrokJadwal = PengajuanIzin::where('user_id', $user->id)
            ->where('kegiatan_id', '!=', $kegiatan->id)
            ->where('status', '!=', 'rejected') // Abaikan jika izin sebelumnya sudah ditolak
            ->whereHas('kegiatan', function ($query) use ($kegiatan, $waktuMulai, $waktuSelesai) {
                $query->where('tanggal', $kegiatan->tanggal);

                // Hanya cek bentrok jam jika kedua kegiatan memiliki jam mulai & selesai
                if (!empty($waktuMulai) && !empty($waktuSelesai)) {
                    $query->whereNotNull('waktu_mulai')
                        ->whereNotNull('waktu_selesai')
                        ->where(function ($q) use ($waktuMulai, $waktuSelesai) {
                            $q->whereBetween('waktu_mulai', [$waktuMulai, $waktuSelesai])
                                ->orWhereBetween('waktu_selesai', [$waktuMulai, $waktuSelesai])
                                ->orWhere(function ($sub) use ($waktuMulai, $waktuSelesai) {
                                    $sub->where('waktu_mulai', '<=', $waktuMulai)
                                        ->where('waktu_selesai', '>=', $waktuSelesai);
                                });
                        });
                }
            })
            ->first();

        if ($bentrokJadwal) {
            return back()
                ->withInput()
                ->with('error', "Anda sudah memiliki pengajuan izin pada tanggal dan jam yang sama untuk kegiatan '{$bentrokJadwal->kegiatan->nama}'.");
        }

        // --------------------------------------------------------------------------
        // 2. PROSES UPLOAD FILE & SIMPAN DATA
        // --------------------------------------------------------------------------

        $suratPath = $request->hasFile('surat_izin')
            ? $files->store($request->file('surat_izin'), 'izin/surat')
            : null;

        $buktiPath = $request->hasFile('bukti')
            ? $files->store($request->file('bukti'), 'izin/bukti')
            : null;

        $user->loadMissing('jabatan', 'divisi');
        $skipKoordinator = $user->skipsKoordinatorIzinReview();

        // Jika memperbarui izin yang masih berstatus 'pending' pada kegiatan yang sama
        if ($existingIzinKegiatan) {
            $wasPendingKoordinator = $existingIzinKegiatan->status_koordinator === 'pending';
            $data = [
                'jenis_izin' => $validated['jenis_izin'],
                'alasan' => $validated['alasan'],
                'surat_izin' => $suratPath ?? $existingIzinKegiatan->surat_izin,
                'bukti' => $buktiPath ?? $existingIzinKegiatan->bukti,
            ];

            if ($skipKoordinator && $existingIzinKegiatan->status_koordinator === 'pending') {
                $data = array_merge($data, $this->autoApproveKoordinatorPayload($user));
            }

            $existingIzinKegiatan->update($data);

            if ($skipKoordinator && $wasPendingKoordinator) {
                $this->notifications->izinSubmitted($existingIzinKegiatan->fresh(['user.divisi', 'user.jabatan', 'kegiatan']));
            }

            return redirect()->route('dashboard.pengajuan-izin.index')
                ->with('success', 'Pengajuan izin yang masih menunggu review berhasil dilengkapi.');
        }

        // Buat pengajuan izin baru
        $pengajuan = PengajuanIzin::create(array_merge(
            [
                'user_id' => $user->id,
                'kegiatan_id' => $kegiatan->id,
                'tanggal_izin' => $kegiatan->tanggal,
                'jenis_izin' => $validated['jenis_izin'],
                'alasan' => $validated['alasan'],
                'surat_izin' => $suratPath,
                'bukti' => $buktiPath,
                'status_ranger' => 'pending',
            ],
            $skipKoordinator
                ? $this->autoApproveKoordinatorPayload($user)
                : ['status_koordinator' => 'pending', 'status' => 'pending']
        ));

        $this->notifications->izinSubmitted($pengajuan->load(['user.divisi', 'user.jabatan', 'kegiatan']));

        return redirect()->route('dashboard.pengajuan-izin.index')
            ->with('success', $skipKoordinator
                ? 'Pengajuan izin berhasil dikirim dan menunggu verifikasi Ranger.'
                : 'Pengajuan izin berhasil dikirim dan menunggu verifikasi Koordinator.');
    }

    /**
     * Menampilkan daftar review pengajuan izin (untuk Koordinator, Ranger, Admin).
     */
    public function review(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        $filter = $request->query('filter', 'pending');

        // if (!$user->isPanitia()) {
        //     abort(404, 'Halaman tidak ditemukan.');
        // }

        if (! $user->canReviewIzin()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mereview izin.');
        }

        $query = PengajuanIzin::with([
            'kegiatan',
            'user.divisi',
            'user.jabatan',
            'reviewerKoordinator',
            'reviewerRanger',
        ]);

        if ($user->canViewAllIzinReviews()) {
            if ($filter === 'pending') {
                $query->whereIn('status', ['pending', 'diproses']);
            } elseif ($filter === 'approved') {
                $query->where('status', 'approved');
            } elseif ($filter === 'rejected') {
                $query->where('status', 'rejected');
            }
        } else {
            $query->whereHas('user', fn($q) => $q->where('divisi_id', $user->divisi_id)->where('id', '!=', $user->id));
            if ($filter === 'pending') {
                $query->where('status_koordinator', 'pending');
            } elseif ($filter === 'approved') {
                $query->where('status_koordinator', 'approved');
            } elseif ($filter === 'rejected') {
                $query->where('status_koordinator', 'rejected');
            }
        }

        return view('pengajuan-izin.review', [
            'title' => 'Review Pengajuan Izin',
            'pengajuanList' => $query->latest()->paginate(15)->withQueryString(),
            'filter' => $filter,
        ]);
    }

    /**
     * Menyetujui pengajuan izin.
     */
    public function approve(Request $request, PengajuanIzin $pengajuanIzin): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $catatan = $request->input('catatan');

        if (!$user->isPanitia()) {
            abort(404, 'Halaman tidak ditemukan.');
        }

        if (in_array($pengajuanIzin->status, ['approved', 'rejected'], true)) {
            return back()->with('error', 'Pengajuan ini sudah selesai direview.');
        }

        $step = $pengajuanIzin->currentReviewStep($user);

        if ($step === 'koordinator') {
            $pengajuanIzin->update([
                'status_koordinator' => 'approved',
                'reviewed_by_koordinator' => $user->id,
                'reviewed_at_koordinator' => now(),
                'catatan_koordinator' => $catatan,
                'status' => 'diproses',
            ]);

            $this->notifications->izinReviewed($pengajuanIzin->fresh(['user', 'kegiatan']), 'koordinator', 'approved');

            return back()->with('success', 'Izin disetujui Koordinator dan diteruskan ke Divisi Ranger.');
        }

        if ($step === 'ranger') {
            $pengajuanIzin->update([
                'status_ranger' => 'approved',
                'reviewed_by_ranger' => $user->id,
                'reviewed_at_ranger' => now(),
                'catatan_ranger' => $catatan,
                'status' => 'approved',
            ]);

            $this->syncToPresensi($pengajuanIzin);
            $this->notifications->izinReviewed($pengajuanIzin->fresh(['user', 'kegiatan']), 'ranger', 'approved');

            return back()->with('success', 'Pengajuan izin telah disetujui sepenuhnya.');
        }

        return back()->with('error', 'Anda tidak memiliki otoritas untuk menyetujui izin ini.');
    }

    /**
     * Menolak pengajuan izin.
     */
    public function reject(Request $request, PengajuanIzin $pengajuanIzin): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isPanitia()) {
            abort(404, 'Halaman tidak ditemukan.');
        }
        $request->validate(
            ['catatan' => ['nullable', 'string', 'max:500']],
            ['catatan.max' => 'Catatan terlalu panjang. Maksimal 500 karakter.']
        );
        $catatan = $request->input('catatan');

        if (in_array($pengajuanIzin->status, ['approved', 'rejected'], true)) {
            return back()->with('error', 'Pengajuan ini sudah selesai direview.');
        }

        $step = $pengajuanIzin->currentReviewStep($user);

        if ($step === 'koordinator') {
            $pengajuanIzin->update([
                'status_koordinator' => 'rejected',
                'reviewed_by_koordinator' => $user->id,
                'reviewed_at_koordinator' => now(),
                'catatan_koordinator' => $catatan,
                'status' => 'rejected',
            ]);

            $this->notifications->izinReviewed($pengajuanIzin->fresh(['user', 'kegiatan']), 'koordinator', 'rejected');

            return back()->with('success', 'Pengajuan izin telah ditolak.');
        }

        if ($step === 'ranger') {
            $pengajuanIzin->update([
                'status_ranger' => 'rejected',
                'reviewed_by_ranger' => $user->id,
                'reviewed_at_ranger' => now(),
                'catatan_ranger' => $catatan,
                'status' => 'rejected',
            ]);

            $this->notifications->izinReviewed($pengajuanIzin->fresh(['user', 'kegiatan']), 'ranger', 'rejected');

            return back()->with('success', 'Pengajuan izin telah ditolak.');
        }

        return back()->with('error', 'Anda tidak memiliki otoritas untuk menolak izin ini.');
    }

    /**
     * Hapus pengajuan izin: admin semua, panitia hanya miliknya sendiri.
     */
    public function destroy(PengajuanIzin $pengajuanIzin): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->isPanitia()) {
            abort(404, 'Halaman tidak ditemukan.');
        }

        if (! $pengajuanIzin->canBeDeletedBy($user)) {
            abort(403, 'Anda tidak dapat menghapus pengajuan izin ini.');
        }

        if ($pengajuanIzin->surat_izin) {
            Storage::disk('public')->delete($pengajuanIzin->surat_izin);
        }

        if ($pengajuanIzin->bukti) {
            Storage::disk('public')->delete($pengajuanIzin->bukti);
        }

        Presensi::where('pengajuan_izin_id', $pengajuanIzin->id)->delete();
        $pengajuanIzin->delete();

        return back()->with('success', 'Pengajuan izin berhasil dihapus.');
    }

    /**
     * Ketua divisi dan seluruh Stakeholder melewati tahap koordinator.
     */
    private function autoApproveKoordinatorPayload(User $user): array
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isPanitia()) {
            abort(404, 'Halaman tidak ditemukan.');
        }

        $alasan = $user->isStakeholder()
            ? 'Otomatis disetujui karena pemohon dari Divisi Stakeholder.'
            : 'Otomatis disetujui karena pemohon adalah Ketua Divisi.';

        return [
            'status_koordinator' => 'approved',
            'reviewed_by_koordinator' => $user->id,
            'reviewed_at_koordinator' => now(),
            'catatan_koordinator' => $alasan,
            'status' => 'diproses',
        ];
    }

    /**
     * Helper privat untuk mencatat kehadiran sebagai 'izin' di tabel presensis.
     */
    private function syncToPresensi(PengajuanIzin $pengajuan): void
    {
        /** @var User $user */
        $user = Auth::user();
        // if (!$user->isPanitia()) {
        //     abort(404, 'Halaman tidak ditemukan.');
        // }

        $statusPresensi = strtolower((string) $pengajuan->jenis_izin) === 'sakit' ? 'sakit' : 'izin';

        $pengajuan->loadMissing('reviewerRanger');

        Presensi::updateOrCreate(
            [
                'user_id' => $pengajuan->user_id,
                'kegiatan_id' => $pengajuan->kegiatan_id,
            ],
            [
                'pengajuan_izin_id' => $pengajuan->id,
                'status' => $statusPresensi,
                'keterangan' => $pengajuan->alasan,
                'jam_tap' => $pengajuan->created_at,
                'scanned_by' => $pengajuan->reviewed_by_ranger,
            ]
        );
    }
}
