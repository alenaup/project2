<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JadwalKaryawanController extends Controller
{
    // melakukan fungsi exportPdf untuk mengekspor jadwal karyawan ke dalam format PDF
    // input berupa tahun dan bulan yang dipilih oleh user
    // output berupa file PDF yang berisi jadwal karyawan untuk bulan dan tahun yang dip
    public function exportPdf($year, $month)
    {
        $userId = Auth::id() ?? 1;

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $jadwals = DB::table('karyawan_jadwal')
            ->join('jadwal', 'karyawan_jadwal.jadwal_id', '=', 'jadwal.id_jadwal')
            ->join('shift', 'jadwal.shift_id', '=', 'shift.id_shift')
            ->where('karyawan_jadwal.user_id', $userId)
            ->where('jadwal.tanggal_mulai', '<=', $endDate->format('Y-m-d'))
            ->where('jadwal.tanggal_akhir', '>=', $startDate->format('Y-m-d'))
            ->select(
                'jadwal.*',
                'shift.nama_shift',
                'shift.jam_masuk',
                'shift.jam_keluar'
            )
            ->get();

        $jadwalByDate = [];

        foreach ($jadwals as $j) {
            $mulai = Carbon::parse($j->tanggal_mulai);
            $akhir = Carbon::parse($j->tanggal_akhir);

            while ($mulai <= $akhir) {
                $jadwalByDate[$mulai->format('Y-m-d')] = $j;
                $mulai->addDay();
            }
        }

        $dateObj = Carbon::createFromDate($year, $month, 1);

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

        return $pdf->download(
            'Jadwal_Kerja_' . str_replace(' ', '_', $monthNameStr) . '.pdf'
        );
    }
}
