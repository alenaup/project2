<?php

namespace App\Services;

use App\Models\User;
use App\Models\Kehadiran;
use App\Models\RekapKehadiran;
use App\Enums\Status;
use App\Enums\UserRole;
use App\Enums\Validasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminOutsourcingDashboardService
{
    /**
     * Mengambil jumlah karyawan aktif.
     */
    public function getKaryawanCount(int $outsourcingId): int
    {
        return User::where('role', UserRole::Karyawan->value)
            ->where('outsourcing_id', $outsourcingId)
            ->where('status', Status::Active->value)
            ->whereNull('tanggal_keluar')
            ->count();
    }

    /**
     * Mengambil stats untuk stat cards.
     */
    public function getStats(int $outsourcingId, string $startDate, string $endDate): array
    {
        $userService = new UserService();
        $kehadiranService = new KehadiranService();

        $karyawanIds = $userService->getKaryawanByOutsourcing($outsourcingId, "array");

        $totalHadir = $kehadiranService->totalHadirByDateRange($karyawanIds, $startDate, $endDate);
        $totalAlpha = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('mankir', $karyawanIds, $startDate, $endDate);
        
        $sakit = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('sakit', $karyawanIds, $startDate, $endDate);
        $izin = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('izin', $karyawanIds, $startDate, $endDate);
        $totalIzinSakit = $sakit + $izin;

        $totalKaryawan = $this->getKaryawanCount($outsourcingId);

        return [
            'total_hadir' => $totalHadir,
            'total_alpha' => $totalAlpha,
            'total_izin_sakit' => $totalIzinSakit,
            'total_karyawan' => $totalKaryawan,
        ];
    }

    /**
     * Mengambil list karyawan terpaginasi.
     */
    public function getKaryawans(int $outsourcingId, int $page, int $perPage)
    {
        return User::with(['departemen', 'outsourcing'])
            ->where('role', UserRole::Karyawan->value)
            ->where('outsourcing_id', $outsourcingId)
            ->where('status', Status::Active->value)
            ->whereNull('tanggal_keluar')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();
    }

    /**
     * Mengambil rekap record RekapKehadiran.
     */
    public function loadRekapRecord(array $karyawanIds, string $startDate, string $endDate): ?RekapKehadiran
    {
        $firstKehadiranWithRekapan = Kehadiran::whereIn('karyawan_id', $karyawanIds)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->whereNotNull('rekapan_kehadiran_id')
            ->first();

        if ($firstKehadiranWithRekapan) {
            $rekap = RekapKehadiran::find($firstKehadiranWithRekapan->rekapan_kehadiran_id);
            if ($rekap && $rekap->tanggal_rekap && $rekap->tanggal_rekap->format('Y-m-d') >= $startDate) {
                return $rekap;
            }
        }

        return null;
    }

    /**
     * Kirim rekapan baru.
     */
    public function kirimRekapan(int $outsourcingId, string $startDate, string $endDate): ?RekapKehadiran
    {
        $userService = new UserService();
        $kehadiranService = new KehadiranService();

        $karyawanIds = $userService->getKaryawanByOutsourcing($outsourcingId, "array");

        $kehadiranQuery = Kehadiran::whereIn('karyawan_id', $karyawanIds)
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($kehadiranQuery->count() === 0) {
            return null;
        }

        $totalHadir = $kehadiranService->totalHadirByDateRange($karyawanIds, $startDate, $endDate);
        $totalAlpha = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('mankir', $karyawanIds, $startDate, $endDate);
        $totalSakit = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('sakit', $karyawanIds, $startDate, $endDate);
        $totalIzin = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('izin', $karyawanIds, $startDate, $endDate);
        $totalCuti = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('cuti', $karyawanIds, $startDate, $endDate);
        $totalTerlambat = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('terlambat', $karyawanIds, $startDate, $endDate);

        $rekap = RekapKehadiran::create([
            'pengaju' => Auth::id(),
            'status_validasi' => Validasi::Pending->value,
            'status' => Status::Active->value,
            'tanggal_rekap' => now(),
            'total_hadir' => $totalHadir,
            'total_mankir' => $totalAlpha,
            'total_sakit' => $totalSakit,
            'total_izin' => $totalIzin,
            'total_cuti' => $totalCuti,
            'total_terlambat' => $totalTerlambat,
            'total_jam_kerja' => $totalHadir * 8,
        ]);

        $kehadiranQuery->update([
            'rekapan_kehadiran_id' => $rekap->id_rekapan
        ]);

        return $rekap;
    }

    /**
     * Kirim ulang rekapan.
     */
    public function kirimUlang(RekapKehadiran $rekapRecord, int $outsourcingId, string $startDate, string $endDate): void
    {
        $userService = new UserService();
        $kehadiranService = new KehadiranService();

        $karyawanIds = $userService->getKaryawanByOutsourcing($outsourcingId, "array");

        $totalHadir = $kehadiranService->totalHadirByDateRange($karyawanIds, $startDate, $endDate);
        $totalAlpha = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('mankir', $karyawanIds, $startDate, $endDate);
        $totalSakit = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('sakit', $karyawanIds, $startDate, $endDate);
        $totalIzin = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('izin', $karyawanIds, $startDate, $endDate);
        $totalCuti = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('cuti', $karyawanIds, $startDate, $endDate);
        $totalTerlambat = $kehadiranService->cekKehadiranBanyakKaryawanByDateRange('terlambat', $karyawanIds, $startDate, $endDate);

        $rekapRecord->update([
            'status_validasi' => Validasi::Pending->value,
            'tanggal_rekap' => now(),
            'total_hadir' => $totalHadir,
            'total_mankir' => $totalAlpha,
            'total_sakit' => $totalSakit,
            'total_izin' => $totalIzin,
            'total_cuti' => $totalCuti,
            'total_terlambat' => $totalTerlambat,
            'total_jam_kerja' => $totalHadir * 8,
        ]);
    }
}
