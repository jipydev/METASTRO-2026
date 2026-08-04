<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengumumanController extends Controller
{
    /**
     * Mengambil satu pengumuman (dipakai Alpine.js)
     */
    public function show(Pengumuman $pengumuman)
    {
        return response()->json($pengumuman->load('pembuat'));
    }

    /**
     * Menyimpan pengumuman baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'             => 'required|string|max:255',
            'isi'               => 'required|string',
            'lampiran'          => 'nullable|file|max:5120',
            'tanggal_publish'   => 'required|date',
            'status'            => 'required|in:Draft,Publish',
        ]);

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
     * Update pengumuman
     */
    public function update(Request $request, Pengumuman $pengumuman)
    {
        $validated = $request->validate([
            'judul'             => 'required|string|max:255',
            'isi'               => 'required|string',
            'lampiran'          => 'nullable|file|max:5120',
            'tanggal_publish'   => 'required|date',
            'status'            => 'required|in:Draft,Publish',
        ]);

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
     * Hapus pengumuman
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
}