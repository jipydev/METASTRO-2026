<?php

namespace App\Http\Controllers;

use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class InitialSetupController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->is_initial_setup_completed) {
            return redirect()->route('dashboard');
        }

        return view('auth.initial_setup', compact('user'));
    }

    public function store(Request $request, QrCodeService $qrService)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Upload Photo
        $fotoPath = $request->file('foto')->store('foto_profil', 'public');

        // Update User
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->foto = $fotoPath;
        $user->password = Hash::make($validated['password']);
        $user->is_initial_setup_completed = true;
        $user->save();

        // Generate QR Code token if not exists
        $qrService->generateForUser($user);

        return redirect()->route('dashboard')->with('success', 'Profil dan password Anda berhasil diperbarui! Selamat datang di Metastro.');
    }
}
