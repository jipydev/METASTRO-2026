<?php

namespace App\Http\Controllers;

use App\Http\Requests\InitialSetupRequest;
use App\Models\User;
use App\Services\FileCompressionService;
use App\Services\QrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
            return redirect()->route('dashboard.index');
        }

        return view('auth.initial-setup', [
            'title' => 'Lengkapi Profil Anda',
            'user' => $user,
        ]);
    }

    /**
     * Simpan data onboarding (Nama, Email, Foto, Password Baru) & generate QR.
     */
    public function store(InitialSetupRequest $request, QrCodeService $qrService, FileCompressionService $files): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validated([
            'foto' => 'nullable|image|max:2048', // Maksimal 2MB
            'nim' => 'nullable|string|max:20',
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'nomor_hp' => 'nullable|string|max:20',
            'password' => 'required|confirmed|min:8',
            'password_confirmation' => 'required'
        ]);

        // Hapus foto lama jika ada
        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        // Peserta tidak menggunakan foto profil.
        $fotoPath = null;
        if (! $user->isPeserta()) {
            $fotoPath = $files->store($request->file('foto'), 'foto_profil');
        }

        // Update data user
        $user->update([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'nomor_hp' => $validated['nomor_hp'],
            'foto' => $fotoPath,
            'password' => Hash::make($validated['password']),
            'is_initial_setup_completed' => true,
        ]);

        // Generate QR code token & SVG image
        $qrService->generateForUser($user);

        return redirect()->route('dashboard.index')
            ->with('success', 'Profil dan password berhasil diperbarui! Selamat datang di Portal Metastro.');
    }
}
