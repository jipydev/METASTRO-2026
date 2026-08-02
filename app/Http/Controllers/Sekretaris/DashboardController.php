<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Memanggil file view: resources/views/sekretaris/dashboard/index.blade.php
        return view('sekretaris.dashboard.index');
    }
}