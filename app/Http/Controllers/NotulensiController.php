<?php

namespace App\Http\Controllers;

use App\Models\Notulensi;
use Illuminate\Http\Request;

class NotulensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'lampiran' => 'required|mimes:pdf|max:5120', // 5MB max
        ]);

        $filePath = null;
        if ($request->hasFile('lampiran')) {
            $filePath = $request->file('lampiran')->store('notulensi_pdfs', 'public');
        }

        notulensi::create([
            'judul' => $request->judul,
            'lampiran' => $filePath,
            'pembuat_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Notulensi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Notulensi $notulensi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notulensi $notulensi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notulensi $notulensi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notulensi $notulensi)
    {
        if ($notulensi->lampiran && \Storage::disk('public')->exists($notulensi->lampiran)) {
            \Storage::disk('public')->delete($notulensi->lampiran);
        }

        $notulensi->delete();

        return redirect()->back()->with('success', 'Notulensi berhasil dihapus.');
    }

    /**
     * Serve PDF file inline (for preview).
     */
    public function viewPdf(Notulensi $notulensi)
    {
        if (! $notulensi->lampiran) {
            abort(404, 'Lampiran tidak ditemukan.');
        }

        $path = storage_path('app/public/'.$notulensi->lampiran);

        if (! file_exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
        ]);
    }
}
