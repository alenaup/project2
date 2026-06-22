<?php

namespace App\Services;

use App\Models\PerizinanSakit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PerizinanSakitService
{
    public $id = "";

    public function updatePerizinan(
        $id,
        $tanggal_mulai,
        $tanggal_selesai,
        $keterangan,
        $file = null
    ) {

        $perizinan = PerizinanSakit::find($id);

        if (!$perizinan) {
            return false;
        }

        // update data biasa
        $perizinan->tanggal_mulai = $tanggal_mulai;
        $perizinan->tanggal_selesai = $tanggal_selesai;
        $perizinan->keterangan = $keterangan;


        // jika upload file baru
        if ($file) {

            // hapus file lama
            if (
                $perizinan->file_surat &&
                Storage::disk('public')
                    ->exists($perizinan->file_surat)
            ) {
                Storage::disk('public')
                    ->delete($perizinan->file_surat);
            }
            // simpan file baru
            $path = $file->store(
                'surat_sakit',
                'public'
            );
            $perizinan->file_surat = $path;
        }


        $perizinan->save();


        return $perizinan;
    }

    public function __construct()
    {
        $this->id = Auth::user()->id_user;
    }

    public function ambilStatus($id_perizinan)
    {
        $query = PerizinanSakit::where('id_perizinan', $id_perizinan)
            ->where('karyawan_id', $this->id)
            ->where('status', 'menunggu')
            ->first();
        return $query;
    }

    public function membuatFormulir($tanggal_mulai, $tanggal_selesai, $keterangan, $file_surat)
    {
        $query = PerizinanSakit::create([
            'karyawan_id'       => $this->id,
            'tanggal_mulai'     => $tanggal_mulai,
            'tanggal_selesai'   => $tanggal_selesai,
            'keterangan'        => $keterangan,
            'file_surat'        => $file_surat,
            'status'            => 'menunggu',
            'tanggal_pengajuan' => now(),
        ]);
        return $query;
    }
}
