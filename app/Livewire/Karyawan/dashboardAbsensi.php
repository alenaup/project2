<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;

class dashboardAbsensi extends Component
{
    public $jenisAbsensi;

    public $waktu;

    public $latitude;

    public $longitude;

    public $jarak;

    public function simpanAbsensi() 
    {
        dd($this->waktu);
    }

    public function render()
    {
        return view('livewire.Karyawan.dashboardAbsensi');
    }
}
