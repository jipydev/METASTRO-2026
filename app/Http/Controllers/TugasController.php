<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TugasController extends Controller
{
    public function index()
    {
        $data = [
            'title' => "Tugas",
            'tugases' => \App\Models\Tugas::all(),
        ];

        return view('tugas.index', $data);
    }
}
