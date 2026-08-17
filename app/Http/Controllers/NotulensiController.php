<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotulensiRequest;
use App\Models\Kegiatan;
use App\Models\Notulensi;
use App\Models\User;
use App\Services\FileCompressionService;
use App\Services\NotificationDispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class NotulensiController extends Controller
{
    public function __construct(private NotificationDispatcher $notifications) {}

    /**
     * Daftar arsip notulensi lengkap.
     */
    public function index(Request $request): View
    {
        $notulensis = Notulensi::with(['kegiatan', 'pembuat.divisi'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search')->toString();
                $q->where(function ($inner) use ($search) {
                    $inner->where('judul', 'like', "%{$search}%")
                        ->orWhere('isi', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('kegiatan_id'), function ($q) use ($request) {
                $q->where('kegiatan_id', $request->integer('kegiatan_id'));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('notulensi.index', [
            'title' => 'Notulensi Rapat',
            'notulensis' => $notulensis,
            'kegiatanOptions' => Kegiatan::orderBy('tanggal', 'desc')->limit(80)->get(['id', 'nama', 'tanggal']),
        ]);
    }

    /**
     * Menyimpan dokumen notulensi baru.
     */
    public function store(NotulensiRequest $request, FileCompressionService $files): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validated();

        $filePath = null;
        if ($request->hasFile('lampiran')) {
            $filePath = $files->store($request->file('lampiran'), 'notulensi_pdfs');
        }

        $notulensi = Notulensi::create([
            'judul' => $validated['judul'],
            'isi' => $validated['isi'] ?? null,
            'kegiatan_id' => $validated['kegiatan_id'] ?? null,
            'lampiran' => $filePath,
            'pembuat_id' => $user->id,
        ]);

        $this->notifications->notulensiCreated($notulensi, $user->id);

        return redirect()->back()->with('success', 'Dokumen notulensi berhasil diunggah.');
    }

    /**
     * Memperbarui dokumen notulensi.
     */
    public function update(NotulensiRequest $request, Notulensi $notulensi, FileCompressionService $files): RedirectResponse
    {
        $validated = $request->validated();

        $data = [
            'judul' => $validated['judul'],
            'isi' => $validated['isi'] ?? null,
            'kegiatan_id' => $validated['kegiatan_id'] ?: null,
        ];

        if ($request->hasFile('lampiran')) {
            if ($notulensi->lampiran && Storage::disk('public')->exists($notulensi->lampiran)) {
                Storage::disk('public')->delete($notulensi->lampiran);
            }

            $data['lampiran'] = $files->store($request->file('lampiran'), 'notulensi_pdfs');
        }

        $notulensi->update($data);

        return redirect()->back()->with('success', 'Notulensi berhasil diperbarui.');
    }

    /**
     * Menghapus dokumen notulensi.
     */
    public function destroy(Notulensi $notulensi): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->canManageSekretariat()) {
            abort(403, 'Hanya Sekretaris/Archivist dan Admin yang dapat menghapus notulensi.');
        }

        if ($notulensi->lampiran && Storage::disk('public')->exists($notulensi->lampiran)) {
            Storage::disk('public')->delete($notulensi->lampiran);
        }

        $notulensi->delete();

        return redirect()->back()->with('success', 'Arsip notulensi berhasil dihapus.');
    }

    /**
     * Pratinjau file PDF secara inline di browser.
     */
    public function show(Notulensi $notulensi): BinaryFileResponse
    {
        if (! $notulensi->lampiran) {
            abort(404, 'File lampiran tidak ditemukan.');
        }

        $path = storage_path('app/public/'.$notulensi->lampiran);

        if (! file_exists($path)) {
            abort(404, 'File fisik tidak ditemukan di server.');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
        ]);
    }
}
