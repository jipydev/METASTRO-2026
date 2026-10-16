<?php

namespace App\Http\Controllers;

use App\Models\AnggotaTim;
use App\Models\Kegiatan;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PesertaTugasController extends Controller
{
    /**
     * Helper to resolve team id for a given participant
     */
    private function getUserTimId(User $user): ?int
    {
        return AnggotaTim::where('anggota_id', $user->id)->value('tim_id');
    }

    /**
     * Dashboard Peserta
     */
    public function dashboard(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $timId = $this->getUserTimId($user);

        // Cari tugas terdekat yang belum dikumpulkan
        $allTugas = Tugas::orderBy('tenggat_waktu', 'asc')->get();
        $submittedTugasIds = PengumpulanTugas::query()
            ->where(function ($q) use ($user, $timId) {
                $q->where('user_id', $user->id)
                    ->orWhere(function ($teamQuery) use ($timId) {
                        if ($timId) {
                            $teamQuery->where('tim_id', $timId);
                        }
                    });
            })
            ->pluck('tugas_id')
            ->toArray();

        $deadlineTerdekat = $allTugas->first(function (Tugas $t) use ($submittedTugasIds) {
            $isSubmitted = in_array($t->id, $submittedTugasIds, true);
            $isUpcoming = $t->tenggat_waktu ? Carbon::parse($t->tenggat_waktu)->isFuture() : true;
            return ! $isSubmitted && $isUpcoming;
        }) ?? $allTugas->first(function (Tugas $t) use ($submittedTugasIds) {
            return ! in_array($t->id, $submittedTugasIds, true);
        }) ?? $allTugas->first();

        // Hitung progres Day 1 dan Day 2
        $day1Total = Tugas::where('hari', 1)->count();
        $day1Done = Tugas::where('hari', 1)->whereIn('id', $submittedTugasIds)->count();
        $day1Percent = $day1Total > 0 ? (int) round(($day1Done / $day1Total) * 100) : 0;

        $day2Total = Tugas::where('hari', 2)->count();
        $day2Done = Tugas::where('hari', 2)->whereIn('id', $submittedTugasIds)->count();
        $day2Percent = $day2Total > 0 ? (int) round(($day2Done / $day2Total) * 100) : 0;

        $kegiatans = collect();
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('kegiatans')) {
                $kegiatans = Kegiatan::orderBy('tanggal', 'asc')->get();
            }
        } catch (\Throwable $e) {
            $kegiatans = collect();
        }

        return view('dashboard.peserta', [
            'title' => 'Dashboard Peserta',
            'user' => $user,
            'kegiatans' => $kegiatans,
            'tugases' => $allTugas,
            'deadlineTerdekat' => $deadlineTerdekat,
            'day1Done' => $day1Done,
            'day1Total' => $day1Total,
            'day1Percent' => $day1Percent,
            'day2Done' => $day2Done,
            'day2Total' => $day2Total,
            'day2Percent' => $day2Percent,
        ]);
    }

    /**
     * Pilih Hari Penugasan (Day 1 - 4)
     */
    public function hariIndex(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $timId = $this->getUserTimId($user);

        $submittedTugasIds = PengumpulanTugas::query()
            ->where(function ($q) use ($user, $timId) {
                $q->where('user_id', $user->id)
                    ->orWhere(function ($teamQuery) use ($timId) {
                        if ($timId) {
                            $teamQuery->where('tim_id', $timId);
                        }
                    });
            })
            ->pluck('tugas_id')
            ->toArray();

        $daysData = [];
        for ($d = 1; $d <= 4; $d++) {
            $total = Tugas::where('hari', $d)->count();
            $done = Tugas::where('hari', $d)->whereIn('id', $submittedTugasIds)->count();
            $daysData[] = [
                'day' => $d,
                'progress' => $done,
                'max' => max($total, 1),
                'total' => $total,
                'percent' => $total > 0 ? (int) round(($done / $total) * 100) : 0,
            ];
        }

        return view('peserta.tugas-hari', [
            'days' => $daysData,
        ]);
    }

    /**
     * List Penugasan Detail
     */
    public function listIndex(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $timId = $this->getUserTimId($user);

        $selectedHari = $request->query('hari');
        $tab = $request->query('tab', 'semua');
        $sort = $request->query('sort', 'deadline');

        $query = Tugas::query();

        if ($selectedHari && in_array((int) $selectedHari, [1, 2, 3, 4], true)) {
            $query->where('hari', (int) $selectedHari);
        }

        if ($sort === 'deadline') {
            $query->orderByRaw('CASE WHEN tenggat_waktu IS NULL THEN 1 ELSE 0 END, tenggat_waktu ASC');
        } else {
            $query->latest();
        }

        $allTugas = $query->get();

        // Ambil submisi terkini untuk peserta ini
        $allTugasWithSubmisi = $allTugas->map(function (Tugas $tugas) use ($user) {
            $submisi = $tugas->submissionFor($user);
            $tugas->user_submission = $submisi;
            $tugas->is_submitted = $submisi !== null;
            return $tugas;
        });

        return view('peserta.tugas-list', [
            'tugases' => $allTugasWithSubmisi,
            'selectedHari' => $selectedHari,
            'activeTab' => $tab,
            'currentSort' => $sort,
        ]);
    }

    /**
     * Form Kumpulkan Tugas
     */
    public function showKumpulkan(Request $request, ?Tugas $tugas = null): View|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! $tugas || ! $tugas->exists) {
            $tugasId = $request->query('id') ?? $request->query('tugas_id');
            if ($tugasId) {
                $tugas = Tugas::find($tugasId);
            }
        }

        if (! $tugas) {
            // Ambil tugas pertama yang belum dikumpulkan
            $timId = $this->getUserTimId($user);
            $submittedIds = PengumpulanTugas::where('user_id', $user->id)
                ->when($timId, fn ($q) => $q->orWhere('tim_id', $timId))
                ->pluck('tugas_id')
                ->toArray();

            $tugas = Tugas::whereNotIn('id', $submittedIds)->orderBy('tenggat_waktu', 'asc')->first()
                ?? Tugas::first();
        }

        if (! $tugas) {
            return redirect()->route('peserta.tugas-list')->with('error', 'Belum ada tugas yang tersedia.');
        }

        $submission = $tugas->submissionFor($user);

        return view('peserta.tugas-kumpulkan', [
            'tugas' => $tugas,
            'submission' => $submission,
        ]);
    }

    /**
     * Simpan Pengumpulan Tugas
     */
    public function storeKumpulkan(Request $request, Tugas $tugas): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,zip,jpg,jpeg,png', 'max:10240'],
            'catatan_peserta' => ['nullable', 'string', 'max:1000'],
            'agreed' => ['accepted'],
        ], [
            'file.required' => 'Pilih berkas tugas yang ingin diunggah.',
            'file.mimes' => 'Format berkas harus berupa PDF, DOC, DOCX, ZIP, JPG, atau PNG.',
            'file.max' => 'Ukuran berkas maksimal 10MB.',
            'agreed.accepted' => 'Anda harus mencentang pernyataan bahwa telah membaca dan mengerjakan sesuai ketentuan.',
        ]);

        $submission = $tugas->submissionFor($user);

        // Cek apakah deadline sudah lewat
        $isPastDeadline = $tugas->tenggat_waktu && Carbon::parse($tugas->tenggat_waktu)->isPast();
        if ($isPastDeadline && (! $submission || $submission->status !== 'rejected')) {
            return back()->with('error', 'Tenggat waktu pengumpulan tugas ini telah berakhir.');
        }

        // Upload berkas ke storage/app/public/pengumpulan-tugas
        $path = $request->file('file')->store('pengumpulan-tugas', 'public');

        $timId = null;
        if ($tugas->jenis === 'tim') {
            $timId = $this->getUserTimId($user);
        }

        $catatan = trim((string) $request->input('catatan_peserta', ''));
        if ($catatan === '') {
            $catatan = '-';
        }

        if ($submission) {
            // Hapus file lama jika ada
            if ($submission->file_path && Storage::disk('public')->exists($submission->file_path)) {
                Storage::disk('public')->delete($submission->file_path);
            }

            $submission->update([
                'user_id' => $user->id,
                'original_participant_name' => $user->nama ?? $user->name,
                'tim_id' => $timId ?? $submission->tim_id,
                'file_path' => $path,
                'catatan_peserta' => $catatan,
                'status' => 'pending',
                'tanggal_pengumpulan' => Carbon::now(),
            ]);
        } else {
            PengumpulanTugas::create([
                'tugas_id' => $tugas->id,
                'user_id' => $user->id,
                'original_participant_name' => $user->nama ?? $user->name,
                'tim_id' => $timId,
                'file_path' => $path,
                'catatan_peserta' => $catatan,
                'status' => 'pending',
                'tanggal_pengumpulan' => Carbon::now(),
            ]);
        }

        return redirect()->route('peserta.arsip')->with('success', "Tugas '{$tugas->judul}' berhasil dikumpulkan!");
    }

    /**
     * Arsip Tugas Terkumpul
     */
    public function arsipIndex(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $timId = $this->getUserTimId($user);

        $submissions = PengumpulanTugas::with(['tugas', 'tim'])
            ->where(function ($q) use ($user, $timId) {
                $q->where('user_id', $user->id)
                    ->orWhere(function ($teamQuery) use ($timId) {
                        if ($timId) {
                            $teamQuery->where('tim_id', $timId);
                        }
                    });
            })
            ->latest('tanggal_pengumpulan')
            ->get();

        $totalTugas = Tugas::count();
        $totalSubmitted = $submissions->pluck('tugas_id')->unique()->count();
        $progressPercent = $totalTugas > 0 ? (int) round(($totalSubmitted / $totalTugas) * 100) : 0;

        return view('peserta.arsip', [
            'submissions' => $submissions,
            'totalTugas' => $totalTugas,
            'totalSubmitted' => $totalSubmitted,
            'progressPercent' => $progressPercent,
        ]);
    }
}
