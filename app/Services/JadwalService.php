<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\Jadwal;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JadwalService
{
    /* fungsi untuk mengambil jadwal karyawann berdasarkan user yg sedang login
    dan menyaring berdasarkan tanggal hari ini, user yg terdaftar belum tentu punya jadwal */
    public function kirimWaktuJadwal()
    {
        // data yyang dikembalikan adalah id_jadwal dan shift_id
        // menyaring data berdasarkan tanggal hari ini
        return Auth::user()->jadwal()
            ->select([
                'id_jadwal',
                'shift_id',
                'toleransi_telat',
            ]) // melakukan relasi dengan tabel shift, mengambil jam masuk dan jam keluar
            ->with([
                'shift:id_shift,jam_masuk,jam_keluar',
            ])
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_akhir', '>=', now())
            ->where('status', Status::Active->value)
            ->first();
    }

    public function ambilShift()
    {
        return Shift::whereIn('id_shift', [1, 2, 3])
            ->orderBy('id_shift', 'asc')
            ->get()
            ->toArray();
    }

    public function updateShift($editingShiftId, $jam_masuk, $jam_keluar)
    {
        $shift = Shift::find($editingShiftId);

        if ($shift) {
            $shift->update([
                'jam_masuk'  => $jam_masuk . ':00', // tambahkan :00 detik agar cocok dengan database time
                'jam_keluar' => $jam_keluar . ':00',
            ]);
            return true;
        }
    }


    public function getShiftData($shift)
    {
        return Shift::find($shift);
    }

    /**
     * Menyelesaikan jadwal yang tumpang tindih untuk satu karyawan.
     * Logika ini memotong, membelah (split), atau menghapus jadwal lama yang beririsan dengan range tanggal baru.
     */
    public function resolveOverlappingJadwal($userId, $startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        $startDateStr = $startDate->toDateString();
        $endDateStr = $endDate->toDateString();

        $user = User::find($userId);
        if (!$user) {
            return;
        }

        // Ambil semua jadwal user yang tumpang tindih
        $overlappingJadwals = $user->jadwal()
            ->where(function ($query) use ($startDateStr, $endDateStr) {
                $query->where('tanggal_mulai', '<=', $endDateStr)
                      ->where('tanggal_akhir', '>=', $startDateStr);
            })
            ->get();

        foreach ($overlappingJadwals as $j) {
            $jStart = Carbon::parse($j->tanggal_mulai);
            $jEnd = Carbon::parse($j->tanggal_akhir);

            // Case 1: Jadwal lama berada sepenuhnya di dalam range baru -> Hapus/Detach
            if ($jStart->gte($startDate) && $jEnd->lte($endDate)) {
                $user->jadwal()->detach($j->id_jadwal);
                if ($j->user()->count() === 0) {
                    $j->delete();
                }
            }
            // Case 4: Jadwal lama menutupi seluruh range baru -> Split jadi 2 (sebelum dan sesudah)
            elseif ($jStart->lt($startDate) && $jEnd->gt($endDate)) {
                $oldEnd = $jEnd->toDateString();

                $j->update([
                    'tanggal_akhir' => $startDate->copy()->subDay()->toDateString()
                ]);

                $newJadwalAfter = Jadwal::create([
                    'status' => $j->status,
                    'tanggal_mulai' => $endDate->copy()->addDay()->toDateString(),
                    'tanggal_akhir' => $oldEnd,
                    'shift_id' => $j->shift_id,
                    'dibuat_oleh' => $j->dibuat_oleh,
                    'nama_periode' => $j->nama_periode,
                    'toleransi_telat' => $j->toleransi_telat,
                ]);
                $user->jadwal()->attach($newJadwalAfter->id_jadwal);
            }
            // Case 2: Tumpang tindih di bagian awal (tanggal_akhir lama terpotong oleh start_date baru)
            elseif ($jStart->lt($startDate) && $jEnd->gte($startDate) && $jEnd->lte($endDate)) {
                $j->update([
                    'tanggal_akhir' => $startDate->copy()->subDay()->toDateString()
                ]);
            }
            // Case 3: Tumpang tindih di bagian akhir (tanggal_mulai lama terpotong oleh end_date baru)
            elseif ($jStart->gte($startDate) && $jStart->lte($endDate) && $jEnd->gt($endDate)) {
                $j->update([
                    'tanggal_mulai' => $endDate->copy()->addDay()->toDateString()
                ]);
            }
        }
    }

    public function getJadwalKaryawanPaginated($deptId, $startDate, $endDate, $perPage = 10)
    {
        $query = User::where('role', \App\Enums\UserRole::Karyawan->value)
            ->with(['jadwal' => function($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->where('tanggal_mulai', '<=', $endDate)
                      ->where('tanggal_akhir', '>=', $startDate);
                })->with('shift');
            }]);

        if ($deptId) {
            $query->where('departemen_id', $deptId);
        }

        return $query->paginate($perPage);
    }

    public function getAllShifts()
    {
        return Shift::all();
    }

    public function createJadwalForUser($userId, array $data, $createdBy)
    {
        $this->resolveOverlappingJadwal($userId, $data['start_date'], $data['end_date']);

        $jadwal = Jadwal::create([
            'status' => Status::Active->value,
            'tanggal_mulai' => $data['start_date'],
            'tanggal_akhir' => $data['end_date'],
            'shift_id' => $data['shift_id'],
            'dibuat_oleh' => $createdBy,
            'nama_periode' => 'Periode ' . Carbon::parse($data['start_date'])->format('M Y'),
        ]);

        $user = User::find($userId);
        if ($user) {
            $user->jadwal()->attach($jadwal->id_jadwal);
            return $jadwal;
        }
        return null;
    }

    public function getJadwalUserByRange($userId, $startDate, $endDate)
    {
        $user = User::find($userId);
        if (!$user) {
            return collect();
        }
        return $user->jadwal()
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where('tanggal_mulai', '<=', $endDate)
                  ->where('tanggal_akhir', '>=', $startDate);
            })
            ->with('shift')
            ->get();
    }
}
