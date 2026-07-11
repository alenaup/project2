<?php

namespace App\Livewire\AdminOutsourcing;

use Livewire\Component;
use App\Services\KehadiranService;
use App\Services\UserService;

class Grafik extends Component
{
    // membuat variabel public agar bisa diakses di semua fungsi
    public $mode = 'tahunan';
    public $tahun;
    public $bulan;
    public array $listTahun = [];
    public array $listBulan = [];
    public $kehadiran;

    // fungsi ini untuk mengambil data yang ada di database
    // dan akan di jalankan pertama kali saat komponen di load
    public function mount(UserService $userService, KehadiranService $kehadiranService)
    {
        $this->tahun = date('Y');
        $this->bulan = date('m');

        // Mendapatkan daftar tahun dari data absensi karyawan outsourcing
        $karyawanIds = $userService->getOutsourcing();

        $years = $kehadiranService->getYearsListByKaryawanIds($karyawanIds);

        if (empty($years)) {
            $years = [date('Y')];
        }
        $this->listTahun = $years;

        $this->listBulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];
    }

    // melakukan auto update data jika memiliki data baru pada kolom
    // output adalah pemanggilan method getChartData
    public function updated($propertyName)
    {
        // Emit event to update chart
        if (in_array($propertyName, ['mode', 'tahun', 'bulan'])) {
            $this->dispatch('refreshChart', data: $this->getChartData());
        }
    }

    // mengirim data kehadiran kkaryawan yang memiliki outsourcing yang sama 
    // output adalah data array kehadiran karaywan 
    public function getChartData()
    {
        $userService = app(UserService::class);
        $kehadiranService = app(KehadiranService::class);

        $karyawanIds = $userService->getOutsourcing();
        $tahun = $this->tahun;

        // ==========================
        // MODE TAHUNAN
        // ==========================
        if ($this->mode === 'tahunan') {

            $labels = [
                'Jan', 'Feb', 'Mar',
                'Apr', 'Mei', 'Jun',
                'Jul', 'Agu', 'Sep',
                'Okt', 'Nov', 'Des',
            ];

            $dataHadir = array_fill(0, 12, 0);
            $dataIzin = array_fill(0, 12, 0);
            $dataSakit = array_fill(0, 12, 0);
            $dataMankir = array_fill(0, 12, 0);
            $dataTerlambat = array_fill(0, 12, 0);

            $kehadiran = $kehadiranService->getKehadiranByKaryawanIdsAndYear($karyawanIds, $tahun);

            foreach ($kehadiran as $absen) {

                $bulanIndex = (int) date('m', strtotime($absen->tanggal)) - 1;

                switch ($absen->tipe_kehadiran_id) {

                    case 1:
                        $dataHadir[$bulanIndex]++;
                        break;

                    case 5:
                        $dataIzin[$bulanIndex]++;
                        break;

                    case 2:
                        $dataSakit[$bulanIndex]++;
                        break;

                    case 3:
                        $dataMankir[$bulanIndex]++;
                        break;
                    case 6:
                        $dataTerlambat[$bulanIndex]++;
                        break;
                }
            }

            return [
                'labels' => $labels,
                'hadir' => $dataHadir,
                'izin' => $dataIzin,
                'sakit' => $dataSakit,
                'mankir' => $dataMankir,
                'terlambat' => $dataTerlambat,
            ];

        }

        // ==========================
        // MODE BULANAN
        // ==========================

        $jumlahHari = cal_days_in_month(
            CAL_GREGORIAN,
            $this->bulan,
            $tahun
        );

        $labels = range(1, $jumlahHari);

        $dataHadir = array_fill(0, $jumlahHari, 0);
        $dataIzin = array_fill(0, $jumlahHari, 0);
        $dataSakit = array_fill(0, $jumlahHari, 0);
        $dataMankir = array_fill(0, $jumlahHari, 0);
        $dataTerlambat = array_fill(0, $jumlahHari, 0);

        $kehadiran = $kehadiranService->ambilBanyaklKehadiranRange($karyawanIds, $this->bulan, $tahun);


        foreach ($kehadiran as $absen) {

            $hariIndex = (int) date('d', strtotime($absen->tanggal)) - 1;

            switch ($absen->tipe_kehadiran_id) {

                case 1:
                    $dataHadir[$hariIndex]++;
                    break;

                case 5:
                    $dataIzin[$hariIndex]++;
                    break;

                case 2:
                    $dataSakit[$hariIndex]++;
                    break;

                case 3:
                    $dataMankir[$hariIndex]++;
                    break;
                case 6:
                    $dataTerlambat[$hariIndex]++;
                    break;
            }

        }

        return [
            'labels' => $labels,
            'hadir' => $dataHadir,
            'izin' => $dataIzin,
            'sakit' => $dataSakit,
            'mankir' => $dataMankir,
            'terlambat' => $dataTerlambat,
        ];
    }

    public function render()
    {
        return view('livewire.admin-outsourcing.grafik', [
            'chartData' => $this->getChartData(),
        ]);
    }
}
