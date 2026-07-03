<?php

namespace App\Services;
use App\Models\RekapKehadiran;
use App\Models\Kehadiran;
use App\Enums\Validasi;
use App\Enums\Status;
use Carbon\Carbon;

class RekapService
{
    public function ambilRekapDetail()
    {
        return RekapKehadiran::where('status', 'active')->first()
                ?? RekapKehadiran::first();
    }

    public function getPendingCount(): int
    {
        return RekapKehadiran::where('status_validasi', Validasi::Pending->value)->count();
    }

    /**
     * Dapatkan record rekapitulasi kehadiran berdasarkan vendorId dan rentang tanggal periode rekap.
     */
    public function getRekapRecord(int $vendorId, string $startDate, string $endDate): ?RekapKehadiran
    {
        return RekapKehadiran::whereHas('pengajuUser', function ($q) use ($vendorId) {
                $q->where('outsourcing_id', $vendorId);
            })
            ->whereNotNull('tanggal_rekap')
            ->where('tanggal_rekap', '>=', $startDate)
            ->whereHas('kehadiran', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
            })
            ->first();
    }

    /**
     * Update status validasi rekapitulasi kehadiran.
     */
    public function updateStatusValidasi(int $rekapId, string $statusValidasi, int $validatorId): bool
    {
        $rekap = RekapKehadiran::find($rekapId);
        if ($rekap) {
            return $rekap->update([
                'status_validasi' => $statusValidasi,
                'pevalidasi' => $validatorId,
                'tanggal_validasi' => now(),
            ]);
        }
        return false;
    }

    /**
     * Mengambil rekap record RekapKehadiran berdasarkan list ID karyawan.
     */
    public function loadRekapRecordForOutsourcing(array $karyawanIds, string $startDate, string $endDate): ?RekapKehadiran
    {
        $firstKehadiranWithRekapan = Kehadiran::whereIn('karyawan_id', $karyawanIds)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->whereNotNull('rekapan_kehadiran_id')
            ->first();

        if ($firstKehadiranWithRekapan) {
            $rekap = RekapKehadiran::find($firstKehadiranWithRekapan->rekapan_kehadiran_id);
            if ($rekap && $rekap->tanggal_rekap) {
                $tglStr = $rekap->tanggal_rekap instanceof \Carbon\Carbon 
                    ? $rekap->tanggal_rekap->format('Y-m-d') 
                    : Carbon::parse($rekap->tanggal_rekap)->format('Y-m-d');
                if ($tglStr >= $startDate) {
                    return $rekap;
                }
            }
        }

        return null;
    }

    /**
     * Kirim rekapan baru untuk outsourcing.
     */
    public function kirimRekapanForOutsourcing(int $outsourcingId, string $startDate, string $endDate, int $authId): ?RekapKehadiran
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
            'pengaju' => $authId,
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
     * Kirim ulang rekapan untuk outsourcing.
     */
    public function kirimUlangRekapanForOutsourcing(RekapKehadiran $rekapRecord, int $outsourcingId, string $startDate, string $endDate): void
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