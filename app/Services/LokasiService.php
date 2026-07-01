<?php

namespace App\Services;

use App\Models\Departemen;
use App\Models\Lokasi;

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
}