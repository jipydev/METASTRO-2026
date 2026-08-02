<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SekretarisController extends Controller
{
    function index() {
        return view('sekretaris.dashboard.index');
    }
}
