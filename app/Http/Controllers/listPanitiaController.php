<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ListPanitia;
use App\Models\Rapat;
use App\Models\User;
use App\Models\Jabatan;
use Carbon\Carbon;

class ListPanitiaController extends Controller
{
    public function index(Request $request) 
    {
        $statusFilter = $request->query('status');
        $rapatId = $request->query('rapat_id');
        $jabatanFilter = $request->query('jabatan_id');

        $jabatans = Jabatan::all();

        // Ambil semua rapat untuk filter dropdown
        $rapats = Rapat::orderBy('tanggal', 'desc')->orderBy('jam', 'desc')->get();

        if ($rapats->isEmpty()) {
            return view('kegiatan.listPanitia', [
                'panitia' => [],
                'rapats' => $rapats,
                'jabatans' => $jabatans,
                'selectedRapat' => null,
                'statusFilter' => $statusFilter,
                'jabatanFilter' => $jabatanFilter
            ]);
        }

        // Tentukan rapat yang dipilih (default: Rapat terakhir/hari ini jika tidak ada query param)
        if ($rapatId) {
            $selectedRapat = $rapats->firstWhere('id', $rapatId);
        } else {
            $selectedRapat = Rapat::whereDate('tanggal', Carbon::today())->first() ?? $rapats->first();
        }

        if (!$selectedRapat) {
            $selectedRapat = $rapats->first();
        }

        // Ambil data absen yang sudah tersimpan untuk rapat ini
        $absenRecords = ListPanitia::with(['user.divisi', 'user.jabatan', 'scanner'])
                            ->where('rapat_id', $selectedRapat->id)
                            ->get();
        
        $absenUserIds = $absenRecords->pluck('user_id')->toArray();

        // -------------------------------------------------------------
        // LOGIKA ALPHA OTOMATIS
        // "alpha akan otomatis diberikan kepada panitia yang tidak absen 
        // terakhir pada 15 menit setelah rapat dimulai"
        // -------------------------------------------------------------
        $waktuRapat = Carbon::parse($selectedRapat->tanggal . ' ' . $selectedRapat->jam);
        $batasWaktuAlpha = $waktuRapat->copy()->addMinutes(15);
        $sekarang = Carbon::now();

        $semuaPanitia = User::role(['Panitia', 'Sekretaris'])->with(['divisi', 'jabatan'])->get();

        $panitiaData = [];

        foreach ($semuaPanitia as $panitia) {
            $record = $absenRecords->firstWhere('user_id', $panitia->id);

            if ($record) {
                // Sudah scan
                $panitiaData[] = [
                    'nama' => $panitia->name,
                    'divisi' => $panitia->divisi?->nama_divisi ?? '-',
                    'jabatan' => $panitia->jabatan?->nama_jabatan ?? '-',
                    'jabatan_id' => $panitia->jabatan_id,
                    'jam_tap' => Carbon::parse($record->jam_tap)->format('H:i'),
                    'tanggal' => Carbon::parse($selectedRapat->tanggal)->translatedFormat('d F Y'),
                    'status' => $record->status,
                    'scanned_by' => $record->scanner?->name ?? 'Sistem'
                ];
            } else {
                // Belum scan
                // Cek apakah sudah melebihi 15 menit setelah rapat dimulai
                if ($sekarang->greaterThan($batasWaktuAlpha)) {
                    $panitiaData[] = [
                        'nama' => $panitia->name,
                        'divisi' => $panitia->divisi?->nama_divisi ?? '-',
                        'jabatan' => $panitia->jabatan?->nama_jabatan ?? '-',
                        'jabatan_id' => $panitia->jabatan_id,
                        'jam_tap' => '-',
                        'tanggal' => Carbon::parse($selectedRapat->tanggal)->translatedFormat('d F Y'),
                        'status' => 'Alpha',
                        'scanned_by' => '-'
                    ];
                } else {
                    // Masih bisa absen, kita tampilkan sebagai Belum Hadir / Tidak Hadir
                    $panitiaData[] = [
                        'nama' => $panitia->name,
                        'divisi' => $panitia->divisi?->nama_divisi ?? '-',
                        'jabatan' => $panitia->jabatan?->nama_jabatan ?? '-',
                        'jabatan_id' => $panitia->jabatan_id,
                        'jam_tap' => '-',
                        'tanggal' => Carbon::parse($selectedRapat->tanggal)->translatedFormat('d F Y'),
                        'status' => 'Tidak Hadir', // Menunggu waktu
                        'scanned_by' => '-'
                    ];
                }
            }
        }

        // Terapkan Filter Status jika ada
        if ($statusFilter) {
            $panitiaData = array_filter($panitiaData, function($item) use ($statusFilter) {
                return $item['status'] === $statusFilter;
            });
        }
        
        // Terapkan Filter Jabatan jika ada
        if ($jabatanFilter) {
            $panitiaData = array_filter($panitiaData, function($item) use ($jabatanFilter) {
                return $item['jabatan_id'] == $jabatanFilter;
            });
        }
        
        // Sorting: Hadir duluan, Telat, lalu Alpha/Tidak Hadir. Atau biarkan sesuai nama.
        usort($panitiaData, function($a, $b) {
            return strcmp($a['nama'], $b['nama']);
        });

        return view('kegiatan.listPanitia', [
            'panitia' => $panitiaData,
            'rapats' => $rapats,
            'jabatans' => $jabatans,
            'selectedRapat' => $selectedRapat,
            'statusFilter' => $statusFilter,
            'jabatanFilter' => $jabatanFilter
        ]);
    }
}