<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index()
    {
        return view('kegiatan.QR');
    }

    public function lihat()
    {
        return view('kegiatan.lihat');
    }

    public function listPanitia()
    {
        return view('kegiatan.listPanitia');
    }
}
