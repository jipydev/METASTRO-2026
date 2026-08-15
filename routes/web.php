<?php

use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\ScanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InitialSetupController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\NotulensiController;
use App\Http\Controllers\PengajuanIzinController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('index');
})->name('home');

/*
|--------------------------------------------------------------------------
| Onboarding / First Time Setup
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/initial-setup', [InitialSetupController::class, 'index'])->name('initial-setup.index');
    Route::post('/initial-setup', [InitialSetupController::class, 'store'])->name('initial-setup.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // 1. Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // 3. QR Absensi & Presensi Pribadi
    Route::get('/qr-saya', [PresensiController::class, 'index'])->name('qr.show');
    Route::get('/presensi/riwayat', [PresensiController::class, 'history'])->name('presensi.history');

    // 4. Scanner Kamera, Toggle Sesi, & Monitoring Kehadiran
    Route::get('/presensi/scan', [PresensiController::class, 'scan'])->name('presensi.scan');
    Route::get('/presensi/monitoring', [PresensiController::class, 'monitoring'])->name('presensi.monitoring');
    Route::patch('/kegiatan/{kegiatan}/toggle-absen', [PresensiController::class, 'toggleAbsen'])->name('presensi.toggle');

    // 5. Alur Pengajuan Izin
    Route::prefix('izin')->name('pengajuan-izin.')->group(function () {
        Route::get('/', [PengajuanIzinController::class, 'index'])->name('index');
        Route::get('/buat', [PengajuanIzinController::class, 'create'])->name('create');
        Route::post('/', [PengajuanIzinController::class, 'store'])->name('store');
        Route::get('/review', [PengajuanIzinController::class, 'reviewIndex'])->name('review');
        Route::post('/{pengajuanIzin}/approve', [PengajuanIzinController::class, 'approve'])->name('approve');
        Route::post('/{pengajuanIzin}/reject', [PengajuanIzinController::class, 'reject'])->name('reject');
    });

    // 6. Timeline & Jadwal Kegiatan
    Route::resource('kegiatan', KegiatanController::class)->except(['create', 'show', 'edit']);

    // 7. Pengumuman
    Route::resource('pengumuman', PengumumanController::class)->only(['store', 'update', 'destroy', 'show']);

    // 8. Notulensi Rapat
    Route::post('/notulensi', [NotulensiController::class, 'store'])->name('notulensi.store');
    Route::delete('/notulensi/{notulensi}', [NotulensiController::class, 'destroy'])->name('notulensi.destroy');
    Route::get('/notulensi/{notulensi}/view', [NotulensiController::class, 'viewPdf'])->name('notulensi.view');

    // 9. Endpoint Internal Ajax Scanner
    Route::prefix('api/scan')->name('api.scan.')->group(function () {
        Route::post('/lookup', [ScanController::class, 'lookup'])->name('lookup');
        Route::post('/record', [ScanController::class, 'recordAttendance'])->name('record');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Kelola Pengguna (Users CRUD murni)
    Route::resource('users', UserController::class);

    // QR Code Management
    Route::get('/qr', [QrCodeController::class, 'index'])->name('qr.index');
    Route::get('/qr/{user}', [QrCodeController::class, 'show'])->name('qr.show');
    Route::post('/qr/{user}/regenerate', [QrCodeController::class, 'regenerate'])->name('qr.regenerate');
    Route::post('/qr/regenerate-all', [QrCodeController::class, 'regenerateAll'])->name('qr.regenerate-all');
});

require __DIR__ . '/auth.php';
