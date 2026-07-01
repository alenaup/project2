<?php

namespace App\Services;

use App\Models\User;
use App\Models\Lembur;
use App\Models\RekapKehadiran;
use App\Models\Departemen;
use App\Enums\Status;
use App\Enums\Validasi;
use App\Enums\UserRole;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class HRDashboardService
{
    /**
     * Hitung data statistik untuk dashboard HR.
     */
    public function getStats(): array
    {
        $totalOutsourcingAktif = User::whereNotNull('outsourcing_id')
            ->whereNull('tanggal_keluar')
            ->count();

        $totalOutsourcingTerdaftar = User::whereNotNull('outsourcing_id')->count();

        $totalLemburPending = Lembur::where('status_validasi', Validasi::Pending->value)->count();
        $totalRekapPending = RekapKehadiran::where('status_validasi', Validasi::Pending->value)->count();

        $totalAjuanPending = User::whereNotNull('outsourcing_id')
            ->where('role', UserRole::Karyawan->value)
            ->where('status', Status::Inactive->value)
            ->count();

        return [
            'outsourcing_aktif'     => $totalOutsourcingAktif,
            'outsourcing_terdaftar' => $totalOutsourcingTerdaftar,
            'lembur_pending'        => $totalLemburPending,
            'rekap_pending'         => $totalRekapPending,
            'ajuan_pending'         => $totalAjuanPending,
        ];
    }

    /**
     * Dapatkan query data lembur berdasarkan filter.
     */
    public function getLemburQuery(string $startDate = '', string $endDate = '', string $departemenId = ''): Builder
    {
        $query = Lembur::with(['karyawan.outsourcing', 'karyawan.departemen'])
            ->where('status', Status::Active->value)
            ->orderByDesc('mulai_lembur');

        if ($startDate !== '') {
            $query->whereDate('mulai_lembur', '>=', $startDate);
        }

        if ($endDate !== '') {
            $query->whereDate('mulai_lembur', '<=', $endDate);
        }

        if ($departemenId !== '') {
            $query->whereHas('karyawan', function ($q) use ($departemenId) {
                $q->where('departemen_id', $departemenId);
            });
        }

        return $query;
    }

    /**
     * Dapatkan daftar departemen untuk dropdown.
     */
    public function getDepartemenList(): array
    {
        return Departemen::orderBy('nama_departemen')->get(['id_departemen', 'nama_departemen'])->toArray();
    }

    /**
     * Hitung total lembur periode (26 bulan lalu s/d 25 bulan terpilih).
     */
    public function calculateTotalLembur(string $bulan, string $tahun, string $departemenId = ''): array
    {
        $month = (int) $bulan;
        $year = (int) $tahun;

        if ($month === 1) {
            $bulanLalu = 12;
            $tahunLalu = $year - 1;
        } else {
            $bulanLalu = $month - 1;
            $tahunLalu = $year;
        }

        $startDate = sprintf('%04d-%02d-26', $tahunLalu, $bulanLalu);
        $endDate = sprintf('%04d-%02d-25', $year, $month);

        $query = Lembur::where('status', Status::Active->value)
            ->where('status_validasi', Validasi::Valid->value)
            ->whereDate('mulai_lembur', '>=', $startDate)
            ->whereDate('selesai_lembur', '<=', $endDate);

        if ($departemenId !== '') {
            $query->whereHas('karyawan', function ($q) use ($departemenId) {
                $q->where('departemen_id', $departemenId);
            });
        }

        $lemburs = $query->get(['mulai_lembur', 'selesai_lembur']);

        $totalMenit = 0;
        foreach ($lemburs as $lembur) {
            if ($lembur->mulai_lembur && $lembur->selesai_lembur) {
                $totalMenit += (int) Carbon::parse($lembur->mulai_lembur)->diffInMinutes(Carbon::parse($lembur->selesai_lembur));
            }
        }

        return [
            'total_menit' => $totalMenit,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }
}
