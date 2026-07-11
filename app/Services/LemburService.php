<?php

namespace App\Services;

use App\Enums\Validasi;
use App\Models\Lembur;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Enums\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class LemburService
{
    /**
     * Mendapatkan data detail lembur berdasarkan ID.
     */
    public function getLemburById(int $id): ?Lembur
    {
        return Lembur::with('karyawan')->find($id);
    }

    public function getLemburOrFail(int $id): Lembur
    {
        return Lembur::findOrFail($id);
    }

    /**
     * Mendapatkan daftar pengajuan lembur per departemen dengan paginasi.
     */
    public function getLemburListByDepartemenPaginated(?int $deptId, int $perPage = 20, ?string $date = null)
    {
        if (!$deptId) {
            return collect();
        }

        $query = Lembur::with('karyawan')
            ->whereHas('karyawan', function ($query) use ($deptId) {
                $query->where('departemen_id', $deptId);
            });

        if ($date) {
            $query->whereDate('mulai_lembur', $date);
        }

        return $query->latest('created_at')
            ->paginate($perPage);
    }

    /**
     * Memeriksa apakah ada pengajuan lembur yang berstatus pending di departemen tertentu.
     */
    public function hasPendingLembur(?int $deptId): bool
    {
        if (!$deptId) {
            return false;
        }

        return Lembur::whereHas('karyawan', function ($query) use ($deptId) {
                $query->where('departemen_id', $deptId);
            })
            ->where('status_validasi', Validasi::Pending->value)
            ->exists();
    }

    public function listTanggalPendingLembur(?int $deptId)
    {
        if (!$deptId) {
            return collect();
        }

        return Lembur::with('karyawan')
                ->whereHas('karyawan', function ($query) use ($deptId) {
                    $query->where('departemen_id', $deptId);
                })
                ->where('status_validasi', \App\Enums\Validasi::Pending->value)
                ->orderBy('created_at', 'asc')
                ->get();
    }
    
    /**
     * Menyetujui pengajuan lembur.
     */
    public function approveLembur(int $id, int $validatorId, string $status_validasi): bool
    {
        $lembur = $this->getLemburOrFail($id);
        if ($lembur && $lembur->status_validasi === Validasi::Pending->value) {
            if ($status_validasi === 'valid') {
                return $lembur->update([
                    'status_validasi' => Validasi::Valid->value,
                    'pemvalidasi_id' => $validatorId,
                ]);
            } else if ($status_validasi === 'invalid') {
                return $lembur->update([
                    'status_validasi' => Validasi::Invalid->value,
                    'pemvalidasi_id' => $validatorId,
                ]);
            } else {
                return false;
            }

        }
        return false;
    }

    /**
     * Menyetujui semua pengajuan lembur berstatus pending di departemen tertentu.
     */
    public function approveAllPendingLembur(?int $deptId, int $validatorId, ?string $date = null): int
    {
        if (!$deptId) {
            return 0;
        }

        $query = Lembur::whereHas('karyawan', function ($query) use ($deptId) {
                $query->where('departemen_id', $deptId);
            })
            ->where('status_validasi', Validasi::Pending->value);

        if ($date) {
            $query->whereDate('mulai_lembur', $date);
        }

        $pendingLemburs = $query->get();

        if ($pendingLemburs->isEmpty()) {
            return 0;
        }

        $count = 0;
        foreach ($pendingLemburs as $lembur) {
            $updated = $lembur->update([
                'status_validasi' => Validasi::Valid->value,
                'pemvalidasi_id' => $validatorId,
            ]);
            if ($updated) {
                $count++;
            }
        }

        return $count;
    }

    public function createLembur($tanggal_lembur, $jam_mulai, $jam_selesai, $keterangan)
    {
        return Lembur::create([
            'mulai_lembur'    => $tanggal_lembur . ' ' . $jam_mulai . ':00',
            'selesai_lembur'  => $tanggal_lembur . ' ' . $jam_selesai . ':00',
            'created_at'  => now(),
            'status'          => 'active',
            'status_validasi' => 'pending',
            'keterangan'      => $keterangan,
            'karyawan_id'     => Auth::id() ?? User::first()->id_user,
        ]);
    }

    public function getLemburByIdAuth(): ?Lembur
    {
        $id = Auth::id() ?? User::first()->id_user;
        return Lembur::with('karyawan')->find($id);
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

    public function getPendingCount(): int
    {
        return Lembur::where('status_validasi', Validasi::Pending->value)->count();
    }

    public function getValidLemburForExport(string $startDate, string $endDate, ?string $departemenId = null)
    {
        $query = Lembur::with(['karyawan.departemen', 'karyawan.outsourcing'])
            ->where('status', Status::Active->value)
            ->where('status_validasi', Validasi::Valid->value)
            ->whereDate('mulai_lembur', '>=', $startDate)
            ->whereDate('selesai_lembur', '<=', $endDate);

        if (!empty($departemenId)) {
            $query->whereHas('karyawan', function ($q) use ($departemenId) {
                $q->where('departemen_id', $departemenId);
            });
        }

        return $query->orderBy('mulai_lembur', 'asc')->get();
    }
}
