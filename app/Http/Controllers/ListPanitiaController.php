<?php

namespace App\Http\Controllers;

use App\Models\ListPanitia;
use App\Models\PengajuanIzin;
use App\Models\Rapat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ListPanitiaController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->query('status');
        $rapatId = $request->query('rapat_id');

        // Ambil semua rapat untuk filter dropdown
        $rapats = Rapat::orderBy('tanggal', 'desc')->orderBy('jam', 'desc')->get();

        if ($rapats->isEmpty()) {
            return view('kegiatan.listPanitia', [
                'panitia' => [],
                'rapats' => $rapats,
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

        // Ambil data absen yang sudah tersimpan untuk rapat ini
        $absenRecords = ListPanitia::with(['user.divisi', 'scanner'])
            ->where('rapat_id', $selectedRapat->id)
            ->get();

        // Ambil data pengajuan izin yang disetujui pada tanggal rapat ini (termasuk izin Koordinator & Staff)
        $izinRecords = PengajuanIzin::whereDate('tanggal_izin', $selectedRapat->tanggal)
            ->where('status', 'Approved')
            ->get();

        $waktuRapat = Carbon::parse($selectedRapat->tanggal.' '.$selectedRapat->jam);
        $batasWaktuAlpha = $waktuRapat->copy()->addMinutes(15);
        $sekarang = Carbon::now();

        $semuaPanitia = User::with(['divisi', 'roles', 'jabatan'])->get();

        $panitiaData = [];

        foreach ($semuaPanitia as $panitia) {
            $record = $absenRecords->firstWhere('user_id', $panitia->id);
            $izinRecord = $izinRecords->firstWhere('user_id', $panitia->id);

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
            'selectedRapat' => $selectedRapat,
            'statusFilter' => $statusFilter,
        ]);
    }
}
