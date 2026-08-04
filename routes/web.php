<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Api\ScanController;
use App\Http\Controllers\ListPanitiaController;
use App\Http\Controllers\PresensiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengumumanController;

Route::get('/', function () {
    return view('dashboard.landingPage');
});



/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});


/*
|--------------------------------------------------------------------------
| Pengumuman
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
    'role:Admin|Sekretaris'
])->prefix('pengumuman')
  ->name('pengumuman.')
  ->group(function () {

    Route::get('/{pengumuman}', [PengumumanController::class,'show'])
        ->name('show');

    Route::post('/', [PengumumanController::class,'store'])
        ->name('store');

    Route::put('/{pengumuman}', [PengumumanController::class,'update'])
        ->name('update');

    Route::delete('/{pengumuman}', [PengumumanController::class,'destroy'])
        ->name('destroy');
});


/*
|--------------------------------------------------------------------------
| Scan
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth',
    'verified',
    'role:Admin|Sekretaris'
])->group(function () {
    Route::get('/scan', function () {
        return view('kegiatan.scan');
    })->name('scan');
});


/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| Lihat List Panitia
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth',
    'verified',
    'role:Admin|Ranger|Sekretaris'
])->group(function () {

    Route::get('/lihat', [PresensiController::class, 'lihat'])
        ->name('kegiatan.lihat');
});


//list panitia
Route::middleware([
    'auth',
    'verified',
    'role:Admin|Ranger|Sekretaris'
])->group(function () {
    Route::get('/lihat/list', [ListPanitiaController::class, 'index'])
        ->name('kegiatan.ListPanitia');
});



/*
|--------------------------------------------------------------------------
| SELURUH PANITIA
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth',
    'verified',
    'role:Admin|Panitia|Ranger|Sekretaris|Pengawas'
])->group(function () {

    Route::get('/qr', [PresensiController::class, 'index'])
        ->name('kegiatan.QR');
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
    'role:Admin'
])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])
        ->name('admin.dashboard');
    Route::get('/admin/role-request', [AdminController::class, 'roleRequest'])
        ->name('admin.role-request');

    // QR Code Management
    Route::get('/admin/users', [QrCodeController::class, 'index'])
        ->name('admin.users');
    Route::post('/admin/users/{user}/regenerate-qr', [QrCodeController::class, 'regenerate'])
        ->name('admin.users.regenerate-qr');
    Route::post('/admin/users/regenerate-all-qr', [QrCodeController::class, 'regenerateAll'])
        ->name('admin.users.regenerate-all-qr');
});


/*
|--------------------------------------------------------------------------
| API Scan (web routes with CSRF protection)
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth',
    'verified',
    'role:Admin|Sekretaris'
])->group(function () {
    Route::post('/scan/lookup', [ScanController::class, 'lookup'])
        ->name('scan.lookup');
    Route::post('/scan/attendance', [ScanController::class, 'recordAttendance'])
        ->name('scan.attendance');
});




require __DIR__ . '/auth.php';
