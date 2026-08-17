<?php

namespace App\Http\Controllers;

use App\Http\Requests\KegiatanRequest;
use App\Models\Kegiatan;
use App\Models\User;
use App\Services\NotificationDispatcher;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KegiatanController extends Controller
{
    public function __construct(private NotificationDispatcher $notifications) {}

    /**
     * Menampilkan daftar timeline/jadwal kegiatan.
     */
    public function index(Request $request): View
    {
        $kegiatans = Kegiatan::withCount('presensis')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search')->toString();
                $q->where(function ($inner) use ($search) {
                    $inner->where('nama', 'like', "%{$search}%")
                        ->orWhere('tempat', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            })
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        return view('kegiatan.index', [
            'title' => 'Timeline Kegiatan',
            'kegiatans' => $kegiatans,
        ]);
    }

    /**
     * Menyimpan data kegiatan baru (Hanya Admin & Archivist/Sekretaris).
     */
    public function store(KegiatanRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Format ulang datetime-local agar sesuai dengan format DATETIME MySQL
        if (! empty($validated['presensi_mulai'])) {
            $validated['presensi_mulai'] = Carbon::parse($validated['presensi_mulai'])->format('Y-m-d H:i:s');
        }
        if (! empty($validated['presensi_selesai'])) {
            $validated['presensi_selesai'] = Carbon::parse($validated['presensi_selesai'])->format('Y-m-d H:i:s');
        }

        $kegiatan = Kegiatan::create($validated);

        $this->notifications->kegiatanCreated($kegiatan, Auth::id());

        return back()->with('success', 'Kegiatan baru berhasil ditambahkan ke timeline.');
    }

    public function update(KegiatanRequest $request, Kegiatan $kegiatan): RedirectResponse
    {
        $validated = $request->validated();

        // Format ulang datetime-local agar sesuai dengan format DATETIME MySQL
        if (! empty($validated['presensi_mulai'])) {
            $validated['presensi_mulai'] = Carbon::parse($validated['presensi_mulai'])->format('Y-m-d H:i:s');
        }
        if (! empty($validated['presensi_selesai'])) {
            $validated['presensi_selesai'] = Carbon::parse($validated['presensi_selesai'])->format('Y-m-d H:i:s');
        }

        $kegiatan->update($validated);

        return back()->with('success', "Kegiatan '{$kegiatan->nama}' berhasil diperbarui.");
    }

    /**
     * Menghapus kegiatan.
     */
    public function destroy(Kegiatan $kegiatan): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->canManageKegiatan()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus kegiatan.');
        }

        $nama = $kegiatan->nama;
        $kegiatan->delete();

        return back()->with('success', "Kegiatan '{$nama}' berhasil dihapus dari timeline.");
    }
}
