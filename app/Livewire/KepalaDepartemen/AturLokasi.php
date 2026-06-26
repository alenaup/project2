<?php

namespace App\Livewire\KepalaDepartemen;

use Livewire\Component;
use App\Models\Lokasi;
use App\Models\Departemen;
use Illuminate\Support\Facades\Auth;

class AturLokasi extends Component
{
    public $departemen_id;
    public $nama_departemen;
    
    // Properties untuk Lokasi
    public $nama_lokasi = '';
    public $latitude = 1.05450000;
    public $longitude = 104.00410000;
    public $radius = 100; // default 100 meter

    public function mount()
    {
        // Ambil departemen milik kepala departemen yang login
        $user = Auth::user();
        if ($user && $user->departemen_id) {
            $departemen = Departemen::with('lokasi')->find($user->departemen_id);
            
            if ($departemen) {
                $this->departemen_id = $departemen->id_departemen;
                $this->nama_departemen = $departemen->nama_departemen;
                
                if ($departemen->lokasi) {
                    $this->nama_lokasi = $departemen->lokasi->nama_lokasi;
                    $this->latitude = $departemen->lokasi->latitude;
                    $this->longitude = $departemen->lokasi->longitude;
                    $this->radius = $departemen->lokasi->radius;
                } else {
                    $this->nama_lokasi = 'Lokasi ' . $departemen->nama_departemen;
                }
            }
        }
    }

    public function updateLokasi($lat, $lng, $rad)
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
        $this->radius = $rad;
    }

    public function simpan()
    {
        $this->validate([
            'nama_lokasi' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:10',
        ], [
            'nama_lokasi.required' => 'Nama lokasi wajib diisi.',
            'radius.min' => 'Radius minimal 10 meter.',
        ]);

        $departemen = Departemen::find($this->departemen_id);
        
        if (!$departemen) {
            session()->flash('error', 'Departemen tidak ditemukan.');
            return;
        }

        if ($departemen->lokasi_id) {
            // Update existing lokasi
            $lokasi = Lokasi::find($departemen->lokasi_id);
            if ($lokasi) {
                $lokasi->update([
                    'nama_lokasi' => $this->nama_lokasi,
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'radius' => $this->radius,
                ]);
            }
        } else {
            // Create new lokasi
            $lokasi = Lokasi::create([
                'nama_lokasi' => $this->nama_lokasi,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'radius' => $this->radius,
            ]);

            $departemen->update([
                'lokasi_id' => $lokasi->id_lokasi
            ]);
        }

        session()->flash('success', 'Lokasi absensi berhasil disimpan!');
    }

    public function render()
    {
        return view('livewire.kepala-departemen.atur-lokasi');
    }
}
