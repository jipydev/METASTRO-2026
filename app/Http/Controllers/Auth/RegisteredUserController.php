<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Menampilkan halaman register.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Menyimpan user baru.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            dd($request->all()),
            'name' => ['required', 'string', 'max:255'],
            // 'email' => [
            //     'required',
            //     'string',
            //     'lowercase',
            //     'email',
            //     'max:255',
            //     'unique:users,email',
            // ],
            'nim' => [
                'required',
                'string',
                'max:20',
                'unique:users,nim',
            ],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            // 'email' => $validated['email'],
            'nim' => $validated['nim'],
            'password' => Hash::make($validated['password']),
        ]);

        // Semua user baru otomatis menjadi Panitia
        $user->assignRole('Panitia');

        event(new Registered($user));

        Auth::login($user);

        // Redirect berdasarkan role
        if ($user->hasRole('Admin')) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }
}
