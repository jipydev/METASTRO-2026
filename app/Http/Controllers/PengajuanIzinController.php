<?php

namespace App\Http\Controllers;

use App\Models\PengajuanIzin;
use App\Models\Rapat;
use App\Models\User;
use Illuminate\Http\Request;

class PengajuanIzinController extends Controller
{
    /**
     * Menampilkan form pengajuan izin.
     */
    public function create()
    {
        $rapats = Rapat::orderBy('tanggal', 'desc')->get();

        return view('izin.create', compact('rapats'));
    }

    /**
     * Menyimpan pengajuan izin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rapat_id' => 'required|exists:rapats,id',
            'jenis_izin' => 'required|in:Sakit,Izin',
            'alasan' => 'required|string|max:1000',
            'surat_izin' => 'nullable|file|mimes:pdf|max:5120',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        $user = auth()->user();
        $rapat = Rapat::findOrFail($validated['rapat_id']);

        // Handle upload file
        $suratPath = null;
        if ($request->hasFile('surat_izin')) {
            $suratPath = $request->file('surat_izin')->store('surat_izin', 'public');
        }

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('bukti_izin', 'public');
        }

        // Cek jika pengaju adalah Role Ranger
        if ($user->hasRole('Ranger')) {
            $pengajuan = PengajuanIzin::create([
                'user_id' => $user->id,
                'rapat_id' => $rapat->id,
                'tanggal_izin' => $rapat->tanggal,
                'jenis_izin' => $validated['jenis_izin'],
                'alasan' => $validated['alasan'],
                'surat_izin' => $suratPath,
                'bukti' => $buktiPath,
                'status_koordinator' => 'Approved',
                'catatan_koordinator' => 'Bypass Koordinator (Role Ranger)',
                'status_ranger' => 'Pending',
                'status' => 'Pending',
            ]);

            return redirect()->route('izin.history')
                ->with('success', 'Pengajuan izin Anda berhasil dikirim! Izin diteruskan ke sesama Ranger untuk ditinjau.');
        }

        // Cek jika pengaju adalah Role Stakeholder
        if ($user->hasRole('Stakeholder')) {
            $pengajuan = PengajuanIzin::create([
                'user_id' => $user->id,
                'rapat_id' => $rapat->id,
                'tanggal_izin' => $rapat->tanggal,
                'jenis_izin' => $validated['jenis_izin'],
                'alasan' => $validated['alasan'],
                'surat_izin' => $suratPath,
                'bukti' => $buktiPath,
                'status_koordinator' => 'Approved',
                'catatan_koordinator' => 'Bypass Koordinator (Role Stakeholder)',
                'status_ranger' => 'Pending',
                'status' => 'Pending',
            ]);

            return redirect()->route('izin.history')
                ->with('success', 'Pengajuan izin Anda berhasil dikirim! Izin dapat ditinjau oleh role Stakeholder lainnya dan Ranger.');
        }

        // Cek apakah pengaju adalah Koordinator
        if ($user->isKoordinator()) {
            $pengajuan = PengajuanIzin::create([
                'user_id' => $user->id,
                'rapat_id' => $rapat->id,
                'tanggal_izin' => $rapat->tanggal,
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

        // Pengajuan Staff umum: memerlukan approval Koordinator lalu Ranger
        $pengajuan = PengajuanIzin::create([
            'user_id' => $user->id,
            'rapat_id' => $rapat->id,
            'tanggal_izin' => $rapat->tanggal,
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
            ->with(['rapat', 'reviewerKoordinator', 'reviewerRanger'])
            ->latest()
            ->paginate(10);

        return view('izin.history', compact('pengajuanList'));
    }

    /**
     * Menampilkan daftar pengajuan izin yang perlu di-review.
     */
    public function reviewIndex()
    {
        $user = auth()->user();

        $query = PengajuanIzin::whereHas('user', function ($q) {
            $q->where('status_aktif', true);
        })->with(['rapat', 'user.divisi', 'user.roles', 'user.jabatan', 'reviewerKoordinator', 'reviewerRanger']);

        if ($user->hasRole('Admin')) {
            // Admin melihat semua pengajuan izin
        } elseif ($user->hasRole('Stakeholder')) {
            // Stakeholder dapat mereview izin dari pemohon berkategori Stakeholder
            $query->whereHas('user', function ($q) {
                $q->role('Stakeholder');
            });
        } elseif ($user->hasRole('Ranger')) {
            // Ranger melihat izin staff (yg disetujui koordinator), izin Ranger, dan izin Stakeholder
            $query->where(function ($q) {
                $q->where('status_koordinator', 'Approved')
                  ->orWhereHas('user', function ($u) {
                      $u->role(['Ranger', 'Stakeholder']);
                  });
            });
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
        $applicant = $pengajuanIzin->user;

        // Approval untuk Stakeholder (mereview izin Stakeholder)
        if ($user->hasRole('Stakeholder') && $applicant && $applicant->hasRole('Stakeholder')) {
            $pengajuanIzin->update([
                'status_ranger' => 'Approved',
                'reviewed_by_ranger' => $user->id,
                'reviewed_at_ranger' => now(),
                'catatan_ranger' => $request->input('catatan', 'Disetujui oleh Stakeholder'),
                'status' => 'Approved',
            ]);

            return redirect()->back()->with('success', 'Pengajuan izin Stakeholder berhasil disetujui.');
        }

        // Approval Koordinator
        if ($user->isKoordinator() && $applicant && $applicant->divisi_id == $user->divisi_id && !$applicant->hasRole('Ranger') && !$applicant->hasRole('Stakeholder')) {
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

        // Approval Ranger atau Admin
        if ($user->hasRole('Ranger') || $user->hasRole('Admin')) {
            if ($pengajuanIzin->status_ranger !== 'Pending') {
                return redirect()->back()->with('error', 'Pengajuan ini sudah ditinjau.');
            }

            $pengajuanIzin->update([
                'status_ranger' => 'Approved',
                'reviewed_by_ranger' => $user->id,
                'reviewed_at_ranger' => now(),
                'catatan_ranger' => $request->input('catatan'),
                'status' => 'Approved',
            ]);

            return redirect()->back()->with('success', 'Pengajuan izin berhasil disetujui.');
        }

        return redirect()->back()->with('error', 'Anda tidak memiliki otoritas untuk menyetujui pengajuan ini.');
    }

    /**
     * Menolak pengajuan izin.
     */
    public function reject(Request $request, PengajuanIzin $pengajuanIzin)
    {
        $user = auth()->user();
        $applicant = $pengajuanIzin->user;

        $request->validate([
            'catatan' => 'nullable|string|max:500',
        ]);

        // Rejection untuk Stakeholder
        if ($user->hasRole('Stakeholder') && $applicant && $applicant->hasRole('Stakeholder')) {
            $pengajuanIzin->update([
                'status_ranger' => 'Rejected',
                'reviewed_by_ranger' => $user->id,
                'reviewed_at_ranger' => now(),
                'catatan_ranger' => $request->input('catatan', 'Ditolak oleh Stakeholder'),
                'status' => 'Rejected',
            ]);

            return redirect()->back()->with('success', 'Pengajuan izin Stakeholder telah ditolak.');
        }

        // Rejection Koordinator
        if ($user->isKoordinator() && $applicant && $applicant->divisi_id == $user->divisi_id && !$applicant->hasRole('Ranger') && !$applicant->hasRole('Stakeholder')) {
            $pengajuanIzin->update([
                'status_koordinator' => 'Rejected',
                'reviewed_by_koordinator' => $user->id,
                'reviewed_at_koordinator' => now(),
                'catatan_koordinator' => $request->input('catatan'),
                'status' => 'Rejected',
            ]);

            return redirect()->back()->with('success', 'Pengajuan izin telah ditolak oleh Koordinator.');
        }

        // Rejection Ranger atau Admin
        if ($user->hasRole('Ranger') || $user->hasRole('Admin')) {
            $pengajuanIzin->update([
                'status_ranger' => 'Rejected',
                'reviewed_by_ranger' => $user->id,
                'reviewed_at_ranger' => now(),
                'catatan_ranger' => $request->input('catatan'),
                'status' => 'Rejected',
            ]);

            return redirect()->back()->with('success', 'Pengajuan izin telah ditolak.');
        }

        return redirect()->back()->with('error', 'Anda tidak memiliki otoritas untuk menolak pengajuan ini.');
    }
}
