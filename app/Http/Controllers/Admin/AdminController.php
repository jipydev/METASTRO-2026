<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\RoleRequest;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return redirect()->route('dashboard');
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
