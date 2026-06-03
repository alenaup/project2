<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;
use App\Models\Jadwal;
use Illuminate\Support\Facades\Auth;

class dashboard extends Component
{
    public function render()
    {
        $today = now()->toDateString();

        $jadwal = Auth::user()->jadwal()
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_akhir', '>=', now())
            ->first();

        $shift = null;
        $jam_masuk = null;
        $jam_keluar = null; 

        if ($jadwal) {
            $shift = $jadwal->shift;

            if ($shift) {
                $jam_masuk = $shift->jam_masuk;
                $jam_keluar = $shift->jam_keluar;
            } 
        }

        return view('livewire.Karyawan.dashboard', [
            'jadwal' => $jadwal,
            'shift' => $shift,
            'jam_masuk' => $jam_masuk,
            'jam_keluar' => $jam_keluar,
        ]);
    }
}