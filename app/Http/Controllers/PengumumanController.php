<?php

namespace App\Http\Controllers;

use App\Http\Requests\PengumumanRequest;
use App\Models\Pengumuman;
use App\Models\User;
use App\Services\FileCompressionService;
use App\Services\NotificationDispatcher;
use App\Services\PengumumanPublisher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PengumumanController extends Controller
{
    public function __construct(
        private NotificationDispatcher $notifications,
        private PengumumanPublisher $publisher,
    ) {}

    /**
     * Daftar pengumuman lengkap.
     */
    public function index(Request $request): View
    {
        $this->publisher->publishDue();

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
                    $q->publishedAndLive();
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
            'minPublishAt' => now()->format('Y-m-d\TH:i'),
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

        $validated = $this->publisher->resolveAttributes($request->validated());

        if ($request->hasFile('lampiran')) {
            $validated['lampiran'] = $files->store($request->file('lampiran'), 'pengumuman');
        }

        $validated['pembuat_id'] = $user->id;

        $pengumuman = Pengumuman::create($validated);

        if ($pengumuman->isPublished()) {
            $this->notifications->pengumumanPublished($pengumuman, $user->id);
        }

        $message = $pengumuman->isScheduled()
            ? 'Pengumuman draft terjadwal berhasil disimpan.'
            : 'Pengumuman berhasil ditambahkan.';

        return redirect()
            ->back()
            ->with('success', $message);
    }

    /**
     * Update Pengumuman
     */
    public function update(PengumumanRequest $request, Pengumuman $pengumuman, FileCompressionService $files): RedirectResponse
    {
        if (! $pengumuman->canBeManagedBy($request->user())) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah pengumuman ini.');
        }

        $validated = $this->publisher->resolveAttributes($request->validated());

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

        $message = $pengumuman->isScheduled()
            ? 'Jadwal pengumuman berhasil diperbarui.'
            : 'Pengumuman berhasil diperbarui.';

        return redirect()->back()->with('success', $message);
    }

    public function destroy(Request $request, Pengumuman $pengumuman): RedirectResponse
    {
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
