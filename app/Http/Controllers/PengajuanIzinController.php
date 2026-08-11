<?php

namespace App\Http\Controllers;

use App\Models\PengajuanIzin;
use App\Models\User;
use Illuminate\Http\Request;

class PengajuanIzinController extends Controller
{
    /**
     * Menampilkan form pengajuan izin.
     */
    public function create()
    {
        return view('izin.create');
    }

    /**
     * Menyimpan pengajuan izin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_izin' => 'required|date',
            'jenis_izin' => 'required|in:Sakit,Izin',
            'alasan' => 'required|string|max:1000',
            'surat_izin' => 'nullable|file|mimes:pdf|max:5120',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        $user = auth()->user();

        // Handle upload file
        $suratPath = null;
        if ($request->hasFile('surat_izin')) {
            $suratPath = $request->file('surat_izin')->store('surat_izin', 'public');
        }

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('bukti_izin', 'public');
        }

        // Cek apakah pengaju adalah Koordinator
        if ($user->isKoordinator()) {
            // Pengajuan Koordinator: otomatis disetujui, diteruskan ke Stakeholder & Ranger sebagai notifikasi / listpanitia
            $pengajuan = PengajuanIzin::create([
                'user_id' => $user->id,
                'tanggal_izin' => $validated['tanggal_izin'],
                'jenis_izin' => $validated['jenis_izin'],
                'alasan' => $validated['alasan'],
                'surat_izin' => $suratPath,
                'bukti' => $buktiPath,
                'status_koordinator' => 'Approved',
                'reviewed_by_koordinator' => $user->id,
                'reviewed_at_koordinator' => now(),
                'catatan_koordinator' => 'Pengajuan dari Koordinator (Otomatis disetujui)',
                'status_ranger' => 'Approved',
                'reviewed_by_ranger' => null,
                'reviewed_at_ranger' => now(),
                'catatan_ranger' => 'Informasi pengajuan Koordinator diteruskan ke Stakeholder & Ranger',
                'status' => 'Approved',
            ]);

            return redirect()->route('izin.history')
                ->with('success', 'Pengajuan izin berhasil dibuat! Status langsung disetujui dan diteruskan ke Stakeholder & Ranger.');
        }

        // Pengajuan Staff: memerlukan approval Koordinator lalu Ranger
        $pengajuan = PengajuanIzin::create([
            'user_id' => $user->id,
            'tanggal_izin' => $validated['tanggal_izin'],
            'jenis_izin' => $validated['jenis_izin'],
            'alasan' => $validated['alasan'],
            'surat_izin' => $suratPath,
            'bukti' => $buktiPath,
            'status_koordinator' => 'Pending',
            'status_ranger' => 'Pending',
            'status' => 'Pending',
        ]);

        return redirect()->route('izin.history')
            ->with('success', 'Pengajuan izin Anda berhasil dikirim! Menunggu peninjauan dari Koordinator divisi.');
    }

    /**
     * Menampilkan riwayat pengajuan izin (terutama untuk Staff).
     */
    public function history()
    {
        $user = auth()->user();

        $pengajuanList = PengajuanIzin::where('user_id', $user->id)
            ->with(['reviewerKoordinator', 'reviewerRanger'])
            ->latest()
            ->paginate(10);

        return view('izin.history', compact('pengajuanList'));
    }

    /**
     * Menampilkan daftar pengajuan izin yang perlu di-review (Koordinator & Ranger).
     */
    public function reviewIndex()
    {
        $user = auth()->user();

        $query = PengajuanIzin::whereHas('user', function ($q) {
            $q->where('status_aktif', true);
        })->with(['user.divisi', 'user.jabatan', 'reviewerKoordinator', 'reviewerRanger']);

        if ($user->hasRole('Admin')) {
            // Admin melihat semua pengajuan izin
        } elseif ($user->hasRole('Ranger')) {
            // Ranger melihat pengajuan yang sudah disetujui Koordinator
            $query->where('status_koordinator', 'Approved');
        } elseif ($user->isKoordinator()) {
            // Koordinator melihat pengajuan staff di divisinya
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('divisi_id', $user->divisi_id)
                  ->where('id', '!=', $user->id);
            });
        } else {
            abort(403, 'Anda tidak memiliki akses untuk mereview izin.');
        }

        $pengajuanList = $query->latest()->paginate(15);

        return view('izin.review', compact('pengajuanList'));
    }

    /**
     * Menyetujui pengajuan izin.
     */
    public function approve(Request $request, PengajuanIzin $pengajuanIzin)
    {
        $user = auth()->user();

        if ($user->isKoordinator() && $pengajuanIzin->user->divisi_id == $user->divisi_id) {
            if ($pengajuanIzin->status_koordinator !== 'Pending') {
                return redirect()->back()->with('error', 'Pengajuan ini sudah ditinjau oleh Koordinator.');
            }

            $pengajuanIzin->update([
                'status_koordinator' => 'Approved',
                'reviewed_by_koordinator' => $user->id,
                'reviewed_at_koordinator' => now(),
                'catatan_koordinator' => $request->input('catatan'),
                'status' => 'Diproses', // Menunggu persetujuan Ranger
            ]);

            return redirect()->back()->with('success', 'Pengajuan izin disetujui oleh Koordinator dan diteruskan ke Ranger.');
        }

        if ($user->hasRole('Ranger') || $user->hasRole('Admin')) {
            if ($pengajuanIzin->status_koordinator !== 'Approved') {
                return redirect()->back()->with('error', 'Pengajuan izin harus disetujui Koordinator terlebih dahulu.');
            }

            if ($pengajuanIzin->status_ranger !== 'Pending') {
                return redirect()->back()->with('error', 'Pengajuan ini sudah ditinjau oleh Ranger.');
            }

            $pengajuanIzin->update([
                'status_ranger' => 'Approved',
                'reviewed_by_ranger' => $user->id,
                'reviewed_at_ranger' => now(),
                'catatan_ranger' => $request->input('catatan'),
                'status' => 'Approved', // Final Approved
            ]);

            return redirect()->back()->with('success', 'Pengajuan izin berhasil disetujui sepenuhnya oleh Ranger!');
        }

        return redirect()->back()->with('error', 'Anda tidak memiliki otoritas untuk menyetujui pengajuan ini.');
    }

    /**
     * Menolak pengajuan izin.
     */
    public function reject(Request $request, PengajuanIzin $pengajuanIzin)
    {
        $user = auth()->user();

        $request->validate([
            'catatan' => 'nullable|string|max:500',
        ]);

        if ($user->isKoordinator() && $pengajuanIzin->user->divisi_id == $user->divisi_id) {
            $pengajuanIzin->update([
                'status_koordinator' => 'Rejected',
                'reviewed_by_koordinator' => $user->id,
                'reviewed_at_koordinator' => now(),
                'catatan_koordinator' => $request->input('catatan'),
                'status' => 'Rejected',
            ]);

            return redirect()->back()->with('success', 'Pengajuan izin telah ditolak oleh Koordinator.');
        }

        if ($user->hasRole('Ranger') || $user->hasRole('Admin')) {
            $pengajuanIzin->update([
                'status_ranger' => 'Rejected',
                'reviewed_by_ranger' => $user->id,
                'reviewed_at_ranger' => now(),
                'catatan_ranger' => $request->input('catatan'),
                'status' => 'Rejected',
            ]);

            return redirect()->back()->with('success', 'Pengajuan izin telah ditolak oleh Ranger.');
        }

        return redirect()->back()->with('error', 'Anda tidak memiliki otoritas untuk menolak pengajuan ini.');
    }
}
