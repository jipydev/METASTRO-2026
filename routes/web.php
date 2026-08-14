<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Api\ScanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ListPanitiaController;
use App\Http\Controllers\NotulensiController;
use App\Http\Controllers\PengajuanIzinController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\InitialSetupController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard.landingPage');
})->name('home');

/*
|--------------------------------------------------------------------------
| First Time Initial Setup (Onboarding)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/initial-setup', [InitialSetupController::class, 'index'])
        ->name('initial-setup.index');
    Route::post('/initial-setup', [InitialSetupController::class, 'store'])
        ->name('initial-setup.store');
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'initial.setup'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Pengajuan Izin Workflow
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->prefix('izin')->name('izin.')->group(function () {
    Route::get('/', function () { return redirect()->route('izin.history'); });
    Route::get('/create', [PengajuanIzinController::class, 'create'])->name('create');
    Route::post('/', [PengajuanIzinController::class, 'store'])->name('store');
    Route::get('/history', [PengajuanIzinController::class, 'history'])->name('history');

    // Review izin (Koordinator, Ranger, Admin)
    Route::get('/review', [PengajuanIzinController::class, 'reviewIndex'])->name('review');
    Route::post('/{pengajuanIzin}/approve', [PengajuanIzinController::class, 'approve'])->name('approve');
    Route::post('/{pengajuanIzin}/reject', [PengajuanIzinController::class, 'reject'])->name('reject');
});

// View pengumuman detail - semua user terautentikasi bisa mengakses
Route::middleware(['auth', 'verified'])
    ->get('/pengumuman/{pengumuman}', [PengumumanController::class, 'show'])
    ->name('pengumuman.show');

Route::middleware([
    'auth',
    'verified',
])->prefix('pengumuman')
    ->name('pengumuman.')
    ->group(function () {

        Route::post('/', [PengumumanController::class, 'store'])
            ->name('store');

        Route::put('/{pengumuman}', [PengumumanController::class, 'update'])
            ->name('update');

        Route::delete('/{pengumuman}', [PengumumanController::class, 'destroy'])
            ->name('destroy');
    });

/*
|--------------------------------------------------------------------------
| Timeline
|--------------------------------------------------------------------------
*/
// View: Semua role yang terautentikasi
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/timeline', [TimelineController::class, 'index'])
        ->name('timeline.index');
});

// CRUD: Hanya Admin & Sekretaris
Route::middleware([
    'auth',
    'verified',
])->prefix('timeline')
    ->name('timeline.')
    ->group(function () {

        Route::post('/', [TimelineController::class, 'store'])
            ->name('store');

        Route::put('/{timeline}', [TimelineController::class, 'update'])
            ->name('update');

        Route::delete('/{timeline}', [TimelineController::class, 'destroy'])
            ->name('destroy');
    });

/*
|--------------------------------------------------------------------------
| Notulensi
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth',
    'verified',
])->prefix('notulensi')
    ->name('notulensi.')
    ->group(function () {

        Route::post('/', [NotulensiController::class, 'store'])
            ->name('store');

        Route::delete('/{notulensi}', [NotulensiController::class, 'destroy'])
            ->name('destroy');
    });

// View PDF — semua user terautentikasi bisa mengakses
Route::middleware(['auth', 'verified'])
    ->get('/notulensi/{notulensi}/view', [NotulensiController::class, 'viewPdf'])
    ->name('notulensi.view');

/*
|--------------------------------------------------------------------------
| Scan
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth',
    'verified',
])->group(function () {
    Route::get('/scan', function () {
        $user = auth()->user();
        if (!$user->hasRole('Admin') && !$user->isArchivist()) {
            abort(403, 'Anda tidak memiliki akses.');
        }
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


// list panitia
Route::middleware([
    'auth',
    'verified',
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
    'role:admin',
])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])
        ->name('admin.dashboard');
    Route::get('/admin/role-request', [AdminController::class, 'roleRequest'])
        ->name('admin.role-request');
    Route::post('/admin/role-request/{roleRequest}/approve', [AdminController::class, 'approveRoleRequest'])
        ->name('admin.role-request.approve');
    Route::post('/admin/role-request/{roleRequest}/reject', [AdminController::class, 'rejectRoleRequest'])
        ->name('admin.role-request.reject');

    // Manage Users
    Route::get('/admin/manage-users', [AdminController::class, 'manageUsers'])
        ->name('admin.manage-users.index');
    Route::get('/admin/manage-users/create', [AdminController::class, 'createUserForm'])
        ->name('admin.manage-users.create');
    Route::post('/admin/manage-users', [AdminController::class, 'storeUser'])
        ->name('admin.manage-users.store');
    Route::put('/admin/manage-users/{user}', [AdminController::class, 'updateUserRole'])
        ->name('admin.manage-users.update');
    Route::delete('/admin/manage-users/{user}', [AdminController::class, 'destroyUser'])
        ->name('admin.manage-users.destroy');

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
])->group(function () {
    Route::post('/scan/lookup', [ScanController::class, 'lookup'])
        ->name('scan.lookup');
    Route::post('/scan/attendance', [ScanController::class, 'recordAttendance'])
        ->name('scan.attendance');

    // Kontrol & Penjadwalan Absensi (Sekretaris & Admin)
    Route::post('/absen/{rapat}/toggle', [PresensiController::class, 'toggleAbsen'])
        ->name('absen.toggle');
    Route::post('/absen/{rapat}/schedule', [PresensiController::class, 'updateJadwalAbsen'])
        ->name('absen.schedule');
});


require __DIR__ . '/auth.php';
