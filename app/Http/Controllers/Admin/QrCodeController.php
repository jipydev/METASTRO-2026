<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QrCodeController extends Controller
{
    /**
     * Show list of all users with QR management options.
     */
    public function index(): View
    {
        $users = User::where('status', true)
            ->with(['divisi', 'jabatan'])
            ->orderBy('nama')
            ->paginate(20);

            $data = [
                'title' => 'Kelola QR Code',
                'users' => $users,
            ];

        return view('admin.qr.index', $data);
    }

    /**
     * Re-generate QR code for a specific user.
     */
    public function regenerate(User $user, QrCodeService $qrService): RedirectResponse
    {
        $qrService->regenerateForUser($user);

        return redirect()
            ->route('admin.qr.index')
            ->with('success', "QR Code untuk {$user->nama} berhasil di-regenerate.");
    }

    /**
     * Bulk re-generate QR codes for all users.
     */
    public function regenerateAll(QrCodeService $qrService): RedirectResponse
    {
        $count = 0;

        User::where('status', true)->chunkById(50, function ($users) use ($qrService, &$count) {
            /** @var User $user */
            foreach ($users as $user) {
                $qrService->regenerateForUser($user);
                $count++;
            }
        });

        return redirect()
            ->route('admin.qr.index')
            ->with('success', "QR Code untuk {$count} pengguna aktif berhasil di-regenerate.");
    }
}
