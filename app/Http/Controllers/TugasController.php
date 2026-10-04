<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use Illuminate\Http\Request;

use Illuminate\Routing\Controller;

class TugasController extends Controller
{
    private function sanitizeDescription(?string $description): ?string
    {
        if ($description === null) {
            return null;
        }

        $allowedTags = '<strong><b><em><i><u><s><ul><ol><li><br><p><div>';
        $description = strip_tags($description, $allowedTags);

        return preg_replace(
            '/<(strong|b|em|i|u|s|ul|ol|li|br|p|div)\b[^>]*>/i',
            '<$1>',
            $description
        ) ?? $description;
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $jenis = $request->query('jenis');

        $data = [
            'title' => 'Tugas',
            'tugases' => Tugas::query()->orderBy('jenis')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('judul', 'like', "%{$search}%")
                            ->orWhere('deskripsi', 'like', "%{$search}%");
                    });
                })
                ->when(in_array($jenis, ['individu', 'tim', 'angkatan'], true), fn ($query) => $query->where('jenis', $jenis))
                ->paginate(10)
                ->withQueryString(),
            'search' => $search,
            'jenis' => $jenis,
        ];
        return view('tugas.index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Buat Tugas Baru',
        ];

        return view('tugas.create', $data);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'judul' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'jenis' => 'required|in:individu,tim,angkatan',
            'tenggat_waktu' => 'nullable|date',
        ], [
            'judul.required' => 'Judul tugas harus diisi.',
            'judul.string' => 'Judul tugas harus berupa teks.',
            'judul.max' => 'Judul tugas tidak boleh lebih dari 100 karakter.',
            'deskripsi.string' => 'Deskripsi tugas harus berupa teks.',
            'jenis.required' => 'Jenis tugas harus dipilih.',
            'jenis.in' => 'Jenis tugas tidak valid.',
            'tenggat_waktu.date' => 'Tenggat waktu harus berupa tanggal yang valid.',
        ]);

        $validatedData['deskripsi'] = $this->sanitizeDescription($validatedData['deskripsi'] ?? null);
        $validatedData['pembuat_id'] = auth()->id();
        Tugas::create($validatedData);

        return redirect()->route('dashboard.tugas.index')->with('success', 'Tugas berhasil dibuat.');
    }

    public function edit(int $id)
    {
        $data = [
            'title' => 'Edit Tugas',
            'tugas' => Tugas::findOrFail($id),
        ];
        
        return view('tugas.edit', $data);
    }

    public function update(Request $request, int $id)
    {
        $validatedData = $request->validate([
            'judul' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'jenis' => 'required|in:individu,tim,angkatan',
            'tenggat_waktu' => 'nullable|date',
        ], [
            'judul.required' => 'Judul tugas harus diisi.',
            'judul.string' => 'Judul tugas harus berupa teks.',
            'judul.max' => 'Judul tugas tidak boleh lebih dari 100 karakter.',
            'deskripsi.string' => 'Deskripsi tugas harus berupa teks.',
            'jenis.required' => 'Jenis tugas harus dipilih.',
            'jenis.in' => 'Jenis tugas tidak valid.',
            'tenggat_waktu.date' => 'Tenggat waktu harus berupa tanggal yang valid.',
        ]);

        $validatedData['deskripsi'] = $this->sanitizeDescription($validatedData['deskripsi'] ?? null);
        Tugas::where('id', $id)->update($validatedData);


        return redirect()->route('dashboard.tugas.index')->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        Tugas::where('id', $id)->delete();

        return redirect()->route('dashboard.tugas.index')->with('success', 'Tugas berhasil dihapus.');
    }
}
