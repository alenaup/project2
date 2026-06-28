<?php

namespace App\Livewire\KepalaDepartemen;

use Livewire\Component;
use App\Models\PerizinanSakit;
use App\Models\User;
use App\Enums\Status;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaporanCutiIzin extends Component
{
    public $search = '';
    public $filterDate = '';

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

        // Base query for totals (unfiltered by date/search, restricted to active karyawan in same department)
        $allReportsQuery = PerizinanSakit::query()
            ->whereHas('karyawan', function ($q) use ($deptId) {
                $q->where('role', UserRole::Karyawan->value)
                  ->where('status', Status::Active->value);
                if ($deptId) {
                    $q->where('departemen_id', $deptId);
                }
            });

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

        // Filtered query for the list
        $query = PerizinanSakit::query()
            ->with('karyawan.departemen')
            ->whereHas('karyawan', function ($q) use ($deptId) {
                $q->where('role', UserRole::Karyawan->value)
                  ->where('status', Status::Active->value);
                if ($deptId) {
                    $q->where('departemen_id', $deptId);
                }
                if ($this->search) {
                    $q->where('nama_lengkap', 'like', '%' . $this->search . '%');
                }
            });

        if ($this->filterDate) {
            $query->whereDate('tanggal_pengajuan', $this->filterDate);
        }

        $reports = $query->orderBy('tanggal_pengajuan', 'desc')->get();

        return view('livewire.kepala-departemen.laporan-cuti-izin', [
            'reports' => $reports,
            'totalSakit' => $totalSakit,
            'totalIzin' => $totalIzin,
            'totalCuti' => $totalCuti,
        ]);
    }
}
