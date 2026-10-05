<?php

namespace App\Http\Controllers;

use App\Models\PengumpulanTugas;
use Illuminate\Http\Request;

class PengumpulanTugasController extends Controller
{
    public function reviewIndex(Request $request)
    {
        $tugasId = $request->integer('tugas_id');
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');
        $jenis = $request->query('jenis');

        $submisiList = PengumpulanTugas::query()
            ->with(['tugas', 'peserta', 'tim'])
            ->when($tugasId > 0, fn ($query) => $query->where('tugas_id', $tugasId))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->whereHas('tugas', fn ($task) => $task->where('judul', 'like', "%{$search}%"))
                        ->orWhereHas('peserta', function ($participant) use ($search) {
                            $participant->where('nama', 'like', "%{$search}%")
                                ->orWhere('nim', 'like', "%{$search}%");
                        })
                        ->orWhereHas('tim', fn ($team) => $team->where('nama', 'like', "%{$search}%"));
                });
            })
            ->when(auth()->user()->isGuider() && !auth()->user()->isAdmin(), function ($query) {
                $timIds = \App\Models\Guider::where('pembimbing_id', auth()->id())->pluck('tim_id');
                $query->whereIn('tim_id', $timIds);
            })
            ->when(in_array($status, ['pending', 'reviewed', 'rejected'], true), fn ($query) => $query->where('status', $status))
            ->when(in_array($jenis, ['individu', 'tim', 'angkatan'], true), fn ($query) => $query->whereHas('tugas', fn ($task) => $task->where('jenis', $jenis)))
            ->latest('tanggal_pengumpulan')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (PengumpulanTugas $submisi): array => [
                'id' => $submisi->id,
                'judul_tugas' => $submisi->tugas?->judul ?? 'Tugas tidak ditemukan',
                'jenis_tugas' => $submisi->tugas?->jenis ?? 'individu',
                'nama_pengumpul' => $submisi->peserta?->nama ?? 'Peserta tidak ditemukan',
                'perwakilan_label' => $submisi->tim ? 'Perwakilan Tim' : 'Peserta',
                'nim_pengumpul' => $submisi->peserta?->nim ?? '-',
                'asal_tim' => $submisi->tim?->nama ?? '-',
                'tautan_berkas' => asset('storage/' . ltrim($submisi->file_path, '/')),
                'catatan_peserta' => $submisi->catatan_peserta,
                'catatan_pemeriksa' => $submisi->catatan_pemeriksa ?? '',
                'status' => $submisi->status,
                'dikumpulkan_at' => $submisi->tanggal_pengumpulan,
            ]);

        return view('review-tugas.index', [
            'title' => 'Review Pengumpulan Tugas',
            'submisiList' => $submisiList,
            'tugasId' => $tugasId,
            'search' => $search,
            'status' => $status,
            'jenis' => $jenis,
        ]);
    }

    public function reviewUpdate(Request $request, PengumpulanTugas $pengumpulan): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:reviewed,rejected'],
            'catatan_pemeriksa' => ['required', 'string', 'max:2000'],
        ]);

        $pengumpulan->update([
            ...$validated,
            'pemeriksa_id' => $request->user()->id,
        ]);

        return back()->with('success', 'Status dan komentar pengumpulan berhasil diperbarui.');
    }
}
