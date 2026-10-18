<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use App\Models\User;
use App\Models\Guider;
use App\Models\AnggotaTim;
use App\Services\NotificationDispatcher;
use Illuminate\Http\Request;

class TugasController extends Controller
{
    public function __construct(private NotificationDispatcher $notifications) {}

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

        $tugases = Tugas::with('pembuat')
            ->orderBy('created_at', 'desc')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('judul', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            })
            ->when(in_array($jenis, ['individu', 'tim', 'angkatan'], true), fn($query) => $query->where('jenis', $jenis))
            ->when($day !== null, fn($query) => $query->where('day', $day))
            ->paginate(10)
            ->withQueryString();

        return view('tugas.index', [
            'title' => 'Tugas',
            'tugases' => $tugases,
            'days' => $this->dayOptions(),
            'search' => $search,
            'jenis' => $jenis,
            'day' => $day,
        ]);
    }

    public function create()
    {
        return view('tugas.create', [
            'title' => 'Buat Tugas Baru',
            'days' => $this->dayOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'judul' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'jenis' => 'required|in:individu,tim,angkatan',
            'day' => 'nullable|string|max:50',
            'tenggat_waktu' => 'nullable|date',
        ]);

        $validatedData['deskripsi'] = $this->sanitizeDescription($validatedData['deskripsi'] ?? null);
        $validatedData['pembuat_id'] = auth()->id();

        $tugas = Tugas::create($validatedData);

        // =========================================================================
        // LOGIKA DISTRIBUSI NOTIFIKASI PEMBUATAN TUGAS BARU
        // =========================================================================

        // 1. Semua tugas baru mengirim notifikasi universal ke Divisi Pathfinder
        $pathfinders = User::whereHas('divisi', fn($q) => $q->whereRaw('LOWER(nama) = ?', ['pathfinder']))
            ->where('status', true)
            ->get();

        foreach ($pathfinders as $pathfinder) {
            // $this->notifications->tugasBaruPathfinder($tugas, $pathfinder);
        }

        $jenisTugas = strtolower($tugas->jenis); // 'individu', 'tim', atau 'angkatan'

        if ($jenisTugas === 'individu') {
            // Tugas Individu: Dikirim ke semua Guider pembimbing atau peserta terkait
            $allGuiders = User::whereHas('divisi', fn($q) => $q->whereRaw('LOWER(nama) = ?', ['guider']))
                ->where('status', true)
                ->get();

            foreach ($allGuiders as $guider) {
                // $this->notifications->tugasBaruGuider($tugas, $guider);
            }
        } elseif ($jenisTugas === 'tim') {
            // Tugas Tim: Dikirim ke seluruh Guider dan semua Anggota Tim aktif
            $allGuiders = User::whereHas('divisi', fn($q) => $q->whereRaw('LOWER(nama) = ?', ['guider']))
                ->where('status', true)
                ->get();
            $allAnggotaTim = User::whereHas('tims')->where('status', true)->get();

            $penerimaTim = $allGuiders->concat($allAnggotaTim);
            foreach ($penerimaTim as $penerima) {
                // $this->notifications->tugasBaruTim($tugas, $penerima);
            }
        } elseif ($jenisTugas === 'angkatan') {
            // Tugas Angkatan: Dikirim ke semua Guider dan seluruh Peserta aktif
            $allGuiders = User::whereHas('divisi', fn($q) => $q->whereRaw('LOWER(nama) = ?', ['guider']))
                ->where('status', true)
                ->get();
            $allPeserta = User::where('status', true)->get()->filter(fn($u) => $u->isPeserta());

            $penerimaAngkatan = $allGuiders->concat($allPeserta);
            foreach ($penerimaAngkatan as $penerima) {
                // $this->notifications->tugasBaruAngkatan($tugas, $penerima);
            }
        }

        return redirect()->route('dashboard.tugas.index')->with('success', 'Tugas berhasil dibuat.');
    }

    public function edit(int $id)
    {
        return view('tugas.edit', [
            'title' => 'Edit Tugas',
            'tugas' => Tugas::findOrFail($id),
            'days' => $this->dayOptions(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $validatedData = $request->validate([
            'judul' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'jenis' => 'required|in:individu,tim,angkatan',
            'day' => 'nullable|string|max:50',
            'tenggat_waktu' => 'nullable|date',
        ]);

        $validatedData['deskripsi'] = $this->sanitizeDescription($validatedData['deskripsi'] ?? null);

        $tugas = Tugas::findOrFail($id);
        $tugas->update($validatedData);

        // Opsional: Jika ingin mengirim notifikasi pembaruan tugas, Anda bisa menambahkan dispatcher di sini.

        return redirect()->route('dashboard.tugas.index')->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        Tugas::where('id', $id)->delete();

        return redirect()->route('dashboard.tugas.index')->with('success', 'Tugas berhasil dihapus.');
    }

    public function pesertaDashboard()
    {
        $days = $this->dayOptions();

        return view('peserta.dashboard', compact('days'));
    }

    public function pesertaTugasList(Request $request)
    {
        $selectedDay = $request->query('day');

        if (is_string($selectedDay) && trim($selectedDay) !== '') {
            $selectedDay = trim(urldecode($selectedDay));
        } else {
            $selectedDay = null;
        }

        $tugases = Tugas::query()
            ->when($selectedDay !== null, function ($query) use ($selectedDay) {
                $query->whereRaw('LOWER(TRIM(day)) = ?', [mb_strtolower($selectedDay)]);
            })
            ->orderBy('tenggat_waktu', 'asc')
            ->get();

        return view('peserta.tugas-list', [
            'tugases' => $tugases,
            'selectedDay' => $selectedDay,
        ]);
    }
}
