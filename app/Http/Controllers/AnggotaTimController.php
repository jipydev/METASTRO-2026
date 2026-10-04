<?php

namespace App\Http\Controllers;

use App\Models\AnggotaTim;
use App\Models\Tim;
use Illuminate\Http\Request;

class AnggotaTimController extends Controller
{
    public function store(Request $request, int $tim)
    {
        $timModel = Tim::findOrFail($tim);

        $validated = $request->validate([
            'anggota_id' => 'required|exists:users,id',
        ]);

        if (AnggotaTim::where('anggota_id', $validated['anggota_id'])->exists()) {
            return back()->withErrors([
                'anggota_id' => 'Mahasiswa tersebut sudah memiliki tim.',
            ]);
        }

        AnggotaTim::create([
            'tim_id' => $timModel->id,
            'anggota_id' => $validated['anggota_id'],
        ]);

        return redirect()->route('dashboard.tim.show', $timModel->slug)
            ->with('success', 'Mahasiswa berhasil ditambahkan ke tim.');
    }

    public function destroy(int $tim, int $anggota)
    {
        $timModel = Tim::findOrFail($tim);

        AnggotaTim::where('tim_id', $timModel->id)
            ->where('anggota_id', $anggota)
            ->delete();

        return redirect()->route('dashboard.tim.show', $timModel->slug)
            ->with('success', 'Mahasiswa berhasil dikeluarkan dari tim.');
    }
}
