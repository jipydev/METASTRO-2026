<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Timeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\QrCodeService;

class PresensiController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Presensi',
            'timelines' => Timeline::orderBy('tanggal_mulai', 'asc')->get(),
        ];

        return view('dashboard.presensi.index', $data);
    }

    public function show(Request $request, string $timeline_slug)
    {
        $timeline = Timeline::where('slug', $timeline_slug)->firstOrFail();

        $allowedStatuses = ['Hadir', 'Izin', 'Sakit', 'Alpha'];
        $status = $request->query('status');
        $search = trim((string) $request->query('q', ''));

        $query = Presensi::query()
            ->where('timeline_id', $timeline->id)
            ->with(['panitia.divisi', 'panitia.jabatan']);

        if ($status && in_array($status, $allowedStatuses, true)) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->whereHas('panitia', function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhereHas('divisi', fn($divisiQuery) => $divisiQuery->where('nama_divisi', 'like', "%{$search}%"))
                    ->orWhereHas('jabatan', fn($jabatanQuery) => $jabatanQuery->where('nama_jabatan', 'like', "%{$search}%"));
            });
        }

        $presensis = $query
            ->orderByDesc('waktu_presensi')
            ->paginate(25)
            ->appends($request->query());

        return view('dashboard.presensi.show', [
            'title' => 'Presensi',
            'timeline' => $timeline,
            'presensis' => $presensis,
            'statusFilter' => $status,
            'q' => $search,
        ]);
    }

    public function qr(QrCodeService $qrService)
    {
        $user = Auth::user();
        $user->load('divisi');

        // Generate QR if not exists yet
        $qrUrl = $qrService->getQrUrl($user);
        if (!$qrUrl) {
            $qrService->generateForUser($user);
            $qrUrl = $qrService->getQrUrl($user);
        }

        $data = [
            'title' => 'QR Code',
            'user' => $user,
            'qrUrl' => $qrService->getQrUrl($user)
        ];

        return view('dashboard.presensi.qr', $data);
    }

    public function scan(QrCodeService $qrService)
    {
        $user = Auth::user();
        $user->load('divisi');

        // Generate QR if not exists yet
        $qrUrl = $qrService->getQrUrl($user);
        if (!$qrUrl) {
            $qrService->generateForUser($user);
            $qrUrl = $qrService->getQrUrl($user);
        }

        return view('kegiatan.QR', [
            'user' => $user,
            'qrUrl' => $qrUrl,
        ]);
    }

    public function listPanitia()
    {
        return view('kegiatan.listPanitia');
    }
}
