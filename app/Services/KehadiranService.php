<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\Kehadiran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class KehadiranService
{
    // array untuk menyimpan nama defauld kehadiran
    private $nama_kehadiran = [
        1 => 'Hadir',
        2 => 'Sakit',
        3 => 'Mankir',
        4 => 'Cuti',
        5 => 'Izin',
        6 => 'Terlambat',
    ];

    // fungsi untuk memvalidasi kehadiran user pada hari ini
    // mengembalikan status kehadiran, waktu masuk dan waktu keluar

    // memiliki dua pengembalian data memiliki absensi dan tidak memiliki absensi
    public function validasiKehadiran()
    {
        // mengecek apakah user sudah melakukan absensi
        $data = $this->cekKehadiran();

        // mengembalikan data jika belum melakukan absensi
        if (! $data) {
            return [
                'tipe_kehadiran' => null,
                'status_kehadiran' => false,
                'waktuMasuk' => 'belum melakukan absensi masuk',
                'waktuKeluar' => 'belum melakukan absensi keluar',
            ];
        }

        // melakukan pengecekan berdasarkan tipe kehadiran
        // mengisi properti nama_kehadiran berdasarkan tipe kehadiran yang dimiliki
        switch ($data->tipe_kehadiran_id) {
            case 1:
                $nama_kehadiran = $this->nama_kehadiran[1];
                break;
            case 2:
                $nama_kehadiran = $this->nama_kehadiran[2];
                break;
            case 3:
                $nama_kehadiran = $this->nama_kehadiran[3];
                break;
            case 4:
                $nama_kehadiran = $this->nama_kehadiran[4];
                break;
            case 5:
                $nama_kehadiran = $this->nama_kehadiran[5];
                break;
            case 6:
                $nama_kehadiran = $this->nama_kehadiran[6];
                break;
            default:
                $nama_kehadiran = 'Belum Melakukan Absensi';
                break;

        }
        // mengembalikan data kehadiran
        /*  data yang dikembalikan adalah
        - tipe kehadiran
        - status kehadiran
        - waktu masuk
        - waktu keluar
        */
        return [
            'tipe_kehadiran' => $nama_kehadiran,
            'status_kehadiran' => true,
            'waktuMasuk' => $data->waktu_masuk,
            'waktuKeluar' => $data->waktu_keluar,
        ];
    }

    // method yang berungsi mengecek kehadiran pada hari ini
    // mengembalikan data kehadiran satu karyawan
    public function cekKehadiran()
    {
        // mengambil user yang sedang login
        // menggunakan service UserService untuk mempermudah dalam mendapatkan data user
        $user = (new UserService)->getUserById();
        // mengambil tanggal hari ini
        $tanggal = now()->toDateString();

        // melakukan query ke database berdasarkan karyawan_id dan tanggal
        $query = Kehadiran::select('id_kehadiran', 'tipe_kehadiran_id', 'waktu_masuk', 'waktu_keluar')
            ->where('karyawan_id', $user)->where('tanggal', $tanggal)->first();
        return $query;
    }
    
    // method untuk mengecek kehadiran banyak karyawan berdasarkan tipe kehadiran dan bulan dan tahun
    /* mengembalikan data
    - jumlah int data yang cocok */
    public function cekKehadiranBanyakKaryawan($tipeKehadiran, $arrayKaryawanIds, $bulan, $tahun)
    {
        // melakukan pengecekan berdasarkan tipe kehadiran
        $query = Kehadiran::whereHas('tipeKehadiran', function ($q) use ($tipeKehadiran) {
            $q->where('status_kehadiran', $tipeKehadiran);
        })
            ->whereIn('karyawan_id', $arrayKaryawanIds)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->count();
        return $query;
    }

    // fungsi melakukan absensi kehadiran masuk kerja
    public function absenMasuk(array $data)
    {
        // mengambil status kehadiran pada hari ini dengan method cekKehadiran
        $kehadiran = $this->cekKehadiran();
        // melakukan pengecekan apakah sudah melakukan absensi masuk
        if ($kehadiran && $kehadiran->waktu_masuk) {
            // mengembalikan info bahwa user sudah melakukan absensi masuk
            return [
                'success' => false,
                'message' => 'Anda sudah melakukan absensi masuk',
            ];
        }

        // menentukan tipe kehadiran
        $tipeId = $this->tentukanTipeKehadiran(
            $data['waktu'],
            $data['jamMasuk'],
            $data['toleransi'],
        );

        // Cek batas maksimal absensi masuk: tidak boleh setelah jam keluar shift
        if (! empty($data['jamKeluar'])) {
            $batasMaksimal = Carbon::parse(now()->toDateString() . ' ' . $data['jamKeluar']);
            if (Carbon::parse($data['waktu'])->greaterThan($batasMaksimal)) {
                return [
                    'success' => false,
                    'message' => 'Absensi masuk tidak dapat dilakukan, waktu shift sudah berakhir pukul '
                                 . Carbon::parse($data['jamKeluar'])->format('H:i') . '.',
                ];
            }
        }

        // mengecek aapakah hari ini sudah memiliki status
        if ($kehadiran && $kehadiran->tipe_kehadiran_id != 1) {
            return [
                'success' => false,
                'message' => 'Anda sudah memiliki kehadiran bukan absensi kerja',
            ];
        }

        // memperbarui data kehadiran
        if ($kehadiran) {
            $kehadiran->update([
                'waktu_masuk' => $data['waktu'],
                'latitude_masuk' => $data['latitude'],
                'longitude_masuk' => $data['longitude'],
                'tipe_kehadiran_id' => $tipeId,
            ]);
        } else { // membuat data kehadiran baru
            Kehadiran::create([
                'tanggal' => now()->toDateString(),
                'waktu_masuk' => $data['waktu'],
                'waktu_keluar' => null,
                'latitude_masuk' => $data['latitude'],
                'longitude_masuk' => $data['longitude'],
                'jadwal_id' => $data['jadwalId'],
                'tipe_kehadiran_id' => $tipeId,
                'rekapan_kehadiran_id' => $data['rekapId'],
                'karyawan_id' => Auth::id(),
            ]);
        }

        return [
            'success' => true,
            'message' => 'Absensi masuk berhasil',
        ];
    }

    // fungsi melakukan absensi kehadiran kelluar kerja
    public function absenKeluar(array $data)
    {
        $kehadiran = $this->cekKehadiran();
        if (! $kehadiran || ! $kehadiran->waktu_masuk) {
            return [
                'success' => false,
                'message' => 'Belum melakukan absen masuk',
            ];
        }

        if ($kehadiran->waktu_keluar) {
            return [
                'success' => false,
                'message' => 'Sudah melakukan absen keluar',
            ];
        }

        $kehadiran->update([
            'waktu_keluar' => $data['waktu'],
            'latitude_keluar' => $data['latitude'],
            'longitude_keluar' => $data['longitude'],
        ]);

        return [
            'success' => true,
            'message' => 'Absen keluar berhasil',
        ];

    }

    // method yang berfungsi mengecek tipe kehadiran dan menentukan batas toleransi keterlambatan
    public function tentukanTipeKehadiran($waktu, $jamMasuk, $toleransiTelat)
    {
        // menghitung batas toleransi keterlambatan
        $batas = Carbon::parse(
            now()->toDateString().' '.$jamMasuk
        );

        // tambahkan toleransi keterlambatan jika ada
        if ($toleransiTelat) {
            $batas->addSeconds(
                Carbon::parse($toleransiTelat)->secondsSinceMidnight()
            );
        }

        // kembalikan tipe kehadiran: 6 = Terlambat, 1 = Hadir
        return Carbon::parse($waktu)
            ->greaterThan($batas)
            ? 6
            : 1;
    }

    public function ambilKehadiranRange($tahun, $bulan)
    {
        if ($tahun == null && $bulan == null) {
            $query = Kehadiran::where('karyawan_id', Auth::user()->id_user)
                ->selectRaw('YEAR(tanggal) as tahun')
                ->groupBy('tahun')
                ->orderBy('tahun', 'desc')
                ->pluck('tahun')
                ->toArray();
        } elseif ($tahun && $bulan == null) {
            $query = Kehadiran::where('karyawan_id', Auth::user()->id_user)
                ->whereYear('tanggal', $tahun)
                ->get();
        } elseif ($tahun == null && $bulan) {
            $query = Kehadiran::where('karyawan_id', Auth::user()->id_user)
                ->whereMonth('tanggal', $bulan)
                ->get();
        } elseif ($tahun && $bulan) {
            $query = Kehadiran::where('karyawan_id', Auth::user()->id_user)
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->get();
        }
        return $query;
    }

    
    public function ambilBanyaklKehadiranRange(
        $karyawanIds,
        int $bulan,
        int $tahun
    ) {
        $query = Kehadiran::whereIn('karyawan_id', $karyawanIds)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();
        return $query;
    }

    public function ambilBanyakKehadiranByDateRange($karyawanIds, $startDate, $endDate)
    {
        $query = Kehadiran::whereIn('karyawan_id', $karyawanIds)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();
        return $query;
    }

    public function totalHadir(
        array $karyawanIds,
        int $tahun,
        int $bulan
    ): int {

        $query = Kehadiran::query()
            ->whereIn('karyawan_id', $karyawanIds)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->whereHas('tipeKehadiran', function ($query) {
                $query->where('status_kehadiran', 'hadir');
            })
            ->count();
        return $query;
    }

    public function totalHadirByDateRange(array $karyawanIds, $startDate, $endDate): int {
        $query = Kehadiran::query()
            ->whereIn('karyawan_id', $karyawanIds)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->whereHas('tipeKehadiran', function ($query) {
                $query->where('status_kehadiran', 'hadir');
            })
            ->count();
        return $query;
    }

    public function cekKehadiranBanyakKaryawanByDateRange($tipeKehadiran, $arrayKaryawanIds, $startDate, $endDate)
    {
        $query = Kehadiran::whereHas('tipeKehadiran', function ($q) use ($tipeKehadiran) {
            $q->where('status_kehadiran', $tipeKehadiran);
        })
            ->whereIn('karyawan_id', $arrayKaryawanIds)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->count();
        return $query;
    }
}
