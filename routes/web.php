<?php

use App\Http\Controllers\AdminOutsourcingController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


/* Route untuk login berfungsi menyalurkan reuest dan melakukan validasi awal */
/* mengembalikan data ke controller Auth dan mengeksekusi  method atau fungsi login*/
Route::get('/', [AuthController::class, 'login'])->/* ini adalah nama rute */name('login');


/* Route Kepala departement */
Route::get('/kepala-departement/dashboard', function () {
    return view('kepala-departement.dashboard');
})->name('kepala_departemen.dashboard');

Route::get('/kepala-departement/karyawan', function () {
    return view('kepala-departement.karyawan');
});

Route::get('/kepala-departement/shift', function () {
    return view('kepala-departement.shift');
});

Route::get('/kepala-departement/laporan', function () {
    return view('kepala-departement.cutiizin');
});

Route::get('/kepala-departement/pengajuan', function () {
    return view('kepala-departement.pengajuanKaryawan');
});

/* Kepala departement seelesai */


/* ============================================================== */


/* Admin OutSourcing */
Route::middleware('auth')->group(function () {
    Route::get('/admin-outsourcing/dashboard',  [AdminOutsourcingController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin-outsourcing/kelola-karyawan', [AdminOutsourcingController::class, 'kelolaKaryawan']);
    Route::get('/admin-outsourcing/api/departemen', [AdminOutsourcingController::class, 'getDepartemen']);
    Route::post('/admin-outsourcing/api/karyawan', [AdminOutsourcingController::class, 'storeKaryawan']);
}); 

Route::get('/admin-outsourcing/pengajuan-karyawan', function () {
    return view('adminOutsourcing.pengajuanKaryawan');
});

/* Admin OutSourcing */

/* ============================================================ */

/* USER HR */

Route::get('/hr/dashboard', function () {
    return view('hr.dashboard');
})->name('hr.dashboard');

Route::get('/hr/rekapan-detail', function () {
    return view('hr.rekapanDetail');
});

Route::get('/hr/ajuan-data-karyawan', function () {
    return view('hr.ajuanDataKaryawan');
});

Route::get('/hr/data-karyawan', function () {
    return view('hr.dataKaryawan');
});

/* USER HR SELESAI*/

/* ======================================================================== */

/* Super Admin */

Route::get('/super-admin/dashboard', function () {
    return view('superAdmin.dashboardAdmin');
})->name('super.dashboard');

Route::get('/super-admin/pengaturan', function () {
    return view('superAdmin.pengaturanAdmin');
});

/* super admin selesai */

/* ======================================================== */

/* Karyawan Outsourcing */

Route::middleware('auth')->group(function () {

    Route::get('/karyawan-outsourcing/dashboard', function () {
        return view('karyawanOutsourcing.dashboardKaryawan');
    })->name('dashboard');


    Route::get('/karyawan-outsourcing/pengajuanKaryawan', function () {
        return view('karyawanOutsourcing.pengajuanKaryawan');
    });


    Route::get('/karyawan-outsourcing/jadwal-karyawan', function () {
        return view('karyawanOutsourcing.jadwalKaryawan');
    });


    Route::get('/karyawan-outsourcing/perizinan-karyawan', function () {
        return view('karyawanOutsourcing.perizinan');
    });

});


