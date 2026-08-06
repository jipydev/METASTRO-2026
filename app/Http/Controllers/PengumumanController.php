<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PengumumanController extends Controller
{
    /**
     * Simpan Pengumuman
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'tanggal_publish' => 'required|date',
            'status' => 'required|in:Draft,Publish',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        // UBAH FORMAT TANGGAL SEBELUM MASUK DATABASE
        $validated['tanggal_publish'] = Carbon::parse($request->tanggal_publish)->format('Y-m-d H:i:s');

        if ($request->hasFile('lampiran')) {
            $validated['lampiran'] = $request
                ->file('lampiran')
                ->store('pengumuman', 'public');
        }

        $validated['pembuat_id'] = Auth::id();

        Pengumuman::create($validated);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    /**
     * Update Pengumuman
     */
    public function update(Request $request, Pengumuman $pengumuman)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'tanggal_publish' => 'required|date',
            'status' => 'required|in:Draft,Publish',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        // UBAH FORMAT TANGGAL SEBELUM MASUK DATABASE
        $validated['tanggal_publish'] = Carbon::parse($request->tanggal_publish)->format('Y-m-d H:i:s');

        if ($request->hasFile('lampiran')) {

            if (
                $pengumuman->lampiran &&
                Storage::disk('public')->exists($pengumuman->lampiran)
            ) {
                Storage::disk('public')->delete($pengumuman->lampiran);
            }

            $validated['lampiran'] = $request
                ->file('lampiran')
                ->store('pengumuman', 'public');
        }

        $pengumuman->update($validated);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }
    /**
     * Hapus Pengumuman
     */
    public function destroy(Pengumuman $pengumuman)
    {
        if (
            $pengumuman->lampiran &&
            Storage::disk('public')->exists($pengumuman->lampiran)
        ) {
            Storage::disk('public')->delete($pengumuman->lampiran);
        }

        $pengumuman->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    /**
     * Detail Pengumuman (opsional)
     */
    public function show(Pengumuman $pengumuman)
    {
        return response()->json($pengumuman);
    }

    /**
     * Lihat lampiran pengumuman inline
     */
    public function viewLampiran(Pengumuman $pengumuman)
    {
        if (!$pengumuman->lampiran) {
            abort(404, 'Lampiran tidak ditemukan.');
        }

        $path = storage_path('app/public/' . $pengumuman->lampiran);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $mimeType = mime_content_type($path);

        return response()->file($path, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }
}
