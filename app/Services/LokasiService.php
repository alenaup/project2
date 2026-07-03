<?php

namespace App\Services;

use App\Models\Departemen;
use App\Models\Lokasi;
use Illuminate\Support\Facades\Auth;

class LokasiService
{
    public function simpanLokasi($departemenId, $data)
    {
        $departemen = Departemen::find($departemenId);

        if (!$departemen) {
            return false;
        }

        if ($departemen->lokasi_id) {

            $lokasi = Lokasi::find($departemen->lokasi_id);
            if ($lokasi) {
                $lokasi->update([
                    'nama_lokasi' => $data['nama_lokasi'],
                    'latitude'    => $data['latitude'],
                    'longitude'   => $data['longitude'],
                    'radius'      => $data['radius'],
                ]);
            }

        } else {

            $lokasi = Lokasi::create([
                'nama_lokasi' => $data['nama_lokasi'],
                'latitude'    => $data['latitude'],
                'longitude'   => $data['longitude'],
                'radius'      => $data['radius'],
            ]);
            $departemen->update([
                'lokasi_id' => $lokasi->id_lokasi
            ]);
        }

        return true;
    }

    public function getLokasiDepartemenUser()
    {
        if (!Auth::check()) {
            return null;
        }
        $user = Auth::user();
        if ($user && $user->departemen_id) {
            return Departemen::with('lokasi')->find($user->departemen_id);
        }
    }
}
