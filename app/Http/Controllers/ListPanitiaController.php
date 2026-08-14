<?php

namespace App\Http\Controllers;

use App\Models\ListPanitia;
use App\Models\PengajuanIzin;
use App\Models\Rapat;
use App\Models\User;
use App\Models\Jabatan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ListPanitiaController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->query('status');
        $rapatId = $request->query('rapat_id');
        $jabatanFilter = $request->query('jabatan_id');

        $jabatans = Jabatan::all();

        // Ambil rapat untuk filter dropdown (maksimal 30 rapat terbaru)
        $rapats = Rapat::orderBy('tanggal', 'desc')->orderBy('jam', 'desc')->limit(30)->get();

        if ($rapats->isEmpty()) {
            return view('kegiatan.listPanitia', [
                'panitia' => [],
                'rapats' => $rapats,
                'jabatans' => $jabatans,
                'selectedRapat' => null,
                'statusFilter' => $statusFilter,
            ]);
        }

        // Tentukan rapat yang dipilih (default: Rapat terakhir/hari ini jika tidak ada query param)
        if ($rapatId) {
            $selectedRapat = $rapats->firstWhere('id', $rapatId);
        } else {
            $selectedRapat = Rapat::whereDate('tanggal', Carbon::today())->first() ?? $rapats->first();
        }

        if (! $selectedRapat) {
            $selectedRapat = $rapats->first();
        }

        // Ambil data absen yang sudah tersimpan untuk rapat ini (keyed by user_id for O(1) lookup)
        $absenRecords = ListPanitia::with(['user.divisi', 'scanner'])
            ->where('rapat_id', $selectedRapat->id)
            ->get()
            ->keyBy('user_id');

        // Ambil data pengajuan izin yang disetujui pada tanggal rapat ini
        $izinRecords = PengajuanIzin::whereDate('tanggal_izin', $selectedRapat->tanggal)
            ->where('status', 'Approved')
            ->get()
            ->keyBy('user_id');

        $waktuRapat = Carbon::parse($selectedRapat->tanggal.' '.$selectedRapat->jam);
        $batasWaktuAlpha = $waktuRapat->copy()->addMinutes(15);
        $sekarang = Carbon::now();

        $semuaPanitia = User::where('status_aktif', true)
            ->select('id', 'name', 'nim', 'divisi_id', 'jabatan_id')
            ->with(['divisi:id,nama_divisi', 'roles:id,name', 'jabatan:id,nama_jabatan'])
            ->get();

        $panitiaData = [];

        foreach ($semuaPanitia as $panitia) {
            $record = $absenRecords->get($panitia->id);
            $izinRecord = $izinRecords->get($panitia->id);

            if ($record) {
                // Sudah scan
                $panitiaData[] = [
                    'nama' => $panitia->name,
                    'divisi' => $panitia->divisi?->nama_divisi ?? '-',
                    'jabatan' => $panitia->jabatan?->nama_jabatan ?? '-',
                    'jam_tap' => Carbon::parse($record->jam_tap)->format('H:i'),
                    'tanggal' => Carbon::parse($selectedRapat->tanggal)->translatedFormat('d F Y'),
                    'status' => $record->status,
                    'scanned_by' => $record->scanner?->name ?? 'Sistem',
                    'alasan_izin' => null,
                ];
            } elseif ($izinRecord) {
                // Ada pengajuan izin yang disetujui
                $panitiaData[] = [
                    'nama' => $panitia->name,
                    'divisi' => $panitia->divisi?->nama_divisi ?? '-',
                    'jabatan' => $panitia->jabatan?->nama_jabatan ?? '-',
                    'jam_tap' => '-',
                    'tanggal' => Carbon::parse($selectedRapat->tanggal)->translatedFormat('d F Y'),
                    'status' => $izinRecord->jenis_izin, // 'Sakit' atau 'Izin'
                    'scanned_by' => 'Approved (Izin)',
                    'alasan_izin' => $izinRecord->alasan,
                    'waktu_pengajuan' => $izinRecord->created_at ? Carbon::parse($izinRecord->created_at)->format('d/m H:i') : null,
                ];
            } else {
                // Belum scan dan tidak izin
                if ($sekarang->greaterThan($batasWaktuAlpha)) {
                    $panitiaData[] = [
                        'nama' => $panitia->name,
                        'divisi' => $panitia->divisi?->nama_divisi ?? '-',
                        'jabatan' => $panitia->jabatan?->nama_jabatan ?? '-',
                        'jam_tap' => '-',
                        'tanggal' => Carbon::parse($selectedRapat->tanggal)->translatedFormat('d F Y'),
                        'status' => 'Alpha',
                        'scanned_by' => '-',
                        'alasan_izin' => null,
                    ];
                } else {
                    $panitiaData[] = [
                        'nama' => $panitia->name,
                        'divisi' => $panitia->divisi?->nama_divisi ?? '-',
                        'jabatan' => $panitia->jabatan?->nama_jabatan ?? '-',
                        'jam_tap' => '-',
                        'tanggal' => Carbon::parse($selectedRapat->tanggal)->translatedFormat('d F Y'),
                        'status' => 'Tidak Hadir',
                        'scanned_by' => '-',
                        'alasan_izin' => null,
                    ];
                }
            }
        }

        // Terapkan Filter Status jika ada
        if ($statusFilter) {
            $panitiaData = array_filter($panitiaData, function ($item) use ($statusFilter) {
                return $item['status'] === $statusFilter;
            });
        }

        usort($panitiaData, function ($a, $b) {
            return strcmp($a['nama'], $b['nama']);
        });

        return view('kegiatan.listPanitia', [
            'panitia' => $panitiaData,
            'rapats' => $rapats,
            'jabatans' => $jabatans,
            'selectedRapat' => $selectedRapat,
            'statusFilter' => $statusFilter,
        ]);
    }
}
