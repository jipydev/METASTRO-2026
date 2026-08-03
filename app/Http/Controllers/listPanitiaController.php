<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Pastikan Request di-import

class PresensiController extends Controller
{
    // Tambahkan parameter Request $request
    public function listPanitia(Request $request) 
    {
        // 1. Tangkap parameter 'status' dari URL
        $statusFilter = $request->query('status');

        // 2. Data Master (Ganti dengan query database jika ada)
        $dataPanitia = [
            ['nama' => 'Azmil Ramadhan', 'divisi' => 'Chiper', 'jam_tap' => '07:45', 'tanggal' => 'Jumat, 31 Juli 2026', 'status' => 'Hadir'],
            ['nama' => 'Helmy Fadlurahman', 'divisi' => 'Chiper', 'jam_tap' => '-', 'tanggal' => 'Jumat, 31 Juli 2026', 'status' => 'Alpha'],
            ['nama' => 'Jahdan Pandita', 'divisi' => 'Chiper', 'jam_tap' => '08:15', 'tanggal' => 'Jumat, 31 Juli 2026', 'status' => 'Telat'],
            ['nama' => 'Rhesiana Putri Dewi', 'divisi' => 'Dokumenter', 'jam_tap' => '08:10', 'tanggal' => 'Jumat, 31 Juli 2026', 'status' => 'Telat'],
            ['nama' => 'Nayla Putri Asfiya', 'divisi' => 'Dokumenter', 'jam_tap' => '07:50', 'tanggal' => 'Jumat, 31 Juli 2026', 'status' => 'Hadir'],
        ];

        // 3. Filter data berdasarkan status
        $panitia = collect($dataPanitia);
        if ($statusFilter) {
            $panitia = $panitia->where('status', $statusFilter);
        }

        // 4. MENGIRIM VARIABEL KE VIEW
        // Pastikan 'statusFilter' ikut disertakan di dalam array ini atau menggunakan compact()
        return view('kegiatan.listPanitia', [
            'panitia' => $panitia,
            'statusFilter' => $statusFilter // Baris ini yang akan menyelesaikan error Anda
        ]);
    }
}