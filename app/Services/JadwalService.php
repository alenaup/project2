<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\Jadwal;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
}
