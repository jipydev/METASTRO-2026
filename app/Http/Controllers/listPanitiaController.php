<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ListPanitia;

class ListPanitiaController extends Controller
{
    public function index(Request $request) 
    {
        $statusFilter = $request->query('status');

        $query = ListPanitia::query();

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $panitia = $query->get();
        
        return view('kegiatan.ListPanitia', [
            'panitia' => $panitia
        ]);
    }
}