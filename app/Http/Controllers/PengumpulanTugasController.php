<?php

namespace App\Http\Controllers;

use App\Models\PengumpulanTugas;
use App\Models\User;
use App\Models\Guider;
use App\Models\AnggotaTim;
use App\Services\NotificationDispatcher;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class PengumpulanTugasController extends Controller
{
    public function __construct(private NotificationDispatcher $notifications) {}

    public function reviewIndex(Request $request)
    {
        $tugasId = $request->integer('tugas_id');
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');
        $jenis = $request->query('jenis');

        $submisiList = PengumpulanTugas::query()
            ->with(['tugas', 'peserta', 'tim'])
            ->when($tugasId > 0, fn($query) => $query->where('tugas_id', $tugasId))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->whereHas('tugas', fn($task) => $task->where('judul', 'like', "%{$search}%"))
                        ->orWhereHas('peserta', function ($participant) use ($search) {
                            $participant->where('nama', 'like', "%{$search}%")
                                ->orWhere('nim', 'like', "%{$search}%");
                        })
                        ->orWhereHas('tim', fn($team) => $team->where('nama', 'like', "%{$search}%"));
                });
            })
            ->when(auth()->user()->isGuider() && !auth()->user()->isAdmin() && !auth()->user()->isPathfinder(), function ($query) {
                $timIds = Guider::where('pembimbing_id', auth()->id())->pluck('tim_id');
                $anggotaIds = AnggotaTim::whereIn('tim_id', $timIds)->pluck('anggota_id');
                $query->where(function ($sub) use ($timIds, $anggotaIds) {
                    $sub->whereIn('tim_id', $timIds)
                        ->orWhereIn('user_id', $anggotaIds);
                });
            })
            ->when(in_array($status, ['pending', 'reviewed', 'rejected'], true), fn($query) => $query->where('status', $status))
            ->when(in_array($jenis, ['individu', 'tim', 'angkatan'], true), fn($query) => $query->whereHas('tugas', fn($task) => $task->where('jenis', $jenis)))
            ->latest('tanggal_pengumpulan')
            ->paginate(10)
            ->withQueryString()
            ->through(function (PengumpulanTugas $submisi): array {
                $tugas = $submisi->tugas;

                // Cek apakah pengumpulan melewati tenggat waktu
                $isLate = false;
                if ($tugas && $tugas->tenggat_waktu && $submisi->tanggal_pengumpulan) {
                    $isLate = \Carbon\Carbon::parse($submisi->tanggal_pengumpulan)
                        ->greaterThan($tugas->tenggat_waktu);
                }

                return [
                    'id' => $submisi->id,
                    'judul_tugas' => $tugas?->judul ?? 'Tugas tidak ditemukan',
                    'jenis_tugas' => $tugas?->jenis ?? 'individu',
                    'nama_pengumpul' => $submisi->peserta?->nama ?? $submisi->original_participant_name ?? 'Peserta tidak ditemukan',
                    'perwakilan_label' => $submisi->tim ? 'Perwakilan Tim' : 'Peserta',
                    'nim_pengumpul' => $submisi->peserta?->nim ?? '-',
                    'asal_tim' => $submisi->tim?->nama ?? '-',
                    'tautan_berkas' => asset('storage/' . ltrim($submisi->file_path, '/')),
                    'catatan_peserta' => $submisi->catatan_peserta,
                    'catatan_pemeriksa' => $submisi->catatan_pemeriksa ?? '',
                    'status' => $submisi->status,
                    'dikumpulkan_at' => $submisi->tanggal_pengumpulan,
                    'is_late' => $isLate, // Tambahan penanda keterlambatan
                ];
            });

        return view('review-tugas.index', [
            'title' => 'Review Pengumpulan Tugas',
            'submisiList' => $submisiList,
            'tugasId' => $tugasId,
            'search' => $search,
            'status' => $status,
            'jenis' => $jenis,
        ]);
    }

    public function reviewUpdate(Request $request, PengumpulanTugas $pengumpulan): RedirectResponse
    {
        if ($request->user()->isGuider() && !$request->user()->isAdmin() && !$request->user()->isPathfinder()) {
            $timIds = Guider::where('pembimbing_id', $request->user()->id)->pluck('tim_id')->toArray();
            $anggotaIds = AnggotaTim::whereIn('tim_id', $timIds)->pluck('anggota_id')->toArray();

            $isAuthorized = ($pengumpulan->tim_id && in_array($pengumpulan->tim_id, $timIds, true))
                || ($pengumpulan->user_id && in_array($pengumpulan->user_id, $anggotaIds, true));

            if (!$isAuthorized) {
                abort(403, 'Anda tidak memiliki wewenang untuk mereview pengumpulan tugas tim/peserta lain.');
            }
        }

        $validated = $request->validate([
            'status' => ['required', 'in:reviewed,rejected'],
            'catatan_pemeriksa' => ['string', 'max:2000'],
        ]);

        $pengumpulan->update([
            ...$validated,
            'pemeriksa_id' => $request->user()->id,
        ]);

        // Kirim notifikasi ke peserta bahwa tugas telah direview
        $this->notifications->tugasReviewed($pengumpulan->fresh(['tugas', 'peserta']));

        return back()->with('success', 'Status dan komentar pengumpulan berhasil diperbarui.');
    }
}
