<?php

namespace App\Http\Controllers;

use App\Models\Notulensi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class NotulensiController extends Controller
{
    /**
     * Menyimpan dokumen notulensi baru.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->canManageSekretariat()) {
            abort(403, 'Hanya Sekretaris/Archivist dan Admin yang dapat mengunggah notulensi.');
        }

        $validated = $request->validate([
            'judul'         => 'required|string|max:255',
            'kegiatan_id'   => 'nullable|exists:kegiatans,id',
            'file_notulensi' => 'required|mimes:pdf|max:5120', // Maksimal 5MB
        ]);

        $filePath = null;
        if ($request->hasFile('file_notulensi')) {
            $filePath = $request->file('file_notulensi')->store('notulensi_pdfs', 'public');
        }

        Notulensi::create([
            'judul'          => $validated['judul'],
            'kegiatan_id'    => $validated['kegiatan_id'] ?? null,
            'file_notulensi' => $filePath,
            'pembuat_id'     => $user->id,
        ]);

        return redirect()->back()->with('success', 'Dokumen notulensi berhasil diunggah.');
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

        if ($notulensi->file_notulensi && Storage::disk('public')->exists($notulensi->file_notulensi)) {
            Storage::disk('public')->delete($notulensi->file_notulensi);
        }

        $notulensi->delete();

        return redirect()->back()->with('success', 'Arsip notulensi berhasil dihapus.');
    }

    /**
     * Pratinjau file PDF secara inline di browser.
     */
    public function viewPdf(Notulensi $notulensi): BinaryFileResponse
    {
        if (! $notulensi->file_notulensi) {
            abort(404, 'File lampiran tidak ditemukan.');
        }

        $path = storage_path('app/public/' . $notulensi->file_notulensi);

        if (! file_exists($path)) {
            abort(404, 'File fisik tidak ditemukan di server.');
        }

        return response()->file($path, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }
}
