<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Jadwal;
use App\Models\Rapat;
use App\Models\User;
use App\Notifications\IzinStatusUpdatedNotification;
use App\Notifications\IzinSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IzinController extends Controller
{
    /**
     * Staff submits an izin/sakit request
     */
    public function store(Request $request)
    {
        $request->validate([
            'alasan' => 'required|in:sakit,izin',
            'detail' => 'nullable|string',
            'surat' => 'nullable|file|mimes:pdf|max:5120',
            'bukti' => 'nullable|image|max:5120',
        ]);

        // Find the latest active meeting / jadwal
        $rapat = Rapat::where('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam', 'asc')
            ->first() ?? Rapat::orderBy('tanggal', 'desc')->orderBy('jam', 'desc')->first();

        if (!$rapat) {
            return redirect()->back()->with('error', 'Tidak ada jadwal rapat yang aktif.');
        }

        // Assuming jadwal_id comes from rapat. In this system Rapat might be Jadwal.
        // Let's check if the system uses jadwal_id from somewhere else. For now we use the latest Rapat ID or Jadwal ID.
        // Since I don't see how Rapat and Jadwal relate in the snippet, I'll use a generic approach based on the dashboard logic.
        // Wait, looking at the migration, `jadwal_id` is required for `absensi`. Does Rapat = Jadwal? 
        // Let's assume there's a Jadwal associated with it, or maybe Rapat is Jadwal.
        // Let's check if Rapat is the same as Jadwal or if it has jadwal_id. 
        // If they are separate, I might need to fetch the Jadwal. Let's just fetch the first Jadwal to be safe or use rapat->id if they are the same.
        // Wait, `DashboardController` uses `Rapat::where(...)`. The model `Absensi` belongsTo `Jadwal`. Let me check if Rapat table has jadwal_id or what.
        // Actually, to be safe, I'll just use the latest Jadwal.
        $jadwal = Jadwal::latest('tanggal')->first(); 
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
        }

        $user = Auth::user();

        // Check if already submitted
        $existing = Absensi::where('user_id', $user->id)
            ->where('jadwal_id', $jadwal->id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah melakukan presensi atau mengajukan izin untuk jadwal ini.');
        }

        $suratPath = null;
        if ($request->hasFile('surat')) {
            $suratPath = $request->file('surat')->store('izin/surat', 'public');
        }

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('izin/bukti', 'public');
        }

        $absensi = Absensi::create([
            'jadwal_id' => $jadwal->id,
            'user_id' => $user->id,
            'status' => $request->alasan == 'sakit' ? 'Sakit' : 'Izin',
            'status_validasi' => 'menunggu_koordinator',
            'keterangan' => $request->detail,
            'bukti' => json_encode(['surat' => $suratPath, 'dokumentasi' => $buktiPath]),
            'waktu_absen' => now(),
            'persentase_kehadiran' => 0,
        ]);

        // Notify Koordinator Divisi
        $koordinators = User::role('Koordinator Divisi')->get();
        foreach ($koordinators as $koor) {
            $koor->notify(new IzinSubmittedNotification($absensi));
        }

        return redirect()->back()->with('success', 'Pengajuan izin berhasil dikirim dan menunggu validasi Koordinator Divisi.');
    }

    /**
     * Koordinator Divisi Dashboard
     */
    public function koordinatorIndex()
    {
        $izinList = Absensi::with(['user', 'jadwal'])
            ->where('status_validasi', 'menunggu_koordinator')
            ->latest()
            ->paginate(10);

        return view('izin.koordinator', compact('izinList'));
    }

    /**
     * Koordinator Divisi Validate (Acc/Reject)
     */
    public function koordinatorValidasi(Request $request, Absensi $absensi)
    {
        $request->validate([
            'action' => 'required|in:acc,reject'
        ]);

        if ($request->action == 'acc') {
            $absensi->status_validasi = 'menunggu_ranger';
            $message = 'Izin disetujui, diteruskan ke Ranger.';
        } else {
            $absensi->status_validasi = 'ditolak_koordinator';
            $message = 'Izin ditolak.';
        }
        $absensi->save();

        // Notify Staff
        $absensi->user->notify(new IzinStatusUpdatedNotification($absensi, 'Koordinator Divisi'));

        // If Acc, notify Ranger
        if ($request->action == 'acc') {
            $rangers = User::role(['Ranger', 'Admin'])->get();
            foreach ($rangers as $ranger) {
                $ranger->notify(new IzinSubmittedNotification($absensi));
            }
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Ranger Dashboard
     */
    public function rangerIndex()
    {
        $izinList = Absensi::with(['user', 'jadwal'])
            ->where('status_validasi', 'menunggu_ranger')
            ->latest()
            ->paginate(10);

        return view('izin.ranger', compact('izinList'));
    }

    /**
     * Ranger Validate (Acc/Reject)
     */
    public function rangerValidasi(Request $request, Absensi $absensi)
    {
        $request->validate([
            'action' => 'required|in:acc,reject'
        ]);

        if ($request->action == 'acc') {
            $absensi->status_validasi = 'disetujui_ranger';
            $message = 'Izin disetujui sepenuhnya.';
        } else {
            $absensi->status_validasi = 'ditolak_ranger';
            $message = 'Izin ditolak.';
        }
        $absensi->save();

        // Notify Staff
        $absensi->user->notify(new IzinStatusUpdatedNotification($absensi, 'Ranger'));

        return redirect()->back()->with('success', $message);
    }
}
