<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\PengajuanIzin;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PengajuanIzinController extends Controller
{
    /**
     * Menampilkan riwayat pengajuan izin milik user yang sedang login.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $pengajuanList = PengajuanIzin::where('user_id', $user->id)
            ->with(['kegiatan', 'reviewerKoordinator', 'reviewerRanger'])
            ->latest()
            ->paginate(10);

        return view('pengajuan-izin.history', [
            'title'         => 'Riwayat Izin Saya',
            'pengajuanList' => $pengajuanList,
        ]);
    }

    /**
     * Menampilkan form pengajuan izin baru.
     */
    public function create(): View
    {
        $kegiatans = Kegiatan::orderBy('tanggal', 'desc')->limit(15)->get();

        return view('pengajuan-izin.create', [
            'title'     => 'Ajukan Izin',
            'kegiatans' => $kegiatans,
        ]);
    }

    /**
     * Menyimpan data pengajuan izin.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,id',
            'jenis_izin'  => 'required|in:Sakit,Izin',
            'alasan'      => 'required|string|max:1000',
            'surat_izin'  => 'nullable|file|mimes:pdf|max:5120',
            'bukti'       => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $kegiatan = Kegiatan::findOrFail($validated['kegiatan_id']);

        // Cek duplikasi pengajuan
        $existing = PengajuanIzin::where('user_id', $user->id)
            ->where('kegiatan_id', $kegiatan->id)
            ->first();

        if ($existing) {
            return back()->with('error', "Anda sudah pernah mengajukan izin untuk kegiatan '{$kegiatan->judul}'.");
        }

        // Simpan Berkas
        $suratPath = $request->hasFile('surat_izin')
            ? $request->file('surat_izin')->store('izin/surat', 'public')
            : null;

        $buktiPath = $request->hasFile('bukti')
            ? $request->file('bukti')->store('izin/bukti', 'public')
            : null;

        // 1. Bypass otomatis jika pemohon adalah Struktural / Ranger / Stakeholder / Admin
        if ($user->isAdmin() || $user->isKetuaOrWakil() || $user->isRanger() || $user->isStakeholder()) {
            $pengajuan = PengajuanIzin::create([
                'user_id'                 => $user->id,
                'kegiatan_id'             => $kegiatan->id,
                'tanggal_izin'            => $kegiatan->tanggal,
                'jenis_izin'              => $validated['jenis_izin'],
                'alasan'                  => $validated['alasan'],
                'surat_izin'              => $suratPath,
                'bukti'                   => $buktiPath,
                'status_koordinator'      => 'Approved',
                'reviewed_by_koordinator' => $user->id,
                'reviewed_at_koordinator' => now(),
                'catatan_koordinator'     => 'Auto-approved (Struktural / Koordinator)',
                'status_ranger'           => 'Approved',
                'reviewed_by_ranger'      => $user->id,
                'reviewed_at_ranger'      => now(),
                'status'                  => 'Approved',
            ]);

            // Catat langsung ke presensi
            $this->syncToPresensi($pengajuan);

            return redirect()->route('pengajuan-izin.index')
                ->with('success', 'Pengajuan izin berhasil dibuat dan otomatis disetujui.');
        }

        // 2. Alur normal untuk Anggota / Staff umum
        PengajuanIzin::create([
            'user_id'            => $user->id,
            'kegiatan_id'        => $kegiatan->id,
            'tanggal_izin'       => $kegiatan->tanggal,
            'jenis_izin'         => $validated['jenis_izin'],
            'alasan'             => $validated['alasan'],
            'surat_izin'         => $suratPath,
            'bukti'              => $buktiPath,
            'status_koordinator' => 'Pending',
            'status_ranger'      => 'Pending',
            'status'             => 'Pending',
        ]);

        return redirect()->route('pengajuan-izin.index')
            ->with('success', 'Pengajuan izin berhasil dikirim dan menunggu verifikasi Koordinator.');
    }

    /**
     * Menampilkan daftar review pengajuan izin (untuk Koordinator, Ranger, Admin).
     */
    public function reviewIndex(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        $filter = $request->query('filter', 'pending');

        $query = PengajuanIzin::with(['kegiatan', 'user.divisi', 'user.jabatan']);

        if ($user->isAdmin()) {
            if ($filter === 'pending') {
                $query->whereIn('status', ['Pending', 'Diproses']);
            } elseif ($filter === 'approved') {
                $query->where('status', 'Approved');
            } elseif ($filter === 'rejected') {
                $query->where('status', 'Rejected');
            }
        } elseif ($user->isRanger()) {
            $query->where('status_koordinator', 'Approved');
            if ($filter === 'pending') {
                $query->where('status_ranger', 'Pending');
            } elseif ($filter === 'approved') {
                $query->where('status_ranger', 'Approved');
            } elseif ($filter === 'rejected') {
                $query->where('status_ranger', 'Rejected');
            }
        } elseif ($user->isKetuaOrWakil()) {
            $query->whereHas('user', fn($q) => $q->where('divisi_id', $user->divisi_id)->where('id', '!=', $user->id));
            if ($filter === 'pending') {
                $query->where('status_koordinator', 'Pending');
            } elseif ($filter === 'approved') {
                $query->where('status_koordinator', 'Approved');
            } elseif ($filter === 'rejected') {
                $query->where('status_koordinator', 'Rejected');
            }
        } else {
            abort(403, 'Anda tidak memiliki hak akses untuk mereview izin.');
        }

        return view('pengajuan-izin.review', [
            'title'         => 'Review Pengajuan Izin',
            'pengajuanList' => $query->latest()->paginate(15)->withQueryString(),
            'filter'        => $filter,
        ]);
    }

    /**
     * Menyetujui pengajuan izin.
     */
    public function approve(Request $request, PengajuanIzin $pengajuanIzin): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $applicant = $pengajuanIzin->user;

        // 1. Approval oleh Ketua / Wakil Divisi
        if ($user->isKetuaOrWakil() && $applicant && $applicant->divisi_id === $user->divisi_id && ! $user->isAdmin() && ! $user->isRanger()) {
            $pengajuanIzin->update([
                'status_koordinator'      => 'Approved',
                'reviewed_by_koordinator' => $user->id,
                'reviewed_at_koordinator' => now(),
                'catatan_koordinator'     => $request->input('catatan'),
                'status'                  => 'Diproses', // Diteruskan ke Ranger
            ]);

            return back()->with('success', 'Izin disetujui Koordinator dan diteruskan ke Divisi Ranger.');
        }

        // 2. Approval Final oleh Divisi Ranger atau Admin
        if ($user->isRanger() || $user->isAdmin()) {
            $pengajuanIzin->update([
                'status_ranger'      => 'Approved',
                'reviewed_by_ranger' => $user->id,
                'reviewed_at_ranger' => now(),
                'catatan_ranger'     => $request->input('catatan'),
                'status'             => 'Approved',
            ]);

            // Sinkronkan ke tabel presensi
            $this->syncToPresensi($pengajuanIzin);

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
        $request->validate(['catatan' => 'nullable|string|max:500']);

        if ($user->isKetuaOrWakil() && ! $user->isRanger() && ! $user->isAdmin()) {
            $pengajuanIzin->update([
                'status_koordinator'      => 'Rejected',
                'reviewed_by_koordinator' => $user->id,
                'reviewed_at_koordinator' => now(),
                'catatan_koordinator'     => $request->input('catatan'),
                'status'                  => 'Rejected',
            ]);
        } else {
            $pengajuanIzin->update([
                'status_ranger'      => 'Rejected',
                'reviewed_by_ranger' => $user->id,
                'reviewed_at_ranger' => now(),
                'catatan_ranger'     => $request->input('catatan'),
                'status'             => 'Rejected',
            ]);
        }

        return back()->with('success', 'Pengajuan izin telah ditolak.');
    }

    /**
     * Helper privat untuk mencatat kehadiran sebagai 'izin' di tabel presensis.
     */
    private function syncToPresensi(PengajuanIzin $pengajuan): void
    {
        Presensi::updateOrCreate(
            [
                'user_id'     => $pengajuan->user_id,
                'kegiatan_id' => $pengajuan->kegiatan_id,
            ],
            [
                'status_kehadiran' => 'izin',
                'waktu_presensi'   => now(),
                'metode_presensi'  => 'manual',
                'keterangan'       => "Izin ({$pengajuan->jenis_izin}): {$pengajuan->alasan}",
            ]
        );
    }
}
