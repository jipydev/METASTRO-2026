<?php

use App\Http\Controllers\HukumanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\ScanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InitialSetupController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\NotificationController;
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
Route::middleware(['auth', 'verified', 'initial.setup'])->group(function () {
    Route::get('/initial-setup', [InitialSetupController::class, 'index'])->name('initial-setup.index');
    Route::post('/initial-setup', [InitialSetupController::class, 'store'])->name('initial-setup.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'initial.setup'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/notifikasi/baca-semua', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::resource('notifikasi', NotificationController::class)
        ->only(['show'])
        ->names(['show' => 'notifications.show']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::redirect('/presensi/qr', '/presensi');
    Route::get('/presensi/template', [PresensiController::class, 'template'])->name('presensi.template');
    Route::post('/presensi/import', [PresensiController::class, 'import'])->name('presensi.import');
    Route::resource('presensi', PresensiController::class)->only(['index', 'store']);
    Route::get('/presensi/riwayat', [PresensiController::class, 'history'])->name('presensi.history');
    Route::get('/presensi/scan', [PresensiController::class, 'scan'])->name('presensi.scan');
    Route::get('/presensi/monitoring', [PresensiController::class, 'monitoring'])->name('presensi.monitoring');
    Route::patch('/kegiatan/{kegiatan}/toggle-absen', [PresensiController::class, 'toggleAbsen'])->name('presensi.toggle');

    Route::redirect('/izin/riwayat', '/izin');
    Route::redirect('/izin/buat', '/izin/create');
    Route::get('/izin/review', [PengajuanIzinController::class, 'review'])->name('pengajuan-izin.review');
    Route::post('/izin/{pengajuanIzin}/approve', [PengajuanIzinController::class, 'approve'])->name('pengajuan-izin.approve');
    Route::post('/izin/{pengajuanIzin}/reject', [PengajuanIzinController::class, 'reject'])->name('pengajuan-izin.reject');
    Route::resource('izin', PengajuanIzinController::class)
        ->parameters(['izin' => 'pengajuanIzin'])
        ->names('pengajuan-izin')
        ->only(['index', 'create', 'store', 'destroy']);

    Route::resource('kegiatan', KegiatanController::class)->except(['create', 'show', 'edit']);

    Route::resource('pengumuman', PengumumanController::class)->except(['create', 'edit']);

    Route::resource('notulensi', NotulensiController::class)->except(['create', 'edit']);

    Route::prefix('hukuman')->name('hukuman.')->group(function () {
        Route::get('/', [HukumanController::class, 'index'])->name('index');
        Route::get('/kelola/{mode}', [HukumanController::class, 'kelola'])->where('mode', 'ranger|pengawas')->name('kelola');
        Route::get('/buat/{mode}', [HukumanController::class, 'create'])->where('mode', 'ranger|pengawas')->name('create');
        Route::post('/{mode}', [HukumanController::class, 'store'])->where('mode', 'ranger|pengawas')->name('store');
        Route::get('/{hukuman}', [HukumanController::class, 'show'])->whereNumber('hukuman')->name('show');
        Route::post('/{hukuman}/pembelaan', [HukumanController::class, 'submitPembelaan'])->whereNumber('hukuman')->name('pembelaan');
        Route::post('/{hukuman}/tugas', [HukumanController::class, 'submitTugas'])->whereNumber('hukuman')->name('tugas');
        Route::post('/{hukuman}/selesai', [HukumanController::class, 'complete'])->whereNumber('hukuman')->name('selesai');
    });

    Route::prefix('api/scan')->name('api.scan.')->group(function () {
        Route::post('/lookup', [ScanController::class, 'lookup'])->name('lookup');
        Route::post('/', [ScanController::class, 'store'])->name('store');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'initial.setup', 'can:admin-access'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class)->except(['show']);
    Route::patch('users/{user}/reset-qr', [UserController::class, 'resetQr'])->name('users.reset-qr');
    Route::patch('users-reset-all-qr', [UserController::class, 'resetAllQr'])->name('users.reset-all-qr');
});

require __DIR__.'/auth.php';
