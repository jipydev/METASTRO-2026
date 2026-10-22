<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHukumanRequest;
use App\Http\Requests\SubmitPembelaanHukumanRequest;
use App\Http\Requests\SubmitTugasHukumanRequest;
use App\Models\Hukuman;
use App\Models\User;
use App\Services\NotificationDispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HukumanController extends Controller
{
    public function __construct(private NotificationDispatcher $notifications) {}

    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $hukumans = Hukuman::query()
            ->where('user_id', $user->id)
            ->with(['issuer.divisi', 'issuer.jabatan'])
            ->latest()
            ->paginate(10);

        return view('hukuman.index', [
            'title' => 'Hukuman Saya',
            'hukumans' => $hukumans,
        ]);
    }

    public function kelola(Request $request, string $mode = 'ranger'): View
    {
        /** @var User $user */
        $user = Auth::user();

        $hukumans = Hukuman::query()
            ->when(! $user->isAdmin(), fn($q) => $q->where('issued_by', $user->id))
            ->with(['user.divisi', 'user.jabatan', 'issuer.divisi', 'issuer.jabatan'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('hukuman.kelola', [
            'title' => 'Kelola Hukuman',
            'mode' => $mode, // <-- Kirim variabel mode ke view
            'hukumans' => $hukumans,
            'isAdminIssuer' => $user->isAdmin(),
        ]);
    }

    public function create(): View|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->canManageHukuman()) {
            abort(403);
        }

        $targets = $this->getUnifiedTargets($user)
            ->with(['divisi', 'jabatan'])
            ->orderBy('nama')
            ->get();

        return view('hukuman.create', [
            'title' => 'Berikan Hukuman',
            'targets' => $targets,
            'kategoriOptions' => Hukuman::KATEGORI,
        ]);
    }

    public function edit(Hukuman $hukuman): View
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->canManageHukumanRecord($hukuman)) {
            abort(403);
        }

        $targets = $this->getUnifiedTargets($user)
            ->with(['divisi', 'jabatan'])
            ->orderBy('nama')
            ->get();

        if ($hukuman->user && ! $targets->contains('id', $hukuman->user_id)) {
            $targets->push($hukuman->user->loadMissing(['divisi', 'jabatan']));
            $targets = $targets->sortBy('nama')->values();
        }

        return view('hukuman.create', [
            'title' => 'Edit Hukuman',
            'hukuman' => $hukuman,
            'targets' => $targets,
            'kategoriOptions' => Hukuman::KATEGORI,
        ]);
    }

    public function store(StoreHukumanRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->canManageHukuman()) {
            abort(403);
        }

        $validated = $request->validated();
        $targetUser = User::query()->findOrFail($validated['user_id']);

        // Tentukan issuer_mode yang benar ('pengawas' jika target pengawas, selain itu 'ranger')
        $issuerMode = $targetUser->isTargetHukumanPengawas() ? 'pengawas' : 'ranger';

        $hukuman = Hukuman::create([
            'user_id' => $validated['user_id'],
            'issued_by' => $user->id,
            'kategori' => $validated['kategori'],
            'issuer_mode' => $issuerMode,
            'alasan' => $validated['alasan'],
            'deadline_at' => now()->addDays(2),
        ]);

        $this->notifications->hukumanIssued($hukuman);

        return redirect()
            ->route('dashboard.hukuman.kelola')
            ->with('success', 'Hukuman berhasil diterbitkan.');
    }

    public function update(StoreHukumanRequest $request, Hukuman $hukuman): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->canManageHukumanRecord($hukuman)) {
            abort(403);
        }

        $validated = $request->validated();
        $targetUser = User::query()->findOrFail($validated['user_id']);
        $previousUserId = $hukuman->user_id;
        $targetChanged = (int) $validated['user_id'] !== (int) $previousUserId;

        $issuerMode = $targetUser->isTargetHukumanPengawas() ? 'pengawas' : 'ranger';

        $payload = [
            'user_id' => $validated['user_id'],
            'kategori' => $validated['kategori'],
            'issuer_mode' => $issuerMode,
            'alasan' => $validated['alasan'],
        ];

        if ($targetChanged) {
            $payload = array_merge($payload, [
                'pembelaan' => null,
                'pembelaan_at' => null,
                'tugas_link' => null,
                'tugas_submitted_at' => null,
                'selesai_at' => null,
                'deadline_at' => now()->addDays(2),
            ]);
        }

        $hukuman->update($payload);
        $hukuman = $hukuman->fresh(['user', 'issuer']);

        if ($targetChanged) {
            $previousTarget = User::query()->find($previousUserId);

            if ($previousTarget) {
                $this->notifications->hukumanCancelled($hukuman, $previousTarget);
            }

            $this->notifications->hukumanIssued($hukuman);
        } else {
            $this->notifications->hukumanUpdated($hukuman);
        }

        return redirect()
            ->route('dashboard.hukuman.show', $hukuman)
            ->with('success', 'Hukuman berhasil diperbarui.');
    }

    public function destroy(Hukuman $hukuman): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->canManageHukumanRecord($hukuman)) {
            abort(403);
        }

        $target = $hukuman->user;

        if ($target) {
            $this->notifications->hukumanCancelled($hukuman, $target);
        }

        $hukuman->delete();

        return redirect()
            ->route('dashboard.hukuman.kelola')
            ->with('success', 'Hukuman berhasil dihapus.');
    }

    public function show(Hukuman $hukuman): View|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $this->canView($user, $hukuman)) {
            abort(403);
        }

        $hukuman->load(['user.divisi', 'user.jabatan', 'issuer.divisi', 'issuer.jabatan']);

        return view('hukuman.show', [
            'title' => 'Detail Hukuman',
            'hukuman' => $hukuman,
            'isTarget' => $user->id === $hukuman->user_id,
            'canManage' => $user->canManageHukumanRecord($hukuman),
        ]);
    }

    public function submitPembelaan(SubmitPembelaanHukumanRequest $request, Hukuman $hukuman): RedirectResponse
    {
        $hukuman->update([
            'pembelaan' => $request->validated('pembelaan'),
            'pembelaan_at' => now(),
        ]);

        $this->notifications->hukumanPembelaanSubmitted($hukuman->fresh());

        return redirect()
            ->route('dashboard.hukuman.show', $hukuman)
            ->with('success', 'Pembelaan berhasil dikirim. Silakan kerjakan tugas hukuman Anda.');
    }

    public function submitTugas(SubmitTugasHukumanRequest $request, Hukuman $hukuman): RedirectResponse
    {
        $link = $request->validated('tugas_link');

        $hukuman->update([
            'tugas_link' => $link,
            'tugas_submitted_at' => $link ? now() : null,
        ]);

        if ($link) {
            $this->notifications->hukumanTugasSubmitted($hukuman->fresh());
        }

        return redirect()
            ->route('dashboard.hukuman.show', $hukuman)
            ->with('success', $link ? 'Link tugas berhasil disimpan.' : 'Link tugas dihapus.');
    }

    public function complete(Request $request, Hukuman $hukuman): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->id !== $hukuman->user_id || $hukuman->isSelesai() || ! $hukuman->sudahPembelaan()) {
            abort(403);
        }

        $hukuman->update(['selesai_at' => now()]);

        $this->notifications->hukumanCompleted($hukuman->fresh());

        return redirect()
            ->route('dashboard.hukuman.index')
            ->with('success', 'Hukuman ditandai selesai. Terima kasih sudah menyelesaikan tugas.');
    }

    private function getUnifiedTargets(User $user)
    {
        if ($user->canIssueHukumanPengawas() && ! $user->canIssueHukumanRanger() && ! $user->isAdmin()) {
            return User::hukumanTargetPengawas();
        }

        return User::query()
            ->where('status', true)
            ->where(function ($query) use ($user) {
                // Memasukkan target peserta
                $query->whereNull('divisi_id')->whereNull('jabatan_id');

                // Memasukkan target panitia sesuai hak akses
                $query->orWhere(function ($q) use ($user) {
                    $q->whereNotNull('divisi_id');
                    if (! $user->isAdmin()) {
                        $q->where(function ($inner) {
                            $inner->whereNull('jabatan_id')
                                ->orWhereHas('jabatan', fn($jabatan) => $jabatan->whereNotIn('nama', [
                                    'Pengawas',
                                    'Ketua Pengawas',
                                    'Wakil Ketua Pengawas',
                                ]));
                        });
                    }
                });
            });
    }

    private function canView(User $user, Hukuman $hukuman): bool
    {
        if ($user->id === $hukuman->user_id) {
            return true;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id === $hukuman->issued_by) {
            return true;
        }

        return false;
    }
}
