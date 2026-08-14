<?php

namespace App\Http\Controllers;

use App\Models\Rapat;
use Illuminate\Http\Request;

class TimelineController extends Controller
{
    /**
     * Menampilkan halaman daftar timeline rapat.
     */
    public function index()
    {
        $timelines = Rapat::orderBy('tanggal', 'asc')
            ->orderBy('jam', 'asc')
            ->get();

        return view('kegiatan.timeline', compact('timelines'));
    }

    /**
     * Menyimpan timeline baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jam' => 'required',
            'tempat' => 'required|string|max:255',
        ]);

        Rapat::create($validated);

        return redirect()->back()->with('success', 'Timeline berhasil ditambahkan.');
    }

    /**
     * Memperbarui timeline.
     */
    public function update(Request $request, Rapat $timeline)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jam' => 'required',
            'tempat' => 'required|string|max:255',
        ]);

        $timeline->update($validated);

        return redirect()->back()->with('success', 'Timeline berhasil diperbarui.');
    }

    /**
     * Menghapus timeline.
     */
    public function destroy(Rapat $timeline)
    {
        $timeline->delete();

        return redirect()->back()->with('success', 'Timeline berhasil dihapus.');
    }
}
