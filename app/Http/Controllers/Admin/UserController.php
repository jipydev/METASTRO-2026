<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display all users with search & filter.
     */
    public function index(Request $request): View
    {
        $users = User::with(['divisi', 'jabatan'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nim', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->divisi_id, fn($q, $divisiId) => $q->where('divisi_id', $divisiId))
            ->when($request->jabatan_id, fn($q, $jabatanId) => $q->where('jabatan_id', $jabatanId))
            ->when($request->filled('status'), fn($q) => $q->where('status', (bool) $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'title'    => 'Manajemen Pengguna',
            'users'    => $users,
            'divisis'  => Divisi::orderBy('nama', 'asc')->get(),
            'jabatans' => Jabatan::orderBy('nama', 'asc')->get(),
        ]);
    }

    /**
     * Show create user form.
     */
    public function create(): View
    {
        return view('admin.users.create', [
            'title'    => 'Tambah Pengguna Baru',
            'divisis'  => Divisi::orderBy('nama', 'asc')->get(),
            'jabatans' => Jabatan::orderBy('nama', 'asc')->get(),
        ]);
    }

    /**
     * Store new user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'       => 'required|string|max:100',
            'nim'        => 'required|string|max:20|unique:users,nim',
            'email'      => 'nullable|email|max:255|unique:users,email',
            'password'   => 'required|string|min:6',
            'divisi_id'  => 'nullable|exists:divisis,id',
            'jabatan_id' => 'nullable|exists:jabatans,id',
        ]);

        $user = User::create([
            'nama'                       => $validated['nama'],
            'nim'                        => $validated['nim'],
            'email'                      => $validated['email'] ?? null,
            'password'                   => Hash::make($validated['password']),
            'divisi_id'                  => $validated['divisi_id'] ?? null,
            'jabatan_id'                 => $validated['jabatan_id'] ?? null,
            'status'                     => true,
            'is_initial_setup_completed' => false,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna {$user->nama} (NIM: {$user->nim}) berhasil ditambahkan.");
    }

    /**
     * Show edit user form.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'title'    => 'Edit Pengguna',
            'user'     => $user,
            'divisis'  => Divisi::orderBy('nama', 'asc')->get(),
            'jabatans' => Jabatan::orderBy('nama', 'asc')->get(),
        ]);
    }

    /**
     * Update user details.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'nama'       => 'required|string|max:100',
            'nim'        => ['required', 'string', 'max:20', Rule::unique('users', 'nim')->ignore($user->id)],
            'email'      => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'divisi_id'  => 'nullable|exists:divisis,id',
            'jabatan_id' => 'nullable|exists:jabatans,id',
            'status'     => 'required|boolean',
            'password'   => 'nullable|string|min:6',
        ]);

        if ($user->id === Auth::id() && ! $validated['status']) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $updateData = [
            'nama'       => $validated['nama'],
            'nim'        => $validated['nim'],
            'email'      => $validated['email'] ?? null,
            'divisi_id'  => $validated['divisi_id'] ?? null,
            'jabatan_id' => $validated['jabatan_id'] ?? null,
            'status'     => (bool) $validated['status'],
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', "Data pengguna {$user->nama} berhasil diperbarui.");
    }

    /**
     * Delete user.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $nama = $user->nama;
        $user->delete();

        return back()->with('success', "Pengguna {$nama} berhasil dihapus.");
    }
}
