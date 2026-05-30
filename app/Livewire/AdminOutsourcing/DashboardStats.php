<?php

namespace App\Livewire\AdminOutsourcing;

use App\Models\Kehadiran;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Component;

/**
 * Class DashboardStats
 *
 * Livewire component untuk menampilkan statistik kehadiran karyawan
 * pada dashboard admin outsourcing.
 *
 * Menampilkan:
 * - Total karyawan hadir pada bulan yang dipilih
 * - Total karyawan alpha (tidak hadir tanpa keterangan)
 * - Total karyawan izin / sakit
 * - Total karyawan aktif
 */
class DashboardStats extends Component
{
    /** @var string Bulan dan tahun yang difilter (format: Y-m) */
    public string $bulan;

    /**
     * Inisialisasi nilai default bulan ke bulan saat ini.
     */
    public function mount(): void
    {
        $this->bulan = Carbon::now()->format('Y-m');
    }

    // ──────────────────────────────────────────────────────
    // Computed Properties (data dari database)
    // ──────────────────────────────────────────────────────

    /**
     * Mengambil total karyawan hadir pada bulan yang dipilih.
     * Kehadiran bertipe 'hadir' dihitung dari tabel tipe_kehadiran.
     */
    public function getTotalHadirProperty(): int
    {
        [$tahun, $bulan] = $this->parseBulan();

        return Kehadiran::whereHas('tipeKehadiran', function ($q) {
                $q->where('status_kehadiran', 'hadir');
            })
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->count();
    }

    /**
     * Mengambil total kehadiran bertipe alpha / tidak hadir tanpa keterangan.
     */
    public function getTotalAlphaProperty(): int
    {
        [$tahun, $bulan] = $this->parseBulan();

        return Kehadiran::whereHas('tipeKehadiran', function ($q) {
                $q->where('status_kehadiran', 'alpha');
            })
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->count();
    }

    /**
     * Mengambil total kehadiran bertipe izin atau sakit.
     */
    public function getTotalIzinSakitProperty(): int
    {
        [$tahun, $bulan] = $this->parseBulan();

        return Kehadiran::whereHas('tipeKehadiran', function ($q) {
                $q->whereIn('status_kehadiran', ['izin', 'sakit']);
            })
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->count();
    }

    /**
     * Mengambil total karyawan aktif (tanpa tanggal_keluar / tanggal_keluar NULL).
     */
    public function getTotalKaryawanProperty(): int
    {
        return User::whereNull('tanggal_keluar')->count();
    }

    // ──────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────

    /**
     * Memparse properti $bulan (format Y-m) menjadi [$tahun, $bulan].
     *
     * @return array{int, int}
     */
    private function parseBulan(): array
    {
        $carbon = Carbon::createFromFormat('Y-m', $this->bulan);
        return [(int) $carbon->format('Y'), (int) $carbon->format('m')];
    }

    /**
     * Render component ke view livewire/admin-outsourcing/dashboard-stats.
     */
    public function render()
    {
        return view('livewire.admin-outsourcing.dashboard-stats', [
            'totalHadir'     => $this->totalHadir,
            'totalAlpha'     => $this->totalAlpha,
            'totalIzinSakit' => $this->totalIzinSakit,
            'totalKaryawan'  => $this->totalKaryawan,
            'labelBulan'     => Carbon::createFromFormat('Y-m', $this->bulan)
                                    ->translatedFormat('F Y'),
        ]);
    }
}
