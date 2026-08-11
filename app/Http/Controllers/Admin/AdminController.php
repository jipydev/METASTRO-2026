<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index()
    {
        return redirect()->route('dashboard');
    }

    public function roleRequest()
    {
        return view('dashboard.admin.role_request');
    }
}
