<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $data = [
            'kegiatans' => Kegiatan::where('jenis', 'pelaksanaan')
                ->orderBy('tanggal_mulai', 'asc')
                ->orderBy('waktu_mulai', 'asc')
                ->get(),
            'title' => 'Home',
        ];

        return view('index', $data);
    }
}
