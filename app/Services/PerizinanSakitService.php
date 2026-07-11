<?php

namespace App\Services;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\PerizinanSakit;
use App\Models\User;
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
        $this->id = Auth::check() ? Auth::user()->id_user : null;
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

    public function ambilPerizinanSakitUserLogin()
    {
        return PerizinanSakit::where('karyawan_id', Auth::id() ?? User::first()->id_user);
    }

    public function ambilPerizinanSakitSemuaKaryawan($deptId)
    {
        return PerizinanSakit::query()
            ->whereHas('karyawan', function ($q) use ($deptId) {
                $q->where('role', UserRole::Karyawan->value)
                    ->where('status', Status::Active->value);
                if ($deptId) {
                    $q->where('departemen_id', $deptId);
                }
        });
    }

    /**
     * Mengambil laporan perizinan karyawan yang difilter berdasarkan pencarian nama dan tanggal.
     *
     * @param int|null $deptId
     * @param string|null $search
     * @param string|null $filterDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function ambilLaporanPerizinanFiltered($deptId, ?string $search = null, ?string $filterDate = null)
    {
        $query = PerizinanSakit::query()
            ->with('karyawan.departemen')
            ->whereHas('karyawan', function ($q) use ($deptId, $search) {
                $q->where('role', UserRole::Karyawan->value)
                  ->where('status', Status::Active->value);
                if ($deptId) {
                    $q->where('departemen_id', $deptId);
                }
                if ($search) {
                    $q->where('nama_lengkap', 'like', '%' . $search . '%');
                }
            });

        if ($filterDate) {
            $query->whereDate('tanggal_pengajuan', $filterDate);
        }

        return $query->orderBy('tanggal_pengajuan', 'desc')->get();
    }

    /**
     * Dapatkan detail perizinan berdasarkan ID beserta relasi karyawan, departemen, dan outsourcing.
     */
    public function getPerizinanById(int $id): ?PerizinanSakit
    {
        return PerizinanSakit::with('karyawan.departemen', 'karyawan.outsourcing')->find($id);
    }

    /**
     * Update status perizinan (disetujui / ditolak).
     */
    public function updateStatus(int $id, string $status): ?PerizinanSakit
    {
        $perizinan = PerizinanSakit::with('karyawan')->find($id);
        if ($perizinan) {
            $perizinan->update(['status' => $status]);
        }
        return $perizinan;
    }

    /**
     * Ambil data perizinan yang menunggu validasi (menunggu).
     */
    public function getPengajuanMenunggu(string $search = '')
    {
        return PerizinanSakit::with('karyawan.departemen', 'karyawan.outsourcing')
            ->where('status', 'menunggu')
            ->whereHas('karyawan', function ($q) use ($search) {
                if ($search) {
                    $keyword = '%' . $search . '%';
                    $q->where('nama_lengkap', 'like', $keyword);
                }
                $q->where('role', UserRole::Karyawan->value);
            })
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();
    }

    /**
     * Ambil riwayat perizinan yang sudah divalidasi hari ini.
     */
    public function getRiwayatValidasiHariIni()
    {
        return PerizinanSakit::with('karyawan.departemen')
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->whereDate('updated_at', today())
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function getApprovedPerizinanByRange($userId, $startDate, $endDate)
    {
        return PerizinanSakit::where('karyawan_id', $userId)
            ->where('status', 'disetujui')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where('tanggal_mulai', '<=', $endDate)
                  ->where('tanggal_selesai', '>=', $startDate);
            })
            ->get();
    }
}
