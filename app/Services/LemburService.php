<?php

namespace App\Services;

use App\Models\Lembur;
use App\Enums\Validasi;

class LemburService
{
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
     * Mendapatkan data detail lembur berdasarkan ID.
     */
    public function getLemburById(int $id): ?Lembur
    {
        return Lembur::with('karyawan')->find($id);
    }

    /**
     * Menyetujui pengajuan lembur.
     */
    public function approveLembur(int $id, int $validatorId, string $status_validasi): bool
    {
        $lembur = Lembur::find($id);
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
}
