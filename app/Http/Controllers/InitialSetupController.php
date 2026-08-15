<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class InitialSetupController extends Controller
{
    /**
     * Tampilkan form onboarding / setup profil awal jika belum selesai.
     */
    public function index(): View|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->is_initial_setup_completed) {
            return redirect()->route('dashboard');
        }

        return view('auth.initial-setup', [
            'title' => 'Lengkapi Profil Anda',
            'user'  => $user,
        ]);
    }

    /**
     * Simpan data onboarding (Nama, Email, Foto, Password Baru) & generate QR.
     */
    public function store(Request $request, QrCodeService $qrService): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'nama'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'foto'     => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Hapus foto lama jika ada
        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        // Upload foto baru ke storage/app/public/foto_profil
        $fotoPath = $request->file('foto')->store('foto_profil', 'public');

        // Update data user
        $user->update([
            'nama'                       => $validated['nama'],
            'email'                      => $validated['email'],
            'foto'                       => $fotoPath,
            'password'                   => Hash::make($validated['password']),
            'is_initial_setup_completed' => true,
        ]);

        // Generate QR code token & SVG image
        $qrService->generateForUser($user);

        return redirect()->route('dashboard')
            ->with('success', 'Profil dan password berhasil diperbarui! Selamat datang di Portal Metastro.');
    }
}
