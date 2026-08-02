<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\PresensiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

Route::get('/dashboard/presensi/lihat', [PresensiController::class,'lihat'])
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

Route::get('/dashboard/presensi', [PresensiController::class,'index'])
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

    Route::get('/admin/dashboard', [AdminController::class,'index'])
        ->name('admin.dashboard');
    Route::get('/admin/role-request', [AdminController::class,'roleRequest'])
        ->name('admin.role-request');

});

/*
|--------------------------------------------------------------------------
| SCAN
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
    'role:Admin|Sekretaris'
])->group(function () {

    Route::get('/scan', function () {
        return view('scan');
    })->name('scan');
});

require __DIR__.'/auth.php';
