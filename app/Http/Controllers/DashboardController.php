<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'title' => "Dashboard"
        ];

        $rabes = [
            'title' => "Rabes 1"
        ];

        return view('dashboard.index', compact('data', 'rabes'));
    }


}
