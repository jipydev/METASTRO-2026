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

    /**
     * Daftar Day unik yang sudah pernah dipakai di tabel tugases.
     * Diurutkan natural supaya "Day 2" tampil sebelum "Day 10".
     */
    private function dayOptions()
    {
        return Tugas::query()
            ->whereNotNull('day')
            ->where('day', '!=', '')
            ->distinct()
            ->pluck('day')
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $jenis = $request->query('jenis');
        $day = $request->query('day');
        $day = is_string($day) && trim($day) !== '' ? trim($day) : null;

        $data = [
            'title' => 'Tugas',
            'tugases' => Tugas::query()->orderBy('jenis')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('judul', 'like', "%{$search}%")
                            ->orWhere('deskripsi', 'like', "%{$search}%");
                    });
                })
                ->when(in_array($jenis, ['individu', 'tim', 'angkatan'], true), fn($query) => $query->where('jenis', $jenis))
                ->when($day !== null, fn($query) => $query->where('day', $day))
                ->paginate(10)
                ->withQueryString(),
            'days' => $this->dayOptions(),
            'search' => $search,
            'jenis' => $jenis,
            'day' => $day,
        ];
        return view('tugas.index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Buat Tugas Baru',
            'days' => $this->dayOptions(),
        ];

        return view('tugas.create', $data);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'judul' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'jenis' => 'required|in:individu,tim,angkatan',
            'day' => 'nullable|string|max:50',
            'tenggat_waktu' => 'nullable|date',
        ], [
            'judul.required' => 'Judul tugas harus diisi.',
            'judul.string' => 'Judul tugas harus berupa teks.',
            'judul.max' => 'Judul tugas tidak boleh lebih dari 100 karakter.',
            'deskripsi.string' => 'Deskripsi tugas harus berupa teks.',
            'jenis.required' => 'Jenis tugas harus dipilih.',
            'jenis.in' => 'Jenis tugas tidak valid.',
            'day.string' => 'Day harus berupa teks.',
            'day.max' => 'Day tidak boleh lebih dari 50 karakter.',
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
            'days' => $this->dayOptions(),
        ];

        return view('tugas.edit', $data);
    }

    public function update(Request $request, int $id)
    {
        $validatedData = $request->validate([
            'judul' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'jenis' => 'required|in:individu,tim,angkatan',
            'day' => 'nullable|string|max:50',
            'tenggat_waktu' => 'nullable|date',
        ], [
            'judul.required' => 'Judul tugas harus diisi.',
            'judul.string' => 'Judul tugas harus berupa teks.',
            'judul.max' => 'Judul tugas tidak boleh lebih dari 100 karakter.',
            'deskripsi.string' => 'Deskripsi tugas harus berupa teks.',
            'jenis.required' => 'Jenis tugas harus dipilih.',
            'jenis.in' => 'Jenis tugas tidak valid.',
            'day.string' => 'Day harus berupa teks.',
            'day.max' => 'Day tidak boleh lebih dari 50 karakter.',
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

    /**
     * Tampilan Dashboard untuk Peserta (Menampilkan daftar Card Day)
     */
    public function pesertaDashboard()
    {
        $days = $this->dayOptions();

        return view('peserta.dashboard', compact('days'));
    }

    /**
     * Tampilan List Tugas untuk Peserta (Berdasarkan Day yang diklik)
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function pesertaTugasList(Request $request)
    {
        // Ambil query parameter 'day' dari URL (?day=DAY%201)
        $selectedDay = $request->query('day');

        // Bersihkan dan decode nilai parameter jika berupa string
        if (is_string($selectedDay) && trim($selectedDay) !== '') {
            $selectedDay = urldecode(trim($selectedDay));
        } else {
            $selectedDay = null;
        }

        // Filter data tugas berdasarkan Day jika ada
        $tugases = Tugas::query()
            ->when($selectedDay !== null, function ($query) use ($selectedDay) {
                $query->where('day', 'LIKE', $selectedDay);
            })
            ->orderBy('tenggat_waktu', 'asc')
            ->get();

        // Pastikan mengembalikan view peserta.tugas-list beserta variabelnya
        return view('peserta.tugas-list', [
            'tugases' => $tugases,
            'selectedDay' => $selectedDay,
        ]);
    }
}
