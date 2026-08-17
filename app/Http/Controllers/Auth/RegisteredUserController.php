<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\RoleRequest;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Menampilkan halaman register.
     */
    public function create(): View
    {
        $divisis = Divisi::all();
        $jabatans = Jabatan::whereIn('nama_jabatan', ['Koordinator', 'Staff'])->get();
        // Role pilihan (semua role kecuali Admin)
        $roles = Role::where('name', '!=', 'Admin')->get();

        return view('auth.register', compact('divisis', 'jabatans', 'roles') + ['title' => 'Daftar']);
    }

    /**
     * Menyimpan user baru.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'nim' => ['required', 'string', 'max:20', 'unique:users,nim'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'divisi_id' => ['required', 'exists:divisis,id'],
                'jabatan_id' => ['required', 'exists:jabatans,id'],
                'role' => ['required', 'string', 'exists:roles,name'],
            ],
            [
                'name.required' => 'Nama lengkap wajib diisi.',
                'nim.required' => 'NIM wajib diisi.',
                'nim.unique' => 'NIM ini sudah dipakai akun lain. Gunakan NIM yang berbeda.',
                'password.required' => 'Password wajib diisi.',
                'password.confirmed' => 'Ulangi password belum sama. Pastikan keduanya sama.',
                'divisi_id.required' => 'Divisi wajib dipilih.',
                'divisi_id.exists' => 'Divisi yang dipilih tidak valid.',
                'jabatan_id.required' => 'Jabatan wajib dipilih.',
                'jabatan_id.exists' => 'Jabatan yang dipilih tidak valid.',
                'role.required' => 'Role wajib dipilih.',
                'role.exists' => 'Role yang dipilih tidak valid.',
            ]
        );

        if ($validated['role'] === 'Admin') {
            throw ValidationException::withMessages([
                'role' => 'Role Admin tidak dapat dipilih pada registrasi mandiri.',
            ]);
        }

        $jabatan = Jabatan::findOrFail($validated['jabatan_id']);
        $divisi = Divisi::findOrFail($validated['divisi_id']);

        // Jika memilih jabatan Koordinator atau role Koordinator, cek apakah divisi tersebut sudah memiliki Koordinator
        if ($jabatan->nama_jabatan === 'Koordinator' || $validated['role'] === 'Koordinator') {
            if ($divisi->koordinator_divisi_nim) {
                throw ValidationException::withMessages([
                    'divisi_id' => "Divisi {$divisi->nama_divisi} sudah memiliki Koordinator.",
                ]);
            }
        }

        // Buat user
        $user = User::create([
            'name' => $validated['name'],
            'nim' => $validated['nim'],
            'password' => Hash::make($validated['password']),
            'divisi_id' => $validated['divisi_id'],
            'jabatan_id' => $validated['jabatan_id'],
        ]);

        // Semua akun otomatis adalah panitia
        $user->assignRole('Panitia');

        // Jika memilih role selain Panitia atau jabatan Koordinator, buat RoleRequest untuk di-review Admin
        $needsApproval = ($validated['role'] !== 'Panitia' || $jabatan->nama_jabatan === 'Koordinator');

        if ($needsApproval) {
            RoleRequest::create([
                'user_id' => $user->id,
                'requested_role' => $validated['role'],
                'requested_divisi_id' => $validated['divisi_id'],
                'requested_jabatan_id' => $validated['jabatan_id'],
                'status' => 'Pending',
            ]);
        } else {
            // Auto-assign role jika hanya Panitia & Staff
            if ($validated['role'] !== 'Panitia') {
                $user->assignRole($validated['role']);
            }
        }

        // Auto-generate QR code untuk absensi
        $qrService = app(QrCodeService::class);
        $qrService->generateForUser($user);

        event(new Registered($user));

        Auth::login($user);

        if ($needsApproval) {
            session()->flash('info', 'Registrasi berhasil! Pendaftaran role/jabatan Anda ('.$validated['role'].' - '.$jabatan->nama_jabatan.') sedang menunggu persetujuan Admin.');
        } else {
            session()->flash('success', 'Registrasi berhasil! Selamat datang di Metastro.');
        }

        return redirect()->route('dashboard');
    }
}
