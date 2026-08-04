<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\QrCodeService;

class PresensiController extends Controller
{
    public function index(QrCodeService $qrService)
    {
        $user = Auth::user();
        $user->load('divisi');

        // Generate QR if not exists yet
        $qrUrl = $qrService->getQrUrl($user);
        if (!$qrUrl) {
            $qrService->generateForUser($user);
            $qrUrl = $qrService->getQrUrl($user);
        }

        return view('kegiatan.QR', [
            'user' => $user,
            'qrUrl' => $qrUrl,
        ]);
    }

    public function lihat()
    {
        return view('kegiatan.lihat');
    }

    public function listPanitia()
    {
        return view('kegiatan.listPanitia');
    }
}
