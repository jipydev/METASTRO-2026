<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard_admin');
    }
    public function roleRequest()
    {
        return view('admin.role_request');
    }
}