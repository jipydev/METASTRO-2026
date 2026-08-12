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
        $users = User::where('status_aktif', true)
            ->with('divisi', 'jabatan')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    /**
     * Re-generate QR code for a specific user.
     */
    public function regenerate(User $user, QrCodeService $qrService): RedirectResponse
    {
        $qrService->regenerateForUser($user);

        return redirect()
            ->route('admin.users')
            ->with('success', "QR Code untuk {$user->name} berhasil di-regenerate.");
    }

    /**
     * Bulk re-generate QR codes for all users.
     */
    public function regenerateAll(QrCodeService $qrService): RedirectResponse
    {
        $count = 0;

        User::where('status_aktif', true)->chunkById(50, function ($users) use ($qrService, &$count) {
            foreach ($users as $user) {
                $qrService->regenerateForUser($user);
                $count++;
            }
        });

        return redirect()
            ->route('admin.users')
            ->with('success', "QR Code untuk {$count} user berhasil di-regenerate.");
    }
}
