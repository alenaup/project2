<?php

namespace App\Livewire\AdminOutsourcing;

use App\Models\Kehadiran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use App\Services\UserService;
use App\Services\KehadiranService;
use Livewire\Component;

// class component livewire
class RekapanKaryawan extends Component
{
    public string $startDate;
    public string $endDate;
    public string $rekapBulan;

    public array $karyawanByOutsourcing = [];
    public array $datas = [];
    public array $koloms = [];

    public int $perPage = 10;
    public int $halamanAktif = 1;
    public int $totalKaryawan = 0;

    public function mount()
    {
        $this->startDate = Carbon::now()->subMonth()->day(26)->format('Y-m-d');
        $this->endDate = Carbon::now()->day(25)->format('Y-m-d');
        $this->rekapBulan = Carbon::now()->format('Y-m');
        $this->loadData();
    }

    public function updatedRekapBulan()
    {
        $this->halamanAktif = 1;
        $this->loadData();
    }

    public function gantiHalaman(int $halaman): void
    {
        $this->halamanAktif = $halaman;
        $this->loadData();
    }

    // ──────────────────────────────────────────────────────
    // Service Getter
    // ──────────────────────────────────────────────────────

    private function getService(): \App\Services\AdminOutsourcingDashboardService
    {
        return app(\App\Services\AdminOutsourcingDashboardService::class);
    }

    // ──────────────────────────────────────────────────────
    // Computed Properties (data dari database)
    // ──────────────────────────────────────────────────────

    /**
     * Mengambil total karyawan hadir pada tanggal range.
     */
    public function getTotalHadirProperty(): int
    {
        $stats = $this->getService()->getStats(Auth::user()->outsourcing_id, $this->startDate, $this->endDate);
        return $stats['total_hadir'];
    }

    /**
     * Mengambil total kehadiran bertipe alpha / tidak hadir tanpa keterangan.
     */
    public function getTotalAlphaProperty(): int
    {
        $stats = $this->getService()->getStats(Auth::user()->outsourcing_id, $this->startDate, $this->endDate);
        return $stats['total_alpha'];
    }

    /**
     * Mengambil total kehadiran bertipe izin atau sakit.
     */
    public function getTotalIzinSakitProperty(): int
    {
        $stats = $this->getService()->getStats(Auth::user()->outsourcing_id, $this->startDate, $this->endDate);
        return $stats['total_izin_sakit'];
    }

    /**
     * Mengambil total karyawan aktif.
     */
    public function getTotalKaryawanProperty(): int
    {
        return $this->getService()->getKaryawanCount(Auth::user()->outsourcing_id);
    }

    // ──────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────

    private function getRekapDateRange()
    {
        $carbon = Carbon::createFromFormat('Y-m', $this->rekapBulan);
        $prevMonthStart = $carbon->copy()->subMonth();
        
        $start = $prevMonthStart->day(26)->format('Y-m-d');
        $end = $carbon->day(25)->format('Y-m-d');
        
        return [$start, $end];
    }

    private function updateDateRangeFromBulan()
    {
        [$start, $end] = $this->getRekapDateRange();
        $this->startDate = $start;
        $this->endDate = $end;
    }

    /**
     * Render component ke view
     */
    protected function loadData(): void
    {
        $this->karyawanByOutsourcing = (new UserService)->getKaryawanByOutsourcing(Auth::user()->outsourcing_id, "array");

        $this->updateDateRangeFromBulan();

        // inisiasi array
        $this->datas = [];
        $this->koloms = [];

        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        
        $dates = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dates[] = $date->copy();
            $this->koloms[] = $date->format('d/m');
        }

        $this->totalKaryawan = $this->getService()->getKaryawanCount(Auth::user()->outsourcing_id);

        $karyawans = $this->getService()->getKaryawans(Auth::user()->outsourcing_id, $this->halamanAktif, $this->perPage);

        $userIds = $karyawans->pluck('id_user');

        // Ambil seluruh kehadiran dalam range tanggal ini dalam 1 query
        $kehadirans = (new KehadiranService)->ambilBanyakKehadiranByDateRange($userIds, $this->startDate, $this->endDate);

        $kehadiranMap = $kehadirans
            ->groupBy('karyawan_id')
            ->map(function ($items) {
                return $items->keyBy(function ($item) {
                    return Carbon::parse($item->tanggal)->format('Y-m-d');
                });
            });

