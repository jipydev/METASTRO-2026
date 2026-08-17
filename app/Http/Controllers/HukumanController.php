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

    public function kelola(Request $request, string $mode = 'ranger'): View|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $this->canManageMode($user, $mode)) {
            abort(403);
        }

        $hukumans = Hukuman::query()
            ->where('issuer_mode', $mode)
            ->when($mode === 'pengawas', fn ($q) => $q->where('issued_by', $user->id))
            ->when($mode === 'ranger' && ! $user->isAdmin(), fn ($q) => $q->where('issued_by', $user->id))
            ->with(['user.divisi', 'user.jabatan', 'issuer.divisi', 'issuer.jabatan'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('hukuman.kelola', [
            'title' => $mode === 'pengawas' ? 'Kelola Hukuman Pengawas' : 'Kelola Hukuman',
            'mode' => $mode,
            'hukumans' => $hukumans,
            'isAdminIssuer' => $user->isAdmin() && $mode === 'ranger',
        ]);
    }

    public function create(string $mode = 'ranger'): View|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $this->canManageMode($user, $mode)) {
            abort(403);
        }

        $targets = $this->targetQuery($user, $mode)
            ->with(['divisi', 'jabatan'])
            ->orderBy('nama')
            ->get();

        return view('hukuman.create', [
            'title' => $mode === 'pengawas' ? 'Hukum Pengawas' : 'Berikan Hukuman',
            'mode' => $mode,
            'targets' => $targets,
            'kategoriOptions' => Hukuman::KATEGORI,
            'isAdminIssuer' => $user->isAdmin() && $mode === 'ranger',
        ]);
    }

    public function store(StoreHukumanRequest $request, string $mode = 'ranger'): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $this->canManageMode($user, $mode)) {
            abort(403);
        }

        $validated = $request->validated();

        $hukuman = Hukuman::create([
            'user_id' => $validated['user_id'],
            'issued_by' => $user->id,
            'kategori' => $validated['kategori'],
            'issuer_mode' => $mode,
            'alasan' => $validated['alasan'],
            'deadline_at' => now()->addDays(2),
        ]);

        $this->notifications->hukumanIssued($hukuman);

        return redirect()
            ->route('hukuman.kelola', ['mode' => $mode])
            ->with('success', 'Hukuman berhasil diterbitkan.');
    }

    public function edit(Hukuman $hukuman): View
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->canManageHukumanRecord($hukuman)) {
            abort(403);
        }

        $mode = $hukuman->issuer_mode === 'pengawas' ? 'pengawas' : 'ranger';
        $targets = $this->targetQuery($user, $mode)
            ->with(['divisi', 'jabatan'])
            ->orderBy('nama')
            ->get();

        if ($hukuman->user && ! $targets->contains('id', $hukuman->user_id)) {
            $targets->push($hukuman->user->loadMissing(['divisi', 'jabatan']));
            $targets = $targets->sortBy('nama')->values();
        }

        return view('hukuman.create', [
            'title' => 'Edit Hukuman',
            'mode' => $mode,
            'hukuman' => $hukuman,
            'targets' => $targets,
            'kategoriOptions' => Hukuman::KATEGORI,
            'isAdminIssuer' => $user->isAdmin() && $mode === 'ranger',
        ]);
    }

    public function update(StoreHukumanRequest $request, Hukuman $hukuman): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->canManageHukumanRecord($hukuman)) {
            abort(403);
        }

        $validated = $request->validated();
        $previousUserId = $hukuman->user_id;
        $targetChanged = (int) $validated['user_id'] !== (int) $previousUserId;

        $payload = [
            'user_id' => $validated['user_id'],
            'kategori' => $validated['kategori'],
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
            ->route('hukuman.show', $hukuman)
            ->with('success', 'Hukuman berhasil diperbarui.');
    }

    public function destroy(Hukuman $hukuman): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->canManageHukumanRecord($hukuman)) {
            abort(403);
        }

        $mode = $hukuman->issuer_mode === 'pengawas' ? 'pengawas' : 'ranger';
        $target = $hukuman->user;

        if ($target) {
            $this->notifications->hukumanCancelled($hukuman, $target);
        }

        $hukuman->delete();

        return redirect()
            ->route('hukuman.kelola', ['mode' => $mode])
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
            ->route('hukuman.show', $hukuman)
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
            ->route('hukuman.show', $hukuman)
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
            ->route('hukuman.index')
            ->with('success', 'Hukuman ditandai selesai. Terima kasih sudah menyelesaikan tugas.');
    }

    private function canManageMode(User $user, string $mode): bool
    {
        return match ($mode) {
            'pengawas' => $user->canIssueHukumanPengawas(),
            default => $user->canIssueHukumanRanger(),
        };
    }

    /** @return \Illuminate\Database\Eloquent\Builder<User> */
    private function targetQuery(User $user, string $mode)
    {
        return match ($mode) {
            'pengawas' => User::hukumanTargetPengawas(),
            default => $user->isAdmin()
                ? User::hukumanTargetAdmin()
                : User::hukumanTargetRanger(),
        };
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
