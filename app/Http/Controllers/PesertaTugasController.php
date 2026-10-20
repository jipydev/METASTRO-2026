<?php

namespace App\Http\Controllers;

use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use App\Models\User;
use App\Models\Guider;
use App\Models\AnggotaTim;
use App\Services\NotificationDispatcher;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PesertaTugasController extends Controller
{
    /**
     * Inject NotificationDispatcher untuk menangani pengiriman notifikasi.
     */
    public function __construct(private NotificationDispatcher $notifications) {}

    /**
     * Helper untuk mengambil Tim dari peserta yang sedang login.
     */
    private function getUserTim($user)
    {
        return $user ? $user->currentTim() : null;
    }

    /**
     * Helper untuk mencari pengumpulan tugas yang relevan untuk user/tim.
     */
    private function findSubmissionForTask(Tugas $tugas, $user, $tim)
    {
        if ($tugas->jenis === 'tim') {
            return $tim ? PengumpulanTugas::where('tugas_id', $tugas->id)->where('tim_id', $tim->id)->first() : null;
        }

        if ($tugas->jenis === 'angkatan') {
            return PengumpulanTugas::where('tugas_id', $tugas->id)->first();
        }

        return PengumpulanTugas::where('tugas_id', $tugas->id)->where('user_id', $user->id)->first();
    }

    /**
     * Halaman List Penugasan Peserta.
     */
    public function list(Request $request): View
    {
        $user = $request->user();
        $tim = $this->getUserTim($user);

        $search = trim((string) $request->query('search', ''));
        $dayFilter = $request->query('day');

        $tugasList = Tugas::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('judul', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            })
            ->when(!empty($dayFilter), fn($q) => $q->where('day', $dayFilter))
            ->orderBy('tenggat_waktu', 'asc')
            ->get();

        // Ambil semua pengumpulan yang relevan dalam 1 batch
        $userSubmissions = PengumpulanTugas::query()
            ->where(function ($q) use ($user, $tim) {
                $q->where('user_id', $user->id);
                if ($tim) {
                    $q->orWhere('tim_id', $tim->id);
                }
                $q->orWhereHas('tugas', fn($t) => $t->where('jenis', 'angkatan'));
            })
            ->get();

        $tasksWithStatus = $tugasList->map(function (Tugas $tugas) use ($user, $tim, $userSubmissions) {
            $submission = null;

            if ($tugas->jenis === 'tim') {
                $submission = $tim ? $userSubmissions->first(fn($s) => $s->tugas_id === $tugas->id && $s->tim_id === $tim->id) : null;
            } elseif ($tugas->jenis === 'angkatan') {
                $submission = $userSubmissions->first(fn($s) => $s->tugas_id === $tugas->id);
            } else {
                $submission = $userSubmissions->first(fn($s) => $s->tugas_id === $tugas->id && $s->user_id === $user->id);
            }

            $isExpired = $tugas->tenggat_waktu ? Carbon::now()->greaterThan($tugas->tenggat_waktu) : false;

            // Tentukan status pengerjaan
            $statusPengerjaan = 'unsubmitted';

            if ($submission) {
                // Jika sudah submit, cek apakah waktu pengumpulannya melewati deadline
                $isLateSubmission = $tugas->tenggat_waktu && $submission->tanggal_pengumpulan
                    ? Carbon::parse($submission->tanggal_pengumpulan)->greaterThan($tugas->tenggat_waktu)
                    : false;

                if ($isLateSubmission) {
                    $statusPengerjaan = 'expired'; // Tulisan: "Terlambat Mengerjakan"
                } else {
                    $statusPengerjaan = $submission->status; // 'reviewed', 'pending', dll
                }
            } else {
                // Jika BELUM submit
                if ($isExpired) {
                    $statusPengerjaan = 'unsubmitted_expired'; // Status khusus untuk belum submit tapi lewat deadline
                }
            }

            return [
                'id' => $tugas->id,
                'judul' => $tugas->judul,
                'deskripsi' => $tugas->deskripsi,
                'jenis' => $tugas->jenis,
                'day' => $tugas->day,
                'tenggat_waktu' => $tugas->tenggat_waktu,
                'is_expired' => $isExpired,
                'submission' => $submission,
                'status_pengerjaan' => $statusPengerjaan,
            ];
        });

        return view('peserta.tugas-list', [
            'title' => 'Daftar Tugas',
            'tasks' => $tasksWithStatus,
            'search' => $search,
            'dayFilter' => $dayFilter,
            'tim' => $tim,
        ]);
    }

    /**
     * Halaman Detail & Form Pengumpulan Tugas.
     */
    public function kumpulkan(Tugas $tugas, Request $request): View
    {
        $user = $request->user();
        $tim = $this->getUserTim($user);
        $submission = $this->findSubmissionForTask($tugas, $user, $tim);

        $isExpired = $tugas->tenggat_waktu ? Carbon::now()->greaterThan($tugas->tenggat_waktu) : false;

        return view('peserta.tugas-kumpulkan', [
            'title' => 'Kumpulkan Tugas',
            'tugas' => $tugas,
            'tim' => $tim,
            'submission' => $submission,
            'isExpired' => $isExpired,
        ]);
    }

    /**
     * Simpan / Upload Berkas Tugas (PDF) serta Kirim Notifikasi.
     */
    public function store(Tugas $tugas, Request $request): RedirectResponse
    {
        $user = $request->user();
        $tim = $this->getUserTim($user);
        $submission = $this->findSubmissionForTask($tugas, $user, $tim);

        $isExpired = $tugas->tenggat_waktu && Carbon::now()->greaterThan($tugas->tenggat_waktu);

        // ATURAN: Jika sudah lewat deadline DAN sudah pernah submit sebelumnya, tolak
        if ($isExpired && $submission) {
            return back()->withErrors(['file_tugas' => 'Tenggat waktu telah berakhir dan Anda tidak dapat mengubah tugas yang sudah dikumpulkan.']);
        }

        // Cek jika tugas jenis tim dan peserta belum punya tim
        if ($tugas->jenis === 'tim' && !$tim) {
            return back()->withErrors(['file_tugas' => 'Anda belum terdaftar dalam regu/tim untuk mengumpulkan tugas tim.']);
        }

        $request->validate([
            'file_tugas' => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'catatan_peserta' => ['nullable', 'string', 'max:1000'],
        ], [
            'file_tugas.required' => 'Berkas tugas format PDF wajib diunggah.',
            'file_tugas.file' => 'Berkas yang diunggah tidak valid.',
            'file_tugas.mimes' => 'Berkas harus berupa file format PDF.',
            'file_tugas.max' => 'Ukuran berkas PDF maksimal 5 MB.',
        ]);

        $filePath = $request->file('file_tugas')->store('pengumpulan_tugas', 'public');
        $catatan = $request->input('catatan_peserta') ?? '';

        if ($submission) {
            // Hapus file lama jika ada
            if ($submission->file_path && Storage::disk('public')->exists($submission->file_path)) {
                Storage::disk('public')->delete($submission->file_path);
            }

            $submission->update([
                'file_path' => $filePath,
                'catatan_peserta' => $catatan,
                'user_id' => $user->id,
                'original_participant_name' => $user->nama,
                'tim_id' => $tim?->id,
                'status' => 'pending',
                'tanggal_pengumpulan' => Carbon::now(),
            ]);
            $submission = $submission->fresh(['tugas', 'peserta', 'tim']);
        } else {
            // Pembuatan submission baru (meskipun telat, diizinkan jika belum pernah submit)
            $submission = PengumpulanTugas::create([
                'tugas_id' => $tugas->id,
                'user_id' => $user->id,
                'original_participant_name' => $user->nama,
                'tim_id' => $tim?->id,
                'file_path' => $filePath,
                'catatan_peserta' => $catatan,
                'status' => 'pending',
                'tanggal_pengumpulan' => Carbon::now(),
            ])->load(['tugas', 'peserta', 'tim']);
        }

        // =========================================================================
        // LOGIKA DISTRIBUSI NOTIFIKASI
        // =========================================================================

        // 1. SEMUA TUGAS/PENGUMPULAN: Notifikasi dikirimkan ke Divisi Pathfinder, Ranger, dan Admin (Chiper)
        $universalReceivers = User::where('status', true)->get()->filter(function ($u) {
            $isPathfinderOrRanger = $u->divisi && in_array(strtolower($u->divisi->nama), ['pathfinder', 'ranger'], true);
            $isAdmin = method_exists($u, 'isAdmin') ? $u->isAdmin() : false;

            return $isPathfinderOrRanger || $isAdmin;
        });

        foreach ($universalReceivers as $receiver) {
            $this->notifications->tugasUniversalSubmitted($submission, $receiver);
        }

        // 2. BERDASARKAN JENIS TUGAS (Individu, Tim, Angkatan)
        $jenisTugas = strtolower($tugas->jenis);

        $guiderIds = $tim ? Guider::where('tim_id', $tim->id)->pluck('pembimbing_id') : collect();
        $guiders = User::whereIn('id', $guiderIds)->where('status', true)->get();

        if ($jenisTugas === 'individu') {
            // INDIVIDU: Dikirim ke guider di tim peserta tersebut
            foreach ($guiders as $guider) {
                $this->notifications->tugasIndividuSubmitted($submission, $guider);
            }
        } elseif ($jenisTugas === 'tim') {
            // TIM: Dikirim ke sesama anggota tim dan guider tim tersebut
            $anggotaTimIds = $tim ? AnggotaTim::where('tim_id', $tim->id)->pluck('anggota_id') : collect();
            $sesamaTim = User::whereIn('id', $anggotaTimIds)->where('id', '!=', $user->id)->where('status', true)->get();

            $penerimaTim = $sesamaTim->concat($guiders);
            foreach ($penerimaTim as $penerima) {
                $this->notifications->tugasTimSubmitted($submission, $penerima);
            }
        } elseif ($jenisTugas === 'angkatan') {
            // ANGKATAN: Dikirim ke semua guider dan seluruh role peserta
            $allGuiders = User::whereHas('divisi', fn($q) => $q->whereRaw('LOWER(nama) = ?', ['guider']))
                ->where('status', true)
                ->get();

            $allPeserta = User::where('status', true)->get()->filter(fn($u) => $u->isPeserta());

            $penerimaAngkatan = $allGuiders->concat($allPeserta);
            foreach ($penerimaAngkatan as $penerima) {
                $this->notifications->tugasAngkatanSubmitted($submission, $penerima);
            }
        }

        return redirect()->route('peserta.tugas-kumpulkan', $tugas->id)
            ->with('success', 'Tugas berhasil dikumpulkan.');
    }

    /**
     * Hapus / Batalkan Pengumpulan Tugas (hanya jika belum deadline).
     */
    public function destroy(Tugas $tugas, Request $request): RedirectResponse
    {
        $user = $request->user();
        $tim = $this->getUserTim($user);

        if ($tugas->tenggat_waktu && Carbon::now()->greaterThan($tugas->tenggat_waktu)) {
            return back()->withErrors(['msg' => 'Tidak dapat menghapus pengumpulan karena batas waktu telah berakhir.']);
        }

        $submission = $this->findSubmissionForTask($tugas, $user, $tim);

        if ($submission) {
            if ($submission->file_path && Storage::disk('public')->exists($submission->file_path)) {
                Storage::disk('public')->delete($submission->file_path);
            }
            $submission->delete();
        }

        return redirect()->route('peserta.tugas-kumpulkan', $tugas->id)
            ->with('success', 'Pengumpulan tugas berhasil dibatalkan / dihapus.');
    }

    /**
     * Tampilan pilihan Hari Penugasan (Day 1, Day 2, dst) beserta progress dinamis.
     */
    public function tugasHari(Request $request): View
    {
        $user = $request->user();
        $tim = $this->getUserTim($user);

        // Ambil daftar unique days dari Tugas atau default Day 1 - Day 4
        $dayNames = Tugas::query()->whereNotNull('day')->distinct()->pluck('day')->filter()->values()->all();
        if (empty($dayNames)) {
            $dayNames = ['Day 1', 'Day 2', 'Day 3', 'Day 4'];
        }

        // Ambil submission user
        $userSubmissions = PengumpulanTugas::query()
            ->where(function ($q) use ($user, $tim) {
                $q->where('user_id', $user->id);
                if ($tim) {
                    $q->orWhere('tim_id', $tim->id);
                }
                $q->orWhereHas('tugas', fn($t) => $t->where('jenis', 'angkatan'));
            })
            ->pluck('tugas_id')
            ->unique()
            ->toArray();

        $days = collect($dayNames)->map(function ($dayName) use ($userSubmissions) {
            $tugasDay = Tugas::where('day', $dayName)->get();
            $max = $tugasDay->count();
            $completed = $tugasDay->filter(fn($t) => in_array($t->id, $userSubmissions, true))->count();

            // Ekstrak angka hari jika format 'Day X'
            preg_match('/(\d+)/', (string) $dayName, $matches);
            $dayNumber = $matches[1] ?? $dayName;

            return [
                'name' => $dayName,
                'day' => $dayNumber,
                'progress' => $completed,
                'max' => $max > 0 ? $max : 1,
            ];
        });

        return view('peserta.tugas-hari', [
            'days' => $days,
        ]);
    }

    /**
     * Tampilan Riwayat / Arsip Tugas yang telah dikerjakan.
     */
    public function arsip(Request $request): View
    {
        $user = $request->user();
        $tim = $this->getUserTim($user);

        $submissions = PengumpulanTugas::query()
            ->with('tugas')
            ->where(function ($q) use ($user, $tim) {
                $q->where('user_id', $user->id);
                if ($tim) {
                    $q->orWhere('tim_id', $tim->id);
                }
                $q->orWhereHas('tugas', fn($t) => $t->where('jenis', 'angkatan'));
            })
            ->latest('tanggal_pengumpulan')
            ->get();

        $totalTugas = Tugas::count();
        $totalSubmitted = $submissions->count();
        $persentase = $totalTugas > 0 ? round(($totalSubmitted / $totalTugas) * 100) : 0;

        return view('peserta.arsip', [
            'submissions' => $submissions,
            'persentase' => $persentase,
        ]);
    }
}
