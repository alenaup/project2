<?php

namespace App\Livewire\Karyawan;

use App\Models\Jadwal;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class JadwalKaryawan extends Component
{
    public $currentYear;
    public $currentMonth;

    public function mount()
    {
        $this->currentYear = Carbon::now()->year;
        $this->currentMonth = Carbon::now()->month;
    }

    public function previousMonth()
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentYear = $date->year;
        $this->currentMonth = $date->month;
    }

    public function nextMonth()
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentYear = $date->year;
        $this->currentMonth = $date->month;
    }

    public function goToToday()
    {
        $this->currentYear = Carbon::now()->year;
        $this->currentMonth = Carbon::now()->month;
    }

    public function downloadPdf()
    {
        $data = $this->getJadwalData();
        $dateObj = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1);
        $monthName = clone clone $dateObj;
        $monthName->locale('id');
        $monthNameStr = $monthName->translatedFormat('F Y');
        
        $pdf = Pdf::loadView('pdf.jadwal-karyawan', [
            'calendarData' => $data,
            'monthName' => $monthNameStr,
            'user' => Auth::user(),
            'daysInMonth' => $dateObj->daysInMonth,
            'firstDayOfWeek' => $dateObj->copy()->startOfMonth()->dayOfWeekIso,
            'currentYear' => $this->currentYear,
            'currentMonth' => $this->currentMonth,
        ]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'Jadwal_Kerja_' . str_replace(' ', '_', $monthNameStr) . '.pdf');
    }

    private function getJadwalData()
    {
        $userId = Auth::id();
        $startDate = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->startOfMonth();
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

        return $jadwalByDate;
    }

    public function render()
    {
        $startDate = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1);
        $startDate->locale('id');
        
        $monthName = $startDate->translatedFormat('F Y');
        $daysInMonth = $startDate->daysInMonth;
        
        // 1 = Monday, 7 = Sunday
        $firstDayOfWeek = $startDate->copy()->startOfMonth()->dayOfWeekIso;

        $calendarData = $this->getJadwalData();

        return view('livewire.karyawan.jadwal-karyawan', [
            'monthName' => $monthName,
            'daysInMonth' => $daysInMonth,
            'firstDayOfWeek' => $firstDayOfWeek,
            'calendarData' => $calendarData,
            'currentDate' => Carbon::now()->format('Y-m-d')
        ]);
    }
}
