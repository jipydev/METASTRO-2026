<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SekretarisController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard'
        ];

        return view('sekretaris.dashboard.index', $data);
    }
}
