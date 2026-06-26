<?php

use App\Http\Controllers\AdminOutsourcingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KaryawanController;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/* Route untuk login berfungsi menyalurkan reuest dan melakukan validasi awal */
/* mengembalikan data ke controller Auth dan mengeksekusi  method atau fungsi login */
Route::get('/', [AuthController::class, 'login'])->/* ini adalah nama rute */ name('login');

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
    Route::get('/admin-outsourcing/dashboard', [AdminOutsourcingController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin-outsourcing/kelola-karyawan', [AdminOutsourcingController::class, 'kelolaKaryawan']);
    Route::get('/admin-outsourcing/api/departemen', [AdminOutsourcingController::class, 'getDepartemen']);
    Route::post('/admin-outsourcing/api/karyawan', [AdminOutsourcingController::class, 'storeKaryawan']);
    Route::get('/admin-outsourcing/pengajuan-karyawan', function () {
        return view('adminOutsourcing.pengajuanKaryawan');
    })->name('admin.pengajuan-karyawan');
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

Route::get('/api/hr/karyawan', [KaryawanController::class, 'index'])->name('api.hr.karyawan');

Route::get('/api/hr/vendors', [KaryawanController::class, 'getVendors'])->name('api.hr.vendors');

/* USER HR SELESAI */

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

    Route::get('/karyawan-outsourcing/jadwal-karyawan/pdf/{year}/{month}', function ($year, $month) {
        $userId = Auth::id() ?? 1; // Fallback to user 1 for test if session is missing

        $startDate = Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $jadwals = DB::table('karyawan_jadwal')
            ->join('jadwal', 'karyawan_jadwal.jadwal_id', '=', 'jadwal.id_jadwal')
            ->join('shift', 'jadwal.shift_id', '=', 'shift.id_shift')
            ->where('karyawan_jadwal.user_id', $userId)
            ->whereBetween('jadwal.tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        $jadwalByDate = [];
        foreach ($jadwals as $j) {
            $jadwalByDate[$j->tanggal] = $j;
        }

        $dateObj = Carbon\Carbon::createFromDate($year, $month, 1);
        $monthName = clone $dateObj;
        $monthName->locale('id');
        $monthNameStr = $monthName->translatedFormat('F Y');

        $pdf = Pdf::loadView('pdf.jadwal-karyawan', [
            'calendarData' => $jadwalByDate,
            'monthName' => $monthNameStr,
            'user' => Auth::user(),
            'daysInMonth' => $dateObj->daysInMonth,
            'firstDayOfWeek' => $dateObj->copy()->startOfMonth()->dayOfWeekIso,
            'currentYear' => $year,
            'currentMonth' => $month,
        ]);

        return $pdf->download('Jadwal_Kerja_'.str_replace(' ', '_', $monthNameStr).'.pdf');
    });

    Route::get('/karyawan-outsourcing/perizinan-karyawan', function () {
        return view('karyawanOutsourcing.perizinan');
    });

});

Route::get('/karyawan-outsourcing/perizinan-karyawan', function () {
    return view('karyawanOutsourcing.perizinan');
});

Route::get('/karyawan-outsourcing/pengajuanKaryawan', function () {
    return view('karyawanOutsourcing.pengajuanKaryawan');
});

Route::get('/karyawan-outsourcing/jadwal-karyawan', function () {
    return view('karyawanOutsourcing.jadwalKaryawan');
});

