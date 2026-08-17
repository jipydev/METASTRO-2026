<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /** @return array<string, mixed> */
    private function jabatanFormData(): array
    {
        return [
            'stakeholderDivisiId' => Divisi::query()->where('nama', 'Stakeholder')->value('id'),
            'operationalJabatan' => Jabatan::orderedOperational(),
            'stakeholderJabatan' => Jabatan::orderedStakeholder(),
        ];
    }
    public function index(Request $request)
    {
        $users = User::with(['divisi', 'jabatan', 'roles'])
            // 1. Filter pencarian, divisi, dll tetap di sini
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama', 'like', "%{$search}%")
                        ->orWhere('nim', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('divisi_id'), function ($q) use ($request) {
                $q->where('divisi_id', $request->divisi_id);
            })
            ->when($request->filled('jabatan_id'), function ($q) use ($request) {
                $q->where('jabatan_id', $request->jabatan_id);
            })
            ->when($request->filled('role'), function ($q) use ($request) {
                $q->role($request->role);
            })
            ->orderByRaw("CASE
        WHEN EXISTS (
            SELECT 1 FROM model_has_roles
            INNER JOIN roles ON roles.id = model_has_roles.role_id
            WHERE model_has_roles.model_id = users.id
              AND model_has_roles.model_type = ?
              AND roles.name = 'admin'
        ) THEN 1
        WHEN EXISTS (
            SELECT 1 FROM model_has_roles
            INNER JOIN roles ON roles.id = model_has_roles.role_id
            WHERE model_has_roles.model_id = users.id
              AND model_has_roles.model_type = ?
              AND roles.name = 'panitia'
        ) THEN 2
        ELSE 3
    END asc", [User::class, User::class])
            ->orderBy('divisi_id', 'asc')
            ->orderBy('jabatan_id', 'asc')
            ->orderBy('nama', 'asc')
            ->paginate(15)
            ->withQueryString();

        $divisis = Divisi::orderBy('nama', 'asc')->get();
        $jabatans = Jabatan::orderBy('nama', 'asc')->get();
        $roles = Role::orderBy('name', 'asc')->get();

        $data = [
            'title' => 'Kelola Pengguna & QR Code',
            'users' => $users,
            'divisis' => $divisis,
            'jabatans' => $jabatans,
            'roles' => $roles,
        ];

        return view('admin.users.index', $data);
    }

    /**
     * Menampilkan halaman tambah pengguna.
     */
    public function create()
    {
        $divisis = Divisi::orderBy('nama', 'asc')->get();
        $jabatans = Jabatan::orderBy('nama', 'asc')->get();
        $roles = Role::orderBy('name', 'asc')->get();

        $data = [
            'title' => 'Tambah Pengguna Baru',
            'divisis' => $divisis,
            'jabatans' => $jabatans,
            'roles' => $roles,
            ...$this->jabatanFormData(),
        ];

        return view('admin.users.create', $data);
    }

    /**
     * Menyimpan pengguna baru.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $divisiId = strtolower($validated['role']) === 'peserta'
            ? null
            : ($validated['divisi_id'] ?: null);
        $jabatanId = strtolower($validated['role']) === 'peserta'
            ? null
            : ($validated['jabatan_id'] ?: null);

        $user = User::create([
            'nama' => $validated['nama'],
            'nim' => $validated['nim'],
            'email' => $validated['email'] ?? null,
            'password' => $validated['password'], // Pastikan model User Anda punya casts password => 'hashed'
            'divisi_id' => $divisiId,
            'jabatan_id' => $jabatanId,
            'status' => true,
            'is_initial_setup_completed' => false,
            'qr_token' => (string) Str::uuid(),
            'qr_updated_at' => now(),
        ]);

        // Berikan role menggunakan Spatie
        $user->assignRole($validated['role']);

        // Ganti back() menjadi redirect()
        return redirect()->route('admin.users.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan halaman edit pengguna.
     */
    public function edit(User $user)
    {
        $divisis = Divisi::orderBy('nama', 'asc')->get();
        $jabatans = Jabatan::orderBy('nama', 'asc')->get();
        $roles = Role::orderBy('name', 'asc')->get();

        $data = [
            'title' => 'Edit Pengguna',
            'user' => $user,
            'divisis' => $divisis,
            'jabatans' => $jabatans,
            'roles' => $roles,
            ...$this->jabatanFormData(),
        ];

        return view('admin.users.edit', $data);
    }

    /**
     * Memperbarui data pengguna.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        $divisiId = strtolower($validated['role']) === 'peserta'
            ? null
            : ($validated['divisi_id'] ?: null);
        $jabatanId = strtolower($validated['role']) === 'peserta'
            ? null
            : ($validated['jabatan_id'] ?: null);

        $user->update([
            'nama' => $validated['nama'],
            'nim' => $validated['nim'],
            'email' => $validated['email'] ?? null,
            'divisi_id' => $divisiId,
            'jabatan_id' => $jabatanId,
            'status' => (bool) $validated['status'], // Update status aktif/non-aktif
        ]);

        // Sinkronisasi/Update role menggunakan Spatie
        $user->syncRoles([$validated['role']]);

        // Ganti back() menjadi redirect()
        return redirect()->route('admin.users.index')->with('success', "Data pengguna {$user->nama} berhasil diperbarui.");
    }

    /**
     * Menghapus pengguna.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Reset QR Code untuk 1 user
     */
    public function resetQr(User $user)
    {
        $user->update([
            'qr_token' => (string) Str::uuid(),
            'qr_updated_at' => now(), // <-- Diperbarui agar waktu "Terakhir Update QR" ikut ter-refresh
        ]);

        return back()->with('success', "QR Code untuk {$user->nama} berhasil di-generate ulang.");
    }

    /**
     * Reset QR Code untuk semua user sekaligus
     */
    public function resetAllQr()
    {
        User::query()->each(function (User $user) {
            $user->update([
                'qr_token' => (string) Str::uuid(),
                'qr_updated_at' => now(), // <-- Diperbarui untuk semua user
            ]);
        });

        return back()->with('success', 'Seluruh token QR Code pengguna berhasil di-generate ulang.');
    }
}
