<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;
use App\Models\Jadwal;
use App\Services\JadwalService;
use Illuminate\Support\Facades\Auth;
use App\Services\KehadiranService;

class Dashboard extends Component
{
    // iniasiasi property untuk semua fungsi
    public $tipe_kehadiran = null;
    public $shift = null;
    public $jam_masuk = null;
    public $jam_keluar = null; 
    public $waktu_masuk = null;
    public $waktu_keluar = null;
    public $today = null;
    public $jadwal = null;
    public $absen = null;

    public function mount() 
    {
        // mengambil tanggal pada hari ini dengan tipe data string
        $this->today = now()->toDateString();
        // inisiasi objek dari service jadwl dan melakukan fungsi kirim waktu jadwal
        // mengembalikan data berupa data user yang memiliki jadwal dalam rentang waktu hari ini, dan berstatus aktif
        $this->jadwal = (new JadwalService)->kirimWaktuJadwal();
        // inisiasi objek dari service kehadiran dan melakukan fungsi validasi kehadiran
        // mengembalikan data berupa status kehadiran dan waktu masuk dan keluar
        $this->absen = (new KehadiranService)->validasiKehadiran();
        // menyimpan data tipe kehadiran
        $this->tipe_kehadiran = $this->absen['tipe_kehadiran'];
    }
    
    public function render() 
    {
        // melakukan pengecekan apakah user memiliki jadwal 
        if ($this->jadwal) {
            $this->shift = $this->jadwal->shift;
            // melakukan pengecekan apakah shift user memiliki jadwal
            if ($this->shift) {
                $this->jam_masuk = $this->shift->jam_masuk;
                $this->jam_keluar = $this->shift->jam_keluar;

                // melakukan pengecekan apakah user sudah melakukan absensi masuk dan keluar              
                $this->waktu_masuk = $this->absen['waktuMasuk'];
                $this->waktu_keluar = $this->absen['waktuKeluar'];                
            } 
        }

        // mengembalikan data ke view
        return view('livewire.Karyawan.dashboard', [
            'tipe_kehadiran' => $this->tipe_kehadiran,
            'jadwal' => $this->jadwal,
            'shift' => $this->shift,
            'jam_masuk' => $this->jam_masuk,
            'jam_keluar' => $this->jam_keluar,
            'waktu_masuk' => $this->waktu_masuk,
            'waktu_keluar' => $this->waktu_keluar,
            'status_kehadiran' => $this->absen['status_kehadiran'],
        ]);
    }
}