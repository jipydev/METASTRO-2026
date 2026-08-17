<?php

namespace App\Http\Controllers;

use App\Exceptions\AttendanceAlreadyRecordedException;
use App\Http\Requests\ImportPresensiRequest;
use App\Http\Requests\StoreManualPresensiRequest;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\User;
use App\Services\AttendanceImportService;
use App\Services\AttendanceRecorder;
use App\Services\NotificationDispatcher;
use App\Services\QrCodeService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PresensiController extends Controller
{
    public function __construct(
        private NotificationDispatcher $notifications,
        private AttendanceRecorder $recorder,
        private AttendanceImportService $importer,
    ) {}

    /**
     * Rekap matriks presensi per kegiatan.
     */
    public function monitoring(Request $request): View
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (! $currentUser->canViewPanitiaList()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat rekap kehadiran.');
        }

        $kegiatans = Kegiatan::orderBy('tanggal', 'desc')->limit(25)->get();
        $selectedKegiatanId = $request->query('kegiatan_id', $kegiatans->first()?->id);
        $selectedKegiatan = $kegiatans->firstWhere('id', $selectedKegiatanId);

        $statusFilter = $request->query('status');
        $divisiFilter = $request->query('divisi_id');
        $search = trim((string) $request->query('search', ''));
        $sort = match ($request->query('sort')) {
            'divisi' => 'divisi',
            'nama' => 'nama',
            'status' => 'status',
            default => 'waktu',
        };

        $usersData = [];
        $hadirCount = 0;
        $terlambatCount = 0;
        $izinCount = 0;
        $sakitCount = 0;
        $belumAbsenCount = 0;
        $belumLabel = 'Belum Absen';

        if ($selectedKegiatan) {
            $users = User::where('status', true)
                ->whereNotNull('divisi_id')
                ->with(['divisi', 'jabatan'])
                ->when($divisiFilter, fn ($q) => $q->where('divisi_id', $divisiFilter))
                ->get();

            $presensiMap = Presensi::with([
                'scanner.divisi',
                'pengajuanIzin.reviewerRanger.divisi',
            ])
                ->where('kegiatan_id', $selectedKegiatan->id)
                ->get()
                ->keyBy('user_id');

            $tanggalStr = Carbon::parse($selectedKegiatan->tanggal)->format('Y-m-d');
            $waktuSelesaiKegiatan = $selectedKegiatan->waktu_selesai
                ? Carbon::parse($tanggalStr.' '.$selectedKegiatan->waktu_selesai)
                : Carbon::parse($tanggalStr.' '.$selectedKegiatan->waktu_mulai)->addHours(3);

            $isKegiatanPassed = now()->greaterThan($waktuSelesaiKegiatan);
            $belumLabel = $isKegiatanPassed ? 'Alpa' : 'Belum Absen';

            foreach ($users as $user) {
                $presensi = $presensiMap->get($user->id);

                if ($presensi) {
                    $presensi->setRelation('kegiatan', $selectedKegiatan);
                    $status = $presensi->status_tampilan;
                    $waktuSumber = $presensi->isIzinAtauSakit()
                        ? ($presensi->pengajuanIzin?->created_at ?? $presensi->jam_tap)
                        : $presensi->jam_tap;
                    $waktuPresensi = optional($waktuSumber)->format('H:i') ?? '-';
                } else {
                    $status = $isKegiatanPassed ? 'alpa' : 'belum_hadir';
                    $waktuPresensi = '-';
                }

                match ($status) {
                    'hadir' => $hadirCount++,
                    'terlambat' => $terlambatCount++,
                    'izin' => $izinCount++,
                    'sakit' => $sakitCount++,
                    default => $belumAbsenCount++,
                };

                if ($statusFilter && $status !== $statusFilter) {
                    continue;
                }

                $scannerNama = null;
                $scannerDivisi = null;
                $izinReviewer = null;
                $izinReviewerDivisi = null;
                $viaIzin = false;

                if ($presensi?->isIzinAtauSakit()) {
                    $viaIzin = true;
                    $ranger = $presensi->pengajuanIzin?->reviewerRanger;
                    $izinReviewer = $ranger?->nama;
                    $izinReviewerDivisi = $ranger?->divisi?->nama ?? 'Ranger';
                } elseif ($presensi?->scanner) {
                    $scannerNama = $presensi->scanner->nama;
                    $scannerDivisi = $presensi->scanner->divisi?->nama ?? 'Umum';
                }

                $divisiJabatan = $user->formatted_divisi_jabatan;

                if ($search !== '') {
                    $haystack = mb_strtolower(implode(' ', array_filter([
                        $user->nama,
                        $user->nim,
                        $divisiJabatan,
                        $user->divisi?->nama,
                        $user->jabatan?->nama,
                        $user->isAdmin() ? 'admin' : 'panitia',
                        $status,
                        str_replace('_', ' ', $status),
                        $scannerNama,
                        $scannerDivisi,
                        $viaIzin ? 'izin disetujui' : null,
                        $izinReviewer,
                        $izinReviewerDivisi,
                    ])));

                    if (! str_contains($haystack, mb_strtolower($search))) {
                        continue;
                    }
                }

                $usersData[] = [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'nim' => $user->nim,
                    'foto' => $user->foto
                        ? asset('storage/'.$user->foto)
                        : 'https://ui-avatars.com/api/?size=128&background=fe5a1d&color=fff&name='.urlencode($user->nama),
                    'divisi_jabatan' => $divisiJabatan,
                    'status' => $status,
                    'waktu_presensi' => $waktuPresensi,
                    'jam_tap' => $presensi?->isIzinAtauSakit()
                        ? ($presensi->pengajuanIzin?->created_at ?? $presensi->jam_tap)
                        : $presensi?->jam_tap,
                    'via_izin' => $viaIzin,
                    'izin_reviewer' => $izinReviewer,
                    'izin_reviewer_divisi' => $izinReviewerDivisi,
                    'role_order' => $user->isAdmin() ? 1 : 2,
                    'divisi_id' => $user->divisi_id,
                    'divisi_nama' => $user->divisi?->nama ?? '',
                    'jabatan_id' => $user->jabatan_id,
                    'scanner_nama' => $scannerNama,
                    'scanner_divisi' => $scannerDivisi,
                ];
            }

            $usersData = collect($usersData)
                ->sort(function (array $a, array $b) use ($sort) {
                    if ($sort === 'divisi') {
                        return [
                            mb_strtolower($a['divisi_nama']),
                            $a['jabatan_id'] ?? 9999,
                            $a['nama'],
                        ] <=> [
                            mb_strtolower($b['divisi_nama']),
                            $b['jabatan_id'] ?? 9999,
                            $b['nama'],
                        ];
                    }

                    if ($sort === 'nama') {
                        return $a['nama'] <=> $b['nama'];
                    }

                    if ($sort === 'status') {
                        $statusOrder = [
                            'hadir' => 1,
                            'terlambat' => 2,
                            'izin' => 3,
                            'sakit' => 4,
                            'alpa' => 5,
                            'belum_hadir' => 6,
                        ];

                        return [
                            $statusOrder[$a['status']] ?? 99,
                            $a['nama'],
                        ] <=> [
                            $statusOrder[$b['status']] ?? 99,
                            $b['nama'],
                        ];
                    }

                    $aTime = $a['jam_tap']?->timestamp ?? 0;
                    $bTime = $b['jam_tap']?->timestamp ?? 0;

                    if ($aTime === $bTime) {
                        return $a['nama'] <=> $b['nama'];
                    }

                    if ($aTime === 0) {
                        return 1;
                    }

                    if ($bTime === 0) {
                        return -1;
                    }

                    return $bTime <=> $aTime;
                })
                ->values();
        }

        $perPage = 15;
        $page = max(1, (int) $request->integer('page', 1));
        $collection = collect($usersData);
        $usersData = new LengthAwarePaginator(
            $collection->forPage($page, $perPage)->values(),
            $collection->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $panitiaOptions = $currentUser->canScanPresensi()
            ? User::query()
                ->where('status', true)
                ->whereNotNull('divisi_id')
                ->with('divisi')
                ->orderBy('nama')
                ->get(['id', 'nama', 'nim', 'divisi_id'])
            : collect();

        return view('presensi.monitoring', [
            'title' => 'Monitoring Kehadiran',
            'kegiatans' => $kegiatans,
            'selectedKegiatan' => $selectedKegiatan,
            'usersData' => $usersData,
            'statusFilter' => $statusFilter,
            'divisiFilter' => $divisiFilter,
            'search' => $search,
            'sort' => $sort,
            'panitiaOptions' => $panitiaOptions,
            'hadirCount' => $hadirCount,
            'terlambatCount' => $terlambatCount,
            'izinCount' => $izinCount,
            'sakitCount' => $sakitCount,
            'belumAbsenCount' => $belumAbsenCount,
            'belumLabel' => $belumLabel,
        ]);
    }

    /**
     * Catat kehadiran manual (satu per satu) oleh Admin / Archivist.
     */
    public function store(StoreManualPresensiRequest $request): RedirectResponse
    {
        $kegiatan = Kegiatan::query()->findOrFail($request->integer('kegiatan_id'));
        $peserta = User::query()->findOrFail($request->integer('user_id'));
        $petugas = $request->user();

        if (! $petugas instanceof User) {
            abort(403);
        }

        if (! $peserta->status) {
            return back()->with('error', 'Akun panitia tersebut sedang dinonaktifkan.');
        }

        $jamTap = $request->filled('jam_tap')
            ? Carbon::parse($kegiatan->tanggal)->setTimeFromTimeString($request->string('jam_tap')->toString())
            : $kegiatan->defaultJamTap();

        try {
            $this->recorder->record($peserta, $kegiatan, $petugas, 'manual', $jamTap);
        } catch (AttendanceAlreadyRecordedException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('presensi.monitoring', ['kegiatan_id' => $kegiatan->id])
            ->with('success', "Presensi {$peserta->nama} berhasil dicatat.");
    }

    /**
     * Impor kehadiran dari CSV / Excel hasil absen kertas.
     */
    public function import(ImportPresensiRequest $request): RedirectResponse
    {
        $kegiatan = Kegiatan::query()->findOrFail($request->integer('kegiatan_id'));
        $petugas = $request->user();
        $file = $request->file('file');

        if (! $petugas instanceof User || $file === null) {
            abort(403);
        }

        try {
            $result = $this->importer->import($file, $kegiatan, $petugas);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        $redirect = redirect()->route('presensi.monitoring', ['kegiatan_id' => $kegiatan->id]);

        if ($result->imported === 0) {
            return $redirect->with('error', $result->message());
        }

        return $redirect->with('success', $result->message());
    }

    /**
     * Unduh template CSV untuk impor presensi.
     */
    public function template(): StreamedResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->canScanPresensi()) {
            abort(403, 'Akses impor presensi hanya untuk Admin dan Divisi Archivist.');
        }

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['nim', 'nama', 'status', 'waktu']);
            fputcsv($handle, ['2508394', 'Nama Panitia', 'hadir', '08:15']);
            fclose($handle);
        }, 'template-presensi.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Menampilkan halaman QR Code Absensi milik user yang sedang login
     * dengan regenerasi otomatis jika token kedaluwarsa atau belum ada.
     */
    public function index(QrCodeService $qrService): View
    {
        /** @var User $user */
        $user = Auth::user();
        $user->load(['divisi', 'jabatan']);

        // OTOMATIS GENERATE DI BELAKANG LAYAR
        // Jika token tidak ada atau bukan dibuat hari ini, buat baru
        if (! $user->qr_token || ! $user->qr_updated_at?->isToday()) {
            $user->update([
                'qr_token' => (string) Str::uuid(),
                'qr_updated_at' => now(),
            ]);

            // Generate ulang file fisik
            $qrService->generateForUser($user);
        }

        $qrUrl = $qrService->getQrUrl($user);
        if (! $qrUrl) {
            $qrService->generateForUser($user);
            $qrUrl = $qrService->getQrUrl($user);
        }

        return view('presensi.qr', [
            'title' => 'QR Absensi Saya',
            'user' => $user,
            'qrUrl' => $qrUrl,
            'qrGeneratedAt' => $user->qr_updated_at,
        ]);
    }

    /**
     * Menampilkan halaman Scanner Barcode untuk Panitia.
     */
    public function scan(): View
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->canScanPresensi()) {
            abort(403, 'Akses scan presensi hanya untuk Admin dan Divisi Archivist.');
        }

        // Cek kegiatan yang presensinya sedang aktif berdasarkan rentang waktu saat ini
        $kegiatanAktif = Kegiatan::where('presensi_mulai', '<=', Carbon::now())
            ->where('presensi_selesai', '>=', Carbon::now())
            ->orderBy('tanggal', 'asc')
            ->first()
            ?? Kegiatan::whereDate('tanggal', Carbon::today())->first();

        return view('presensi.scan', [
            'title' => 'Scanner Presensi',
            'kegiatanAktif' => $kegiatanAktif,
        ]);
    }

    /**
     * Mengubah status sesi absensi (buka / tutup) secara cepat berbasis waktu.
     */
    public function toggleAbsen(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->canTogglePresensi()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengatur sesi presensi.');
        }

        $validated = $request->validate(
            ['status_presensi' => 'required|in:buka,tutup'],
            [
                'status_presensi.required' => 'Status presensi wajib dipilih.',
                'status_presensi.in' => 'Status presensi harus Buka atau Tutup.',
            ]
        );

        if ($validated['status_presensi'] === 'buka') {
            // Tentukan waktu selesai presensi berdasarkan waktu selesai kegiatan
            $tanggalStr = Carbon::parse($kegiatan->tanggal)->format('Y-m-d');

            if ($kegiatan->waktu_selesai) {
                $waktuSelesaiPresensi = Carbon::parse($tanggalStr.' '.$kegiatan->waktu_selesai);
            } else {
                // Fallback jika waktu selesai kegiatan kosong (default +3 jam dari waktu mulai kegiatan)
                $waktuSelesaiPresensi = Carbon::parse($tanggalStr.' '.$kegiatan->waktu_mulai)->addHours(3);
            }

            // Jika waktu selesai kegiatan ternyata sudah lewat atau mendahului waktu sekarang, set minimal 1 jam dari sekarang
            if ($waktuSelesaiPresensi->lessThanOrEqualTo(Carbon::now())) {
                $waktuSelesaiPresensi = Carbon::now()->addHour();
            }

            $kegiatan->update([
                'presensi_mulai' => Carbon::now(),
                'presensi_selesai' => $waktuSelesaiPresensi,
            ]);
            $statusLabel = 'DIBUKA';
            $this->notifications->presensiOpened($kegiatan, $user->id);
        } else {
            // Jika ditutup paksa, set presensi_selesai ke waktu sekarang
            $kegiatan->update([
                'presensi_selesai' => Carbon::now(),
            ]);
            $statusLabel = 'DITUTUP';
        }

        return back()->with('success', "Sesi presensi untuk '{$kegiatan->nama}' berhasil {$statusLabel}.");
    }

    /**
     * Riwayat presensi milik user yang sedang login.
     */
    public function history(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        $search = trim((string) $request->query('search', ''));
        $statusFilter = (string) $request->query('status', '');

        $records = Presensi::with(['kegiatan', 'scanner.divisi', 'pengajuanIzin.reviewerRanger.divisi'])
            ->where('user_id', $user->id)
            ->when($search !== '', function ($q) use ($search) {
                $q->whereHas('kegiatan', function ($kegiatanQuery) use ($search) {
                    $kegiatanQuery->where('nama', 'like', "%{$search}%");
                });
            })
            ->latest('jam_tap')
            ->get();

        if ($statusFilter !== '') {
            $records = $records->filter(
                fn (Presensi $presensi) => $presensi->status_tampilan === $statusFilter
            )->values();
        }

        $perPage = 15;
        $page = max(1, (int) $request->integer('page', 1));
        $presensis = new LengthAwarePaginator(
            $records->forPage($page, $perPage)->values(),
            $records->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('presensi.history', [
            'title' => 'Riwayat Presensi Saya',
            'presensis' => $presensis,
            'search' => $search,
            'statusFilter' => $statusFilter,
        ]);
    }
}
