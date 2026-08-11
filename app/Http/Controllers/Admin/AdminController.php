<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\RoleRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index()
    {
        return redirect()->route('dashboard');
    }

    public function manageUsers(Request $request)
    {
        $search = $request->query('search');
        $roleFilter = $request->query('role');
        $divisiFilter = $request->query('divisi_id');
        $jabatanFilter = $request->query('jabatan_id');

        $query = User::with(['divisi', 'jabatan', 'roles']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleFilter) {
            $query->role($roleFilter);
        }

        if ($divisiFilter) {
            $query->where('divisi_id', $divisiFilter);
        }

        if ($jabatanFilter) {
            $query->where('jabatan_id', $jabatanFilter);
        }

        $users = $query->latest()->paginate(15)->appends($request->all());

        $allRoles = Role::orderBy('name')->get();
        $allDivisis = Divisi::orderBy('nama_divisi')->get();
        $allJabatans = Jabatan::orderBy('nama_jabatan')->get();

        return view('admin.manage_users', compact(
            'users',
            'allRoles',
            'allDivisis',
            'allJabatans',
            'search',
            'roleFilter',
            'divisiFilter',
            'jabatanFilter'
        ));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
            'divisi_id' => 'nullable|exists:divisi,id',
            'jabatan_id' => 'nullable|exists:jabatan,id',
            'status_aktif' => 'required|boolean',
        ]);

        // Sync Spatie Role
        $user->syncRoles([$validated['role']]);

        // Update User attributes
        $user->divisi_id = $validated['divisi_id'];
        $user->jabatan_id = $validated['jabatan_id'];
        $user->status_aktif = (bool) $validated['status_aktif'];
        $user->save();

        // If Jabatan is Koordinator, update koordinator_divisi_nim
        if ($user->jabatan && $user->jabatan->nama_jabatan === 'Koordinator' && $user->divisi_id) {
            $divisi = Divisi::find($user->divisi_id);
            if ($divisi) {
                $divisi->koordinator_divisi_nim = $user->nim;
                $divisi->save();
            }
        }

        return redirect()->back()->with('success', "Data dan role pengguna {$user->name} berhasil diperbarui!");
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->back()->with('success', "Akun pengguna {$userName} berhasil dihapus!");
    }

    public function roleRequest()
    {
        $roleRequests = RoleRequest::with(['user', 'requestedDivisi', 'requestedJabatan'])
            ->orderByRaw("CASE WHEN status = 'Pending' THEN 1 ELSE 2 END")
            ->latest()
            ->paginate(15);

        return view('admin.role_request', compact('roleRequests'));
    }

    public function approveRoleRequest(RoleRequest $roleRequest)
    {
        if ($roleRequest->status !== 'Pending') {
            return redirect()->back()->with('error', 'Permintaan role ini sudah diproses sebelumnya.');
        }

        $user = $roleRequest->user;

        // Assign Spatie Role
        if ($roleRequest->requested_role && !$user->hasRole($roleRequest->requested_role)) {
            $user->assignRole($roleRequest->requested_role);
        }

        // Update User Divisi & Jabatan
        if ($roleRequest->requested_divisi_id) {
            $user->divisi_id = $roleRequest->requested_divisi_id;
        }
        if ($roleRequest->requested_jabatan_id) {
            $user->jabatan_id = $roleRequest->requested_jabatan_id;
        }
        $user->save();

        // Jika Jabatan Koordinator, update koordinator_divisi_nim pada divisi
        if ($user->jabatan && ($user->jabatan->nama_jabatan === 'Koordinator' || $roleRequest->requested_role === 'Koordinator')) {
            $divisi = Divisi::find($roleRequest->requested_divisi_id);
            if ($divisi) {
                $divisi->koordinator_divisi_nim = $user->nim;
                $divisi->save();
            }
        }

        $roleRequest->update([
            'status' => 'Approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->back()->with('success', "Pengajuan role/jabatan untuk {$user->name} berhasil disetujui!");
    }

    public function rejectRoleRequest(Request $request, RoleRequest $roleRequest)
    {
        if ($roleRequest->status !== 'Pending') {
            return redirect()->back()->with('error', 'Permintaan role ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $roleRequest->update([
            'status' => 'Rejected',
            'admin_note' => $request->input('note'),
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->back()->with('success', "Pengajuan role/jabatan untuk {$roleRequest->user->name} telah ditolak.");
    }
}
