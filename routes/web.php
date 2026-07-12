<?php

use App\Http\Controllers\AdminOutsourcingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\JadwalKaryawanController;
use App\Http\Controllers\KepalaDepartemenController;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/* Route untuk login berfungsi menyalurkan request dan melakukan validasi awal */
/* mengembalikan data ke controller Auth dan mengeksekusi method atau fungsi login */
Route::get('/', [AuthController::class, 'login'])->name('login');

/* Route Kepala departement */
Route::middleware(['auth', 'role:kepala_departemen'])->group(function () {
    Route::get('/kepala-departement/dashboard', [KepalaDepartemenController::class, 'dashboard'])->name('kepala_departemen.dashboard');
    Route::get('/kepala-departement/api/jadwal', [KepalaDepartemenController::class, 'getJadwalKaryawan'])->name('kepala_departemen.api.jadwal');
    Route::post('/kepala-departement/api/jadwal', [KepalaDepartemenController::class, 'storeJadwalKaryawan'])->name('kepala_departemen.api.jadwal.store');
    Route::get('/kepala-departement/api/summary', [KepalaDepartemenController::class, 'getDashboardSummary'])->name('kepala_departemen.api.summary');
    Route::get('/kepala-departement/api/karyawan-all', [KepalaDepartemenController::class, 'getAllKaryawan'])->name('kepala_departemen.api.karyawan_all');
    Route::get('/kepala-departement/download-template-jadwal', [KepalaDepartemenController::class, 'downloadTemplateJadwal'])->name('kepala_departemen.download_template_jadwal');
    Route::get('/kepala-departement/export-jadwal', [KepalaDepartemenController::class, 'exportJadwal'])->name('kepala_departemen.export_jadwal');

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

    Route::get('/kepala-departement/atur-lokasi', function () {
        return view('kepala-departement.atur-lokasi');
    });
});

/* Admin OutSourcing */
Route::middleware(['auth', 'role:admin_outsourcing'])->group(function () {
    Route::get('/admin-outsourcing/dashboard', [AdminOutsourcingController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin-outsourcing/export-absensi', [AdminOutsourcingController::class, 'exportAbsensi'])->name('admin.export_absensi');
    Route::get('/admin-outsourcing/kelola-karyawan', [AdminOutsourcingController::class, 'kelolaKaryawan']);
    Route::get('/admin-outsourcing/api/departemen', [AdminOutsourcingController::class, 'getDepartemen']);
    Route::post('/admin-outsourcing/api/karyawan', [AdminOutsourcingController::class, 'storeKaryawan']);
    Route::get('/admin-outsourcing/pengajuan-karyawan', function () {
        return view('adminOutsourcing.pengajuanKaryawan');
    })->name('admin.pengajuan-karyawan');
    Route::get('/admin-outsourcing/pengajuan-akun', [AdminOutsourcingController::class, 'pengajuanAkun'])->name('admin.pengajuan-akun');
});

/* USER HR */
Route::middleware(['auth', 'role:hr'])->group(function () {
    Route::get('/hr/dashboard', function () {
        return view('hr.dashboard');
    })->name('hr.dashboard');

    Route::get('/hr/export-lembur', [\App\Http\Controllers\HRController::class, 'exportLembur'])->name('hr.export_lembur');
    Route::get('/hr/export-absensi', [\App\Http\Controllers\HRController::class, 'exportAbsensi'])->name('hr.export_absensi');

    Route::get('/hr/rekapan-detail', function () {
        return view('hr.rekapanDetail');
    });

    Route::get('/hr/ajuan-data-karyawan', function () {
        return view('hr.ajuanDataKaryawan');
    });

    Route::get('/hr/data-karyawan', function () {
        return view('hr.dataKaryawan');
    });

    Route::get('/api/hr/karyawan', [KaryawanController::class, 'index'])->name('api.hr.karyawan');
    Route::get('/api/hr/vendors', [KaryawanController::class, 'getVendors'])->name('api.hr.vendors');
});

/* Super Admin */
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/super-admin/dashboard', function () {
        return view('superAdmin.dashboardAdmin');
    })->name('super.dashboard');

    Route::get('/super-admin/departemen', function () {
        return view('superAdmin.departemenAdmin');
    })->name('super.departemen');

    Route::get('/super-admin/pengaturan', function () {
        return view('superAdmin.pengaturanAdmin');
    });
});

/* Karyawan Outsourcing */
Route::middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/karyawan-outsourcing/dashboard', function () {
        return view('karyawanOutsourcing.dashboardKaryawan');
    })->name('dashboard');

    Route::get('/karyawan-outsourcing/jadwal-karyawan/pdf/{year}/{month}', [JadwalKaryawanController::class, 'exportPdf']);

    Route::get('/karyawan-outsourcing/perizinan-karyawan', function () {
        return view('karyawanOutsourcing.perizinan');
    });

    Route::get('/karyawan-outsourcing/pengajuanKaryawan', function () {
        return view('karyawanOutsourcing.pengajuanKaryawan');
    });

    Route::get('/karyawan-outsourcing/jadwal-karyawan', function () {
        return view('karyawanOutsourcing.jadwalKaryawan');
    });
});

/* Profil Semua User (Bisa diakses oleh semua role asal terautentikasi) */
Route::get('/profil', function () {
    return view('profil.index');
})->name('profil')->middleware('auth');
