<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RangerController extends Controller
{
    public function index() {
        $data = [
            'title' => 'Dashboard'
        ];

        return view('ranger.dashboard.index', $data);
    }
}
