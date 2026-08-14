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
        $rapats = Rapat::orderBy('tanggal', 'desc')->limit(20)->get();

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
    /**
     * Menampilkan daftar pengajuan izin yang perlu di-review.
     */
    public function reviewIndex(Request $request)
    {
        $user = auth()->user();
        $filter = $request->query('filter', 'pending');

        $query = PengajuanIzin::with(['rapat', 'user.divisi', 'user.roles', 'user.jabatan', 'reviewerKoordinator', 'reviewerRanger']);

        // Hanya sertakan filter status_aktif jika BUKAN Admin (Admin melihat semua)
        if (!$user->hasRole('Admin')) {
            $query->whereHas('user', function ($q) {
                $q->where('status_aktif', true);
            });
        }

        if ($user->isAdmin()) {
            // Admin melihat semua pengajuan izin
            if ($filter === 'pending') {
                $query->where(function ($q) {
                    $q->where('status', 'Pending')->orWhere('status', 'Diproses');
                });
            } elseif ($filter === 'limbo') {
                $query->where(function ($q) {
                    $q->whereHas('user', function ($u) {
                        $u->whereNull('divisi_id');
                    })->orWhere(function ($sub) {
                        $sub->where('status_koordinator', 'Pending')
                            ->where('status', 'Pending');
                    });
                });
            } elseif ($filter === 'approved') {
                $query->where('status', 'Approved');
            } elseif ($filter === 'rejected') {
                $query->where('status', 'Rejected');
            }
        } elseif ($user->isStakeholder()) {
            // Stakeholder dapat mereview izin dari pemohon berkategori Stakeholder
            $query->whereHas('user', function ($q) {
                $q->whereHas('divisi', function ($d) {
                    $d->where('nama_divisi', 'Stakeholder');
                });
            });

            if ($filter === 'pending') {
                $query->where('status_ranger', 'Pending');
            } elseif ($filter === 'approved') {
                $query->where('status_ranger', 'Approved');
            } elseif ($filter === 'rejected') {
                $query->where('status_ranger', 'Rejected');
            }
        } elseif ($user->isRanger()) {
            // Ranger melihat izin staff (yg disetujui ketua/wakil), izin Ranger, Stakeholder, atau izin tanpa divisi
            $query->where(function ($q) {
                $q->where('status_koordinator', 'Approved')
                  ->orWhereHas('user', function ($u) {
                      $u->whereHas('divisi', function ($d) {
                          $d->whereIn('nama_divisi', ['Ranger', 'Stakeholder']);
                      })->orWhereNull('divisi_id');
                  });
            });

            if ($filter === 'pending') {
                $query->where('status_ranger', 'Pending');
            } elseif ($filter === 'approved') {
                $query->where('status_ranger', 'Approved');
            } elseif ($filter === 'rejected') {
                $query->where('status_ranger', 'Rejected');
            }
        } elseif ($user->isKetuaOrWakil()) {
            // Ketua/Wakil melihat pengajuan anggota di divisinya
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('divisi_id', $user->divisi_id)
                  ->where('id', '!=', $user->id);
            });

            // Jika user adalah Wakil dan Ketua juga mengajukan izin, Wakil yang mereview Ketua
            if ($user->isWakil()) {
                $query->whereHas('user', function ($q) use ($user) {
                    $q->where('divisi_id', $user->divisi_id);
                });
            }

            if ($filter === 'pending') {
                $query->where('status_koordinator', 'Pending');
            } elseif ($filter === 'approved') {
                $query->where('status_koordinator', 'Approved');
            } elseif ($filter === 'rejected') {
                $query->where('status_koordinator', 'Rejected');
            }
        } else {
            abort(403, 'Anda tidak memiliki akses untuk mereview izin.');
        }

        $pengajuanList = $query->latest()->paginate(15)->withQueryString();

        return view('izin.review', compact('pengajuanList', 'filter'));
    }

    /**
     * Menyetujui pengajuan izin.
     */
    public function approve(Request $request, PengajuanIzin $pengajuanIzin)
    {
        $user = auth()->user();
        $applicant = $pengajuanIzin->user;

        // Approval untuk Stakeholder (mereview izin Stakeholder)
        if (($user->isStakeholder() || $user->isKetuaPengawas()) && $applicant && $applicant->isStakeholder()) {
            $pengajuanIzin->update([
                'status_ranger' => 'Approved',
                'reviewed_by_ranger' => $user->id,
                'reviewed_at_ranger' => now(),
                'catatan_ranger' => $request->input('catatan', 'Disetujui oleh Stakeholder / Ketua Pengawas'),
                'status' => 'Approved',
            ]);

            return redirect()->back()->with('success', 'Pengajuan izin Stakeholder berhasil disetujui.');
        }

        // Approval Ketua / Wakil Divisi
        if ($user->isKetuaOrWakil() && $applicant && $applicant->divisi_id == $user->divisi_id && !$applicant->isRanger() && !$applicant->isStakeholder()) {
            // Wakil mereview Ketua, atau Ketua mereview Anggota/Wakil/Pengawas
            if ($applicant->isKetua() && !$user->isWakil() && !$user->isAdmin()) {
                return redirect()->back()->with('error', 'Izin Ketua divisi hanya dapat ditinjau oleh Wakil divisi atau Admin.');
            }

            if ($pengajuanIzin->status_koordinator !== 'Pending') {
                return redirect()->back()->with('error', 'Pengajuan ini sudah ditinjau oleh Ketua/Wakil.');
            }

            $pengajuanIzin->update([
                'status_koordinator' => 'Approved',
                'reviewed_by_koordinator' => $user->id,
                'reviewed_at_koordinator' => now(),
                'catatan_koordinator' => $request->input('catatan'),
                'status' => 'Diproses', // Menunggu persetujuan Ranger
            ]);

            return redirect()->back()->with('success', 'Pengajuan izin disetujui dan diteruskan ke Divisi Ranger.');
        }

        // Approval Ranger atau Admin
        if ($user->isRanger() || $user->isAdmin()) {
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
        if (($user->isStakeholder() || $user->isKetuaPengawas()) && $applicant && $applicant->isStakeholder()) {
            $pengajuanIzin->update([
                'status_ranger' => 'Rejected',
                'reviewed_by_ranger' => $user->id,
                'reviewed_at_ranger' => now(),
                'catatan_ranger' => $request->input('catatan', 'Ditolak oleh Stakeholder / Ketua Pengawas'),
                'status' => 'Rejected',
            ]);

            return redirect()->back()->with('success', 'Pengajuan izin Stakeholder telah ditolak.');
        }

        // Rejection Ketua / Wakil
        if ($user->isKetuaOrWakil() && $applicant && $applicant->divisi_id == $user->divisi_id && !$applicant->isRanger() && !$applicant->isStakeholder()) {
            $pengajuanIzin->update([
                'status_koordinator' => 'Rejected',
                'reviewed_by_koordinator' => $user->id,
                'reviewed_at_koordinator' => now(),
                'catatan_koordinator' => $request->input('catatan'),
                'status' => 'Rejected',
            ]);

            return redirect()->back()->with('success', 'Pengajuan izin telah ditolak oleh Ketua/Wakil Divisi.');
        }

        // Rejection Ranger atau Admin
        if ($user->isRanger() || $user->isAdmin()) {
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
