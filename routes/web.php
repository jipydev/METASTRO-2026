<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\SekretarisController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    Route::get('/dashboard/sekretaris', [SekretarisController::class, 'index'])->name('dashboard.sekretaris');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| SEKRE, RANGER, ADMIN, TAMBAHIN (KETUPLAK, WAKEPLAK, KOORDINATOR)
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth',
    'verified',
    'role:Admin|Ranger|Sekretaris'
])->group(function () {

    Route::get('/dashboard/presensi/lihat', [PresensiController::class, 'lihat'])
        ->name('dashboard.presensi.lihat');
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

    Route::get('/dashboard/presensi', [PresensiController::class, 'index'])
        ->name('dashboard.presensi');
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
    Route::get('/role-request', [AdminController::class, 'roleRequest'])
        ->name('admin.role-request');
});

require __DIR__ . '/auth.php';