        $colors = ['bg-green-600','bg-emerald-600','bg-blue-500','bg-purple-600','bg-orange-500'];
        foreach ($karyawans as $index => $karyawan) {
            $absens = [];

            foreach ($dates as $date) {
                $tanggal = $date->format('Y-m-d');

                $kehadiran = $kehadiranMap[$karyawan->id_user][$tanggal] ?? null;

                if (!$kehadiran) {
                    $absens[] = [
                        'value' => '-',
                        'warna' => 'text-gray-300',
                    ];

                    continue;
                }

                switch ($kehadiran->tipe_kehadiran_id) {
                    case 1: // Hadir
                        $absens[] = [
                            'value' => 'H',
                            'warna' => 'bg-green-100 text-green-700',
                        ];
                        break;

                    case 2: // Sakit
                        $absens[] = [
                            'value' => 'S',
                            'warna' => 'bg-yellow-100 text-yellow-700',
                        ];
                        break;

                    case 3: // Mangkir (Alpha)
                        $absens[] = [
                            'value' => 'A',
                            'warna' => 'bg-red-100 text-red-700',
                        ];
                        break;

                    case 4: // Cuti
                        $absens[] = [
                            'value' => 'L',
                            'warna' => 'bg-purple-100 text-purple-700',
                        ];
                        break;

                    case 5: // Izin
                        $absens[] = [
                            'value' => 'I',
                            'warna' => 'bg-blue-100 text-blue-700',
                        ];
                        break;

                    case 6: // Terlambat
                        $absens[] = [
                            'value' => 'H',
                            'warna' => 'bg-green-100 text-green-700',
                        ];
                        break;

                    default:
                        $absens[] = [
                            'value' => '-',
                            'warna' => 'text-gray-300',
                        ];
                        break;
                }
            }

            $inisial = collect(explode(' ', $karyawan->nama_lengkap))
                ->take(2)
                ->map(fn($w) => !empty($w) ? strtoupper($w[0]) : '')
                ->join('');

            $this->datas[] = [
                'no' => (($this->halamanAktif - 1) * $this->perPage) + ($index + 1),
                'nama' => $karyawan->nama_lengkap,
                'inisial' => $inisial,
                'perusahaan' => Auth::user()->outsourcing->nama_outsourcing ?? 'Vendor',
                'posisi' => $karyawan->departemen->nama_departemen ?? '-',
                'warna' => $colors[$index % count($colors)],
                'absens' => $absens,
            ];
        }

        $this->loadRekapRecord();
    }

    public $rekapRecord = null;

    public function loadRekapRecord()
    {
        if (empty($this->rekapBulan)) {
            $this->rekapRecord = null;
            return;
        }

        [$start, $end] = $this->getRekapDateRange();
        $karyawanIds = $this->karyawanByOutsourcing;

        $this->rekapRecord = $this->getService()->loadRekapRecord($karyawanIds, $start, $end);
    }

    public function kirimRekapan()
    {
        [$start, $end] = $this->getRekapDateRange();

        $rekap = $this->getService()->kirimRekapan(Auth::user()->outsourcing_id, $start, $end);

        if (!$rekap) {
            session()->flash('error', 'Tidak ada data absensi pada periode rekap ini untuk dikirim.');
            return;
        }

        $this->loadRekapRecord();
        session()->flash('success', 'Rekapan absensi berhasil dikirim.');
    }

    public function kirimUlang()
    {
        $this->loadRekapRecord();
        if ($this->rekapRecord && $this->rekapRecord->status_validasi === \App\Enums\Validasi::Invalid->value) {
            [$start, $end] = $this->getRekapDateRange();

            $this->getService()->kirimUlang($this->rekapRecord, Auth::user()->outsourcing_id, $start, $end);

            session()->flash('success', 'Rekapan absensi berhasil dikirim ulang.');
            $this->loadRekapRecord();
        }
    }

    public function render()
    {
        $label = Carbon::parse($this->startDate)->translatedFormat('d M Y') . ' - ' . Carbon::parse($this->endDate)->translatedFormat('d M Y');
        return view('livewire.admin-outsourcing.rekapan-karyawan', [
            'totalHadir' => $this->totalHadir,
            'totalAlpha' => $this->totalAlpha,
            'totalIzinSakit' => $this->totalIzinSakit,
            'totalKaryawan' => $this->totalKaryawan,
            'labelBulan' => $label,
        ]);
    }
}