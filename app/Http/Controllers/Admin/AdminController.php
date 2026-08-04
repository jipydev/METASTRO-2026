<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }
    public function roleRequest()
    {
        return view('dashboard.admin.role_request');
    }
}