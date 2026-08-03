<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\PresensiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
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
    Route::get('/lihat/list', [PresensiController::class, 'listPanitia'])
        ->name('kegiatan.listPanitia');
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

    Route::get('/admin/role-request', [AdminController::class, 'roleRequest'])
        ->name('admin.role-request');
});



 // require __DIR__.'/auth.php';
