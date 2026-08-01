<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index()
    {
        return view('dashboard.presensi.index');
    }

    public function lihat()
    {
        return view('dashboard.presensi.lihat');
    }
}
