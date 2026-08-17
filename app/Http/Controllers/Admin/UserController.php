<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
        $query = User::with(['divisi', 'jabatan']);

        // Filter search nama / NIM
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter divisi
        if ($request->filled('divisi_id')) {
            $query->where('divisi_id', $request->divisi_id);
        }

        // Filter role virtual
        if ($request->filled('role')) {
            if ($request->role === 'admin') {
                $query->whereHas('divisi', fn ($q) => $q->where('nama', 'like', '%chiper%'));
            } elseif ($request->role === 'panitia') {
                $query->whereNotNull('divisi_id')
                    ->whereDoesntHave('divisi', fn ($q) => $q->where('nama', 'like', '%chiper%'));
            } elseif ($request->role === 'peserta') {
                $query->whereNull('divisi_id');
            }
        }

        $users = User::with(['divisi', 'jabatan'])
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
                if ($request->role === 'admin') {
                    $q->whereHas('divisi', fn ($sub) => $sub->where('nama', 'like', '%chiper%'));
                } elseif ($request->role === 'panitia') {
                    $q->whereNotNull('divisi_id')->whereDoesntHave('divisi', fn ($sub) => $sub->where('nama', 'like', '%chiper%'));
                } elseif ($request->role === 'peserta') {
                    $q->whereNull('divisi_id');
                }
            })
            // 2. Urutkan berdasarkan logika prioritas user di sistem Anda:
            // Admin (yang punya divisi Chiper) ditaruh paling atas, lalu Panitia (punya divisi), lalu Peserta (divisi null)
            ->orderByRaw("CASE 
        WHEN divisi_id IN (SELECT id FROM divisis WHERE nama LIKE '%chiper%') THEN 1 
        WHEN divisi_id IS NOT NULL THEN 2 
        ELSE 3 
    END asc")
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

        $divisiId = $validated['divisi_id'] ?: null;

        if (strtolower($validated['role']) === 'admin') {
            $chiperDivisi = Divisi::where('nama', 'like', '%chiper%')->first();
            if ($chiperDivisi) {
                $divisiId = $chiperDivisi->id;
            }
        } elseif (strtolower($validated['role']) === 'peserta') {
            $divisiId = null;
        }

        $user = User::create([
            'nama' => $validated['nama'],
            'nim' => $validated['nim'],
            'email' => $validated['email'] ?? null,
            'password' => $validated['password'], // Pastikan model User Anda punya casts password => 'hashed'
            'divisi_id' => $divisiId,
            'jabatan_id' => $validated['jabatan_id'] ?: null,
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

        $divisiId = $validated['divisi_id'] ?: null;

        if (strtolower($validated['role']) === 'admin') {
            $chiperDivisi = Divisi::where('nama', 'like', '%chiper%')->first();
            if ($chiperDivisi) {
                $divisiId = $chiperDivisi->id;
            }
        } elseif (strtolower($validated['role']) === 'peserta') {
            $divisiId = null;
        }

        $user->update([
            'nama' => $validated['nama'],
            'nim' => $validated['nim'],
            'email' => $validated['email'] ?? null,
            'divisi_id' => $divisiId,
            'jabatan_id' => $validated['jabatan_id'] ?: null,
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
