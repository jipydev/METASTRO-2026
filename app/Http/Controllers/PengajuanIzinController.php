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

        // Pengawas: Izin masuk ke Ketua Pengawas
        if ($user->isPengawas()) {
            PengajuanIzin::create([
                'user_id' => $user->id,
                'rapat_id' => $rapat->id,
                'tanggal_izin' => $rapat->tanggal,
                'jenis_izin' => $validated['jenis_izin'],
                'alasan' => $validated['alasan'],
                'surat_izin' => $suratPath,
                'bukti' => $buktiPath,
                'status_koordinator' => 'Approved',
                'catatan_koordinator' => 'Bypass Koordinator (Pengawas)',
                'status_ranger' => 'Pending', // Akan direview oleh Ketua Pengawas
                'status' => 'Pending',
            ]);
            return redirect()->route('izin.history')->with('success', 'Pengajuan izin Anda berhasil dikirim! Izin akan ditinjau oleh Ketua Pengawas.');
        }

        // Ranger (kecuali Pengawas)
        if ($user->isRanger() && !$user->isPengawas()) {
            PengajuanIzin::create([
                'user_id' => $user->id,
                'rapat_id' => $rapat->id,
                'tanggal_izin' => $rapat->tanggal,
                'jenis_izin' => $validated['jenis_izin'],
                'alasan' => $validated['alasan'],
                'surat_izin' => $suratPath,
                'bukti' => $buktiPath,
                'status_koordinator' => 'Approved',
                'catatan_koordinator' => 'Bypass Koordinator (Ranger)',
                'status_ranger' => 'Pending',
                'status' => 'Pending',
            ]);
            return redirect()->route('izin.history')->with('success', 'Pengajuan izin Anda berhasil dikirim! Izin diteruskan ke sesama Ranger untuk ditinjau.');
        }

        // Stakeholder
        if ($user->isStakeholder()) {
            PengajuanIzin::create([
                'user_id' => $user->id,
                'rapat_id' => $rapat->id,
                'tanggal_izin' => $rapat->tanggal,
                'jenis_izin' => $validated['jenis_izin'],
                'alasan' => $validated['alasan'],
                'surat_izin' => $suratPath,
                'bukti' => $buktiPath,
                'status_koordinator' => 'Approved',
                'catatan_koordinator' => 'Bypass Koordinator (Stakeholder)',
                'status_ranger' => 'Pending', // Direview oleh Ranger
                'status' => 'Pending',
            ]);
            return redirect()->route('izin.history')->with('success', 'Pengajuan izin Anda berhasil dikirim! Izin akan ditinjau oleh Ranger.');
        }

        // Ketua / Wakil
        if ($user->isKetuaOrWakil()) {
            PengajuanIzin::create([
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
                'catatan_koordinator' => 'Pengajuan dari Ketua/Wakil (Otomatis disetujui tingkat divisi)',
                'status_ranger' => 'Pending', // Direview oleh Stakeholder
                'status' => 'Pending',
            ]);
            return redirect()->route('izin.history')->with('success', 'Pengajuan izin berhasil dibuat! Izin diteruskan ke Stakeholder untuk ditinjau.');
        }

        // Anggota umum
        PengajuanIzin::create([
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

        return redirect()->route('izin.history')->with('success', 'Pengajuan izin Anda berhasil dikirim! Menunggu peninjauan dari Ketua/Wakil divisi.');
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
    public function reviewIndex(Request $request)
    {
        $user = auth()->user();
        $filter = $request->query('filter', 'pending');

        $query = PengajuanIzin::with(['rapat', 'user.divisi', 'user.roles', 'user.jabatan', 'reviewerKoordinator', 'reviewerRanger']);

        if (!$user->hasRole('Admin')) {
            $query->whereHas('user', function ($q) {
                $q->where('status_aktif', true);
            });
        }

        if ($user->hasRole('Admin')) {
            if ($filter === 'pending') {
                $query->whereIn('status', ['Pending', 'Diproses']);
            } elseif ($filter === 'limbo') {
                $query->where(function ($q) {
                    $q->whereHas('user', function ($u) {
                        $u->whereNull('divisi_id');
                    })->orWhere(function ($sub) {
                        $sub->where('status_koordinator', 'Pending')->where('status', 'Pending');
                    });
                });
            } elseif (in_array($filter, ['approved', 'rejected'])) {
                $query->where('status', ucfirst($filter));
            }
        } elseif ($user->isKetuaPengawas()) {
            // Ketua Pengawas melihat izin para Pengawas
            $query->whereHas('user', function ($q) {
                $q->whereHas('jabatan', function($j) {
                    $j->where('nama_jabatan', 'Pengawas');
                });
            });
            if ($filter === 'pending') {
                $query->where('status_ranger', 'Pending');
            } elseif (in_array($filter, ['approved', 'rejected'])) {
                $query->where('status_ranger', ucfirst($filter));
            }
        } elseif ($user->isStakeholder() && !$user->isKetuaPengawas()) {
            // Stakeholder melihat izin Ketua/Wakil
            $query->whereHas('user', function ($q) {
                $q->whereHas('jabatan', function($j) {
                    $j->whereIn('nama_jabatan', ['Ketua', 'Wakil']);
                });
            });
            if ($filter === 'pending') {
                $query->where('status_ranger', 'Pending');
            } elseif (in_array($filter, ['approved', 'rejected'])) {
                $query->where('status_ranger', ucfirst($filter));
            }
        } elseif ($user->isRanger() && !$user->isPengawas()) {
            // Ranger melihat izin umum, stakeholder, ranger lain, dsb.
            // Tidak melihat izin Pengawas (masuk ke Ketua Pengawas) dan tidak melihat izin Ketua/Wakil (masuk ke Stakeholder)
            $query->where(function ($q) {
                $q->where('status_koordinator', 'Approved')
                  ->orWhereHas('user', function ($u) {
                      $u->whereHas('divisi', function($d) {
                          $d->whereIn('nama_divisi', ['Ranger', 'Stakeholder']);
                      })->orWhereNull('divisi_id');
                  });
            })->whereHas('user', function ($q) {
                $q->whereDoesntHave('jabatan', function($j) {
                    $j->whereIn('nama_jabatan', ['Pengawas', 'Ketua', 'Wakil']);
                });
            });
            if ($filter === 'pending') {
                $query->where('status_ranger', 'Pending');
            } elseif (in_array($filter, ['approved', 'rejected'])) {
                $query->where('status_ranger', ucfirst($filter));
            }
        } elseif ($user->isKetuaOrWakil()) {
            // Ketua / Wakil melihat pengajuan dari anggota divisinya
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('divisi_id', $user->divisi_id)
                  ->where('id', '!=', $user->id)
                  ->whereHas('jabatan', function($j) {
                      $j->where('nama_jabatan', 'Anggota');
                  });
            });
            if ($filter === 'pending') {
                $query->where('status_koordinator', 'Pending');
            } elseif (in_array($filter, ['approved', 'rejected'])) {
                $query->where('status_koordinator', ucfirst($filter));
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

        // Ketua Pengawas meng-approve Pengawas
        if ($user->isKetuaPengawas() && $applicant && $applicant->isPengawas()) {
            $pengajuanIzin->update([
                'status_ranger' => 'Approved',
                'reviewed_by_ranger' => $user->id,
                'reviewed_at_ranger' => now(),
                'catatan_ranger' => $request->input('catatan', 'Disetujui oleh Ketua Pengawas'),
                'status' => 'Approved',
            ]);
            return redirect()->back()->with('success', 'Pengajuan izin Pengawas berhasil disetujui.');
        }

        // Stakeholder meng-approve Ketua/Wakil
        if ($user->isStakeholder() && !$user->isKetuaPengawas() && $applicant && $applicant->isKetuaOrWakil()) {
            $pengajuanIzin->update([
                'status_ranger' => 'Approved',
                'reviewed_by_ranger' => $user->id,
                'reviewed_at_ranger' => now(),
                'catatan_ranger' => $request->input('catatan', 'Disetujui oleh Stakeholder'),
                'status' => 'Approved',
            ]);
            return redirect()->back()->with('success', 'Pengajuan izin Ketua/Wakil berhasil disetujui.');
        }

        // Ketua / Wakil meng-approve Anggota divisinya
        if ($user->isKetuaOrWakil() && $applicant && $applicant->divisi_id == $user->divisi_id && $applicant->isAnggota()) {
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
            return redirect()->back()->with('success', 'Pengajuan izin disetujui oleh Ketua/Wakil dan diteruskan ke Ranger.');
        }

        // Ranger atau Admin meng-approve
        if (($user->isRanger() && !$user->isPengawas()) || $user->hasRole('Admin')) {
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

        $request->validate(['catatan' => 'nullable|string|max:500']);

        // Ketua Pengawas menolak Pengawas
        if ($user->isKetuaPengawas() && $applicant && $applicant->isPengawas()) {
            $pengajuanIzin->update([
                'status_ranger' => 'Rejected',
                'reviewed_by_ranger' => $user->id,
                'reviewed_at_ranger' => now(),
                'catatan_ranger' => $request->input('catatan', 'Ditolak oleh Ketua Pengawas'),
                'status' => 'Rejected',
            ]);
            return redirect()->back()->with('success', 'Pengajuan izin Pengawas telah ditolak.');
        }

        // Stakeholder menolak Ketua/Wakil
        if ($user->isStakeholder() && !$user->isKetuaPengawas() && $applicant && $applicant->isKetuaOrWakil()) {
            $pengajuanIzin->update([
                'status_ranger' => 'Rejected',
                'reviewed_by_ranger' => $user->id,
                'reviewed_at_ranger' => now(),
                'catatan_ranger' => $request->input('catatan', 'Ditolak oleh Stakeholder'),
                'status' => 'Rejected',
            ]);
            return redirect()->back()->with('success', 'Pengajuan izin Ketua/Wakil telah ditolak.');
        }

        // Ketua / Wakil menolak Anggota divisinya
        if ($user->isKetuaOrWakil() && $applicant && $applicant->divisi_id == $user->divisi_id && $applicant->isAnggota()) {
            $pengajuanIzin->update([
                'status_koordinator' => 'Rejected',
                'reviewed_by_koordinator' => $user->id,
                'reviewed_at_koordinator' => now(),
                'catatan_koordinator' => $request->input('catatan'),
                'status' => 'Rejected',
            ]);
            return redirect()->back()->with('success', 'Pengajuan izin telah ditolak oleh Ketua/Wakil.');
        }

        // Ranger atau Admin menolak
        if (($user->isRanger() && !$user->isPengawas()) || $user->hasRole('Admin')) {
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
