<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Services\FileCompressionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Menampilkan form edit profil.
     */
    public function edit(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        return view('profile.edit', [
            'title' => 'Pengaturan Profil',
            'user' => $user->load(['divisi', 'jabatan']),
        ]);
    }

    /**
     * Memperbarui informasi profil (Nama, NIM, Email, dan Foto).
     */
    public function update(ProfileUpdateRequest $request, FileCompressionService $files): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        // Handle upload foto baru
        if ($request->hasFile('foto')) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $path = $files->store($request->file('foto'), 'foto_profil');
            $user->foto = $path;
        }

        $user->fill($request->safe()->only(['nama', 'nim', 'email']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
