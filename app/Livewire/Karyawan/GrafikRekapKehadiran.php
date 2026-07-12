<?php

namespace App\Livewire\Karyawan;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Services\KehadiranService;
use App\Services\UserService;

class GrafikRekapKehadiran extends Component
{
    // membuat variabel public agar bisa diakses di semua fungsi
    public $mode = 'tahunan';
    public $tahun;
    public $bulan;
    public $listTahun = [];
    public $listBulan = [];
    public $kehadiran;

    // fungsi ini untuk mengambil data yang ada di database
    // dan akan di jalankan pertama kali saat komponen di load
    public function mount()
    {
        $this->tahun = date('Y');
        $this->bulan = date('m');

        // Mendapatkan daftar tahun dari data absensi karyawan
        $this->kehadiran = (new KehadiranService)->ambilKehadiranRange(null, null);
        $years = $this->kehadiran;

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

    // melakukan fungsi updated untuk memanggil fungsi getChartData ketika ada perubahan pada properti mode, tahun, atau bulan
    // input propertyName adalah nama properti yang diubah
    // output berupa void yang mengirimkan event ke frontend untuk memperbarui chart dengan data baru
    public function updated($propertyName)
    {
        // Emit event to update chart
        if (in_array($propertyName, ['mode', 'tahun', 'bulan'])) {
            $this->dispatch('refreshChart', data: $this->getChartData());
        }
    }

    // melakukan pengambilan data kehadiran karyawan berdasarkan mode, tahun, dan bulan yang dipilih
    // output berupa array yang berisi label dan data kehadiran karyawan
    public function getChartData()
    {
        $karyawanId = Auth::user()->id_user ?? null;
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

            $kehadiran = (new KehadiranService)->ambilKehadiranRange($tahun, null);
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

        $jumlahHari = \Carbon\Carbon::createFromDate($tahun, $this->bulan, 1)->daysInMonth;

        $labels = range(1, $jumlahHari);

        $dataHadir = array_fill(0, $jumlahHari, 0);
        $dataIzin = array_fill(0, $jumlahHari, 0);
        $dataSakit = array_fill(0, $jumlahHari, 0);
        $dataMankir = array_fill(0, $jumlahHari, 0);
        $dataTerlambat = array_fill(0, $jumlahHari, 0);

        $kehadiran = (new KehadiranService)->ambilKehadiranRange($tahun, $this->bulan);


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

    protected $listeners = ['refresh-dashboard' => 'onDashboardRefresh'];

    // fungsi ini akan dijalankan ketika event 'refresh-dashboard' diterima
    // dan akan memanggil fungsi getChartData untuk memperbarui data chart
    public function onDashboardRefresh()
    {
        $this->dispatch('refreshChart', data: $this->getChartData());
    }

    public function render()
    {
        return view('livewire.Karyawan.grafik-rekap-kehadiran', [
            'chartData' => $this->getChartData(),
        ]);
    }
}
