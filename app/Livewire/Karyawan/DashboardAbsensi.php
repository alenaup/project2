<?php

namespace App\Livewire\Karyawan;

use App\Services\JadwalService;
use App\Services\KehadiranService;
use App\Services\RekapService;
use Livewire\Component;

class DashboardAbsensi extends Component
{
    // ── Input dari frontend ────────────────────────────────────────────────
    public string $jenisAbsensi = 'masuk';
    public ?string $waktu = null;
    public ?string $latitude = null;
    public ?string $longitude = null;
    public ?float $jarak = null;

    // ── Data jadwal aktif (diisi saat mount) ──────────────────────────────
    public ?int $jadwalId = null;
    public ?string $jamMasuk = null;   // HH:MM:SS dari shift
    public ?string $jamKeluar = null;   // HH:MM:SS dari shift
    public ?string $toleransiTelat = null;   // HH:MM:SS
    public bool $sudahAbsenMasuk = false;
    public bool $sudahAbsenKeluar = false;
    public bool $adaJadwal = false;
    public ?string $pesanJadwal = null;
    public ?int $rekapId = null;

    // ── Status kehadiran non-absensi hari ini (sakit/izin/cuti/mankir) ────
    public ?string $tipeKehadiranHariIni = null;   // nilai string dari enum, mis: 'sakit', 'izin'
    public bool $kehadiranSudahTerisi = false;      // true jika ada status bukan hadir/terlambat

    // ──────────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        // Cari jadwal yang aktif hari ini untuk karyawan ini
        $jadwal = (new JadwalService)->kirimWaktuJadwal();

        if (! $jadwal) {
            $this->adaJadwal = false;
            $this->pesanJadwal = 'Tidak ada jadwal kerja hari ini.';

            return;
        }

        $this->adaJadwal = true;
        $this->jadwalId = $jadwal->id_jadwal;
        $this->toleransiTelat = $jadwal->toleransi_telat;

        if ($jadwal->shift) {
            $this->jamMasuk = $jadwal->shift->jam_masuk;
            $this->jamKeluar = $jadwal->shift->jam_keluar;
        }

        // Cek apakah sudah absen masuk hari ini
        $kehadiranHariIni = (new KehadiranService)->cekKehadiran();

        if ($kehadiranHariIni) {
            $this->sudahAbsenMasuk = ! is_null($kehadiranHariIni->waktu_masuk);
            $this->sudahAbsenKeluar = ! is_null($kehadiranHariIni->waktu_keluar);
            // Default ke keluar jika sudah masuk tapi belum keluar
            if ($this->sudahAbsenMasuk && ! $this->sudahAbsenKeluar) {
                $this->jenisAbsensi = 'keluar';
            }
        }

        // mengecek statuus kehadiran pada rekap kehadiran
        $rekap = (new RekapService)->ambilRekapDetail();

        if ($rekap) {
            $this->rekapId = $rekap->id_rekapan;
        }

        // ── Deteksi status kehadiran non-absensi (sakit / izin / cuti / mankir) ──
        // Jika rekaman kehadiran hari ini memiliki tipe selain Hadir & Terlambat,
        // form absensi harus diblokir dan karyawan diberi notifikasi.
        $kehadiranHariIniDetail = (new KehadiranService)->cekKehadiran();
        if ($kehadiranHariIniDetail) {
            $tipeId = $kehadiranHariIniDetail->tipe_kehadiran_id;
            // ID: 2=Sakit, 3=Mankir, 4=Cuti, 5=Izin (bukan 1=Hadir, bukan 6=Terlambat)
            $tipeNonAbsensi = [2, 3, 4, 5];
            if (in_array($tipeId, $tipeNonAbsensi)) {
                $this->kehadiranSudahTerisi = true;
                $namaMap = [
                    2 => 'Sakit',
                    3 => 'Mankir',
                    4 => 'Cuti',
                    5 => 'Izin',
                ];
                $this->tipeKehadiranHariIni = $namaMap[$tipeId] ?? null;
            }
        }

    // ──────────────────────────────────────────────────────────────────────
    } // end mount()

    // ──────────────────────────────────────────────────────────────────────

    // melakukan validasi input, memanggil service untuk menyimpan absensi, dan menampilkan pesan sukses atau error
    // input berupa object KehadiranService, output berupa void yang mengirimkan event ke frontend
    public function simpanAbsensi(KehadiranService $service)
    {
        // Validasi backend: pastikan lokasi dan waktu absensi sudah berhasil diambil dari GPS
        if (empty($this->latitude) || empty($this->longitude) || empty($this->waktu)) {
            $this->dispatch(
                'flash-error',
                message: 'Silakan klik tombol "Ambil Lokasi" terlebih dahulu sebelum melakukan absensi.'
            );
            return;
        }

        // Validasi backend: pastikan jarak berada di dalam radius yang diizinkan
        $user = auth()->user();
        $lokasi = $user && $user->departemen ? $user->departemen->lokasi : null;
        $radius = $lokasi ? $lokasi->radius : 100;

        if ($this->jarak > $radius) {
            $this->dispatch(
                'flash-error',
                message: 'Anda berada di luar area kantor. Jarak: ' . round($this->jarak) . ' meter.'
            );
            return;
        }

        $data = [
            'waktu'     => $this->waktu,
            'latitude'  => $this->latitude,
            'longitude' => $this->longitude,
            'jadwalId'  => $this->jadwalId,
            'jamMasuk'  => $this->jamMasuk,
            'jamKeluar' => $this->jamKeluar,
            'toleransi' => $this->toleransiTelat,
            'rekapId'   => $this->rekapId,
        ];

        if ($this->jenisAbsensi === 'masuk') {
            $hasil = $service->absenMasuk($data);
        } else {
            $hasil = $service->absenKeluar($data);
        }

        if (! $hasil['success']) {
            $this->dispatch(
                'flash-error',
                message: $hasil['message']
            );
            return;
        }

        $this->dispatch(
            'flash-success',
            message: $hasil['message']
        );

        $this->dispatch('refresh-dashboard');

        // Update status absensi tanpa reload halaman
        $kehadiranHariIni = (new KehadiranService)->cekKehadiran();

        if ($kehadiranHariIni) {
            $this->sudahAbsenMasuk = ! is_null($kehadiranHariIni->waktu_masuk);
            $this->sudahAbsenKeluar = ! is_null($kehadiranHariIni->waktu_keluar);
            // Pindahkan pilihan otomatis ke 'keluar' jika baru saja absen masuk
            if ($this->sudahAbsenMasuk && ! $this->sudahAbsenKeluar) {
                $this->jenisAbsensi = 'keluar';
            }
        }
    }

    // ──────────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.Karyawan.dashboard-absensi', [
            'jamMasuk'              => $this->jamMasuk,
            'jamKeluar'             => $this->jamKeluar,
            'kehadiranSudahTerisi'  => $this->kehadiranSudahTerisi,
            'tipeKehadiranHariIni'  => $this->tipeKehadiranHariIni,
        ]);
    }
}
