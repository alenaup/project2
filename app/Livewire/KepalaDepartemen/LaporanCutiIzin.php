<?php

namespace App\Livewire\KepalaDepartemen;

use App\Services\PerizinanSakitService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LaporanCutiIzin extends Component
{
    public $search = '';

    public $filterDate = '';

    protected PerizinanSakitService $perizinanSakitService;

    /**
     * Bootstraps the component with dependency injection.
     *
     * @return void
     */
    public function boot(PerizinanSakitService $perizinanSakitService)
    {
        $this->perizinanSakitService = $perizinanSakitService;
    }

    public function getJenis($item)
    {
        $ket = strtolower($item->keterangan ?? '');
        if (str_contains($ket, 'sakit') || str_contains($ket, 'dokter') || str_contains($ket, 'klinik') || str_contains($ket, 'obat') || $item->file_surat) {
            return 'Sakit';
        }
        if (str_contains($ket, 'cuti') || str_contains($ket, 'tahunan') || str_contains($ket, 'melahirkan') || str_contains($ket, 'nikah')) {
            return 'Cuti';
        }

        return 'Izin';
    }

    public function render()
    {
        $deptId = Auth::check() ? Auth::user()->departemen_id : null;

        // Ambil perizinan sakit semua karyawan via service
        $allReportsQuery = $this->perizinanSakitService->ambilPerizinanSakitSemuaKaryawan($deptId);

        $allReports = $allReportsQuery->get();

        $totalSakit = 0;
        $totalIzin = 0;
        $totalCuti = 0;

        foreach ($allReports as $report) {
            $jenis = $this->getJenis($report);
            if ($jenis === 'Sakit') {
                $totalSakit++;
            } elseif ($jenis === 'Cuti') {
                $totalCuti++;
            } else {
                $totalIzin++;
            }
        }

        // Ambil laporan perizinan terfilter via service
        $reports = $this->perizinanSakitService->ambilLaporanPerizinanFiltered(
            $deptId,
            $this->search,
            $this->filterDate
        );

        return view('livewire.kepala-departemen.laporan-cuti-izin', [
            'reports' => $reports,
            'totalSakit' => $totalSakit,
            'totalIzin' => $totalIzin,
            'totalCuti' => $totalCuti,
        ]);
    }
}
