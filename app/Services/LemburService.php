<?php

namespace App\Services;

use App\Models\Lembur;
use App\Enums\Validasi;

class LemburService
{
    /**
     * Mendapatkan daftar pengajuan lembur per departemen dengan paginasi.
     */
    public function getLemburListByDepartemenPaginated(?int $deptId, int $perPage = 20)
    {
        if (!$deptId) {
            return collect();
        }

        return Lembur::with('karyawan')
            ->whereHas('karyawan', function ($query) use ($deptId) {
                $query->where('departemen_id', $deptId);
            })
            ->latest('created_at')
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
    public function approveLembur(int $id, int $validatorId): bool
    {
        $lembur = Lembur::find($id);
        if ($lembur && $lembur->status_validasi === Validasi::Pending->value) {
            return $lembur->update([
                'status_validasi' => Validasi::Valid->value,
                'pemvalidasi_id' => $validatorId,
            ]);
        }
        return false;
    }

    /**
     * Menolak pengajuan lembur.
     */
    public function rejectLembur(int $id, int $validatorId): bool
    {
        $lembur = Lembur::find($id);
        if ($lembur && $lembur->status_validasi === Validasi::Pending->value) {
            return $lembur->update([
                'status_validasi' => Validasi::Invalid->value,
                'pemvalidasi_id' => $validatorId,
            ]);
        }
        return false;
    }

    /**
     * Menyetujui semua pengajuan lembur berstatus pending di departemen tertentu.
     */
    public function approveAllPendingLembur(?int $deptId, int $validatorId): int
    {
        if (!$deptId) {
            return 0;
        }

        $pendingLemburs = Lembur::whereHas('karyawan', function ($query) use ($deptId) {
                $query->where('departemen_id', $deptId);
            })
            ->where('status_validasi', Validasi::Pending->value)
            ->get();

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