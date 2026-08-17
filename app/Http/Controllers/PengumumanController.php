<?php

namespace App\Http\Controllers;

use App\Http\Requests\PengumumanRequest;
use App\Models\Pengumuman;
use App\Models\User;
use App\Services\FileCompressionService;
use App\Services\NotificationDispatcher;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PengumumanController extends Controller
{
    public function __construct(private NotificationDispatcher $notifications) {}

    /**
     * Daftar pengumuman lengkap.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $pengumumans = Pengumuman::with('pembuat.divisi')
            ->visibleTo($user)
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search')->toString();
                $q->where(function ($inner) use ($search) {
                    $inner->where('judul', 'like', "%{$search}%")
                        ->orWhere('isi', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $status = strtolower($request->string('status')->toString());
                if ($status === 'published') {
                    $q->whereIn('status', ['published', 'Publish']);
                } elseif ($status === 'draft') {
                    $q->whereIn('status', ['draft', 'Draft']);
                }
            })
            ->latest('tanggal_publish')
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('pengumuman.index', [
            'title' => 'Pengumuman',
            'pengumumans' => $pengumumans,
        ]);
    }

    /**
     * Simpan Pengumuman Baru
     */
    public function store(PengumumanRequest $request, FileCompressionService $files): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->canCreatePengumuman()) {
            abort(403, 'Anda tidak memiliki hak akses untuk membuat pengumuman.');
        }

        $validated = $request->validated();

        $validated['tanggal_publish'] = ! empty($validated['tanggal_publish'])
            ? Carbon::parse($validated['tanggal_publish'])->format('Y-m-d H:i:s')
            : null;

        if ($request->hasFile('lampiran')) {
            $validated['lampiran'] = $files->store($request->file('lampiran'), 'pengumuman');
        }

        $validated['pembuat_id'] = $user->id;

        $pengumuman = Pengumuman::create($validated);

        $this->notifications->pengumumanPublished($pengumuman, $user->id);

        return redirect()
            ->back()
            ->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    /**
     * Update Pengumuman
     */
    public function update(PengumumanRequest $request, Pengumuman $pengumuman, FileCompressionService $files)
    {
        if (! $pengumuman->canBeManagedBy($request->user())) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah pengumuman ini.');
        }

        $validated = $request->validated();

        $validated['tanggal_publish'] = ! empty($validated['tanggal_publish'])
            ? Carbon::parse($validated['tanggal_publish'])->format('Y-m-d H:i:s')
            : null;

        if ($request->hasFile('lampiran')) {
            if ($pengumuman->lampiran && Storage::disk('public')->exists($pengumuman->lampiran)) {
                Storage::disk('public')->delete($pengumuman->lampiran);
            }
            $validated['lampiran'] = $files->store($request->file('lampiran'), 'pengumuman');
        }

        $wasPublished = $pengumuman->isPublished();
        $pengumuman->update($validated);

        if (! $wasPublished && $pengumuman->isPublished()) {
            $this->notifications->pengumumanPublished($pengumuman, $request->user()?->id);
        }

        return redirect()->back()->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Request $request, Pengumuman $pengumuman)
    {
        // Admin otomatis lolos di sini
        if (! $pengumuman->canBeManagedBy($request->user())) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus pengumuman ini.');
        }

        if ($pengumuman->lampiran && Storage::disk('public')->exists($pengumuman->lampiran)) {
            Storage::disk('public')->delete($pengumuman->lampiran);
        }

        $pengumuman->delete();

        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus.');
    }

    /**
     * Detail Pengumuman JSON (untuk modal/preview API)
     */
    public function show(Pengumuman $pengumuman): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $pengumuman->load('pembuat:id,nama'),
        ]);
    }
}
