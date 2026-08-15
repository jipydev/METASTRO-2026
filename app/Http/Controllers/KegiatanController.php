<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KegiatanController extends Controller
{
    /**
     * Menampilkan daftar timeline/jadwal kegiatan.
     */
    public function index(): View
    {
        $kegiatans = Kegiatan::withCount('presensis')
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        return view('kegiatan.index', [
            'title'     => 'Timeline Kegiatan',
            'kegiatans' => $kegiatans,
        ]);
    }

    /**
     * Menyimpan data kegiatan baru (Hanya Admin & Archivist/Sekretaris).
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->canManageSekretariat()) {
            abort(403, 'Hanya Sekretaris/Archivist dan Admin yang dapat menambahkan kegiatan.');
        }

        $validated = $request->validate([
            'judul'           => 'required|string|max:255',
            'deskripsi'       => 'nullable|string',
            'tanggal'         => 'required|date',
            'waktu_mulai'     => 'required',
            'waktu_selesai'   => 'nullable|after_or_equal:waktu_mulai',
            'tempat'          => 'required|string|max:255',
            'status_presensi' => 'nullable|in:dijadwalkan,buka,tutup',
        ]);

        $validated['status_presensi'] = $validated['status_presensi'] ?? 'dijadwalkan';

        Kegiatan::create($validated);

        return back()->with('success', 'Kegiatan baru berhasil ditambahkan ke timeline.');
    }

    /**
     * Memperbarui data kegiatan.
     */
    public function update(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->canManageSekretariat()) {
            abort(403, 'Hanya Sekretaris/Archivist dan Admin yang dapat mengedit kegiatan.');
        }

        $validated = $request->validate([
            'judul'           => 'required|string|max:255',
            'deskripsi'       => 'nullable|string',
            'tanggal'         => 'required|date',
            'waktu_mulai'     => 'required',
            'waktu_selesai'   => 'nullable|after_or_equal:waktu_mulai',
            'tempat'          => 'required|string|max:255',
            'status_presensi' => 'nullable|in:dijadwalkan,buka,tutup',
        ]);

        $kegiatan->update($validated);

        return back()->with('success', "Kegiatan '{$kegiatan->judul}' berhasil diperbarui.");
    }

    /**
     * Menghapus kegiatan.
     */
    public function destroy(Kegiatan $kegiatan): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->canManageSekretariat()) {
            abort(403, 'Hanya Sekretaris/Archivist dan Admin yang dapat menghapus kegiatan.');
        }

        $judul = $kegiatan->judul;
        $kegiatan->delete();

        return back()->with('success', "Kegiatan '{$judul}' berhasil dihapus dari timeline.");
    }
}
