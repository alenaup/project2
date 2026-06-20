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
    /** @var string Bulan dan tahun yang difilter (format: Y-m) */
    public string $bulan;

    public array $karyawanByOutsourcing;
    public array $datas = [];
    public array $koloms = [];

    public function mount()
    {
        $this->bulan = Carbon::now()->format('Y-m');
        $this->loadData();
    }

    public function updatedBulan()
    {
        $this->loadData();
    }

    // ──────────────────────────────────────────────────────
    // Computed Properties (data dari database)
    // ──────────────────────────────────────────────────────

    private function getDateRange()
    {
        [$tahun, $bulan] = $this->parseBulan();
        
        $currentMonthStart = Carbon::create($tahun, $bulan, 1);
        $prevMonthStart = $currentMonthStart->copy()->subMonth();
        
        $startDate = $prevMonthStart->copy()->day(26)->format('Y-m-d');
        $endDate = $currentMonthStart->copy()->day(25)->format('Y-m-d');
        
        return [$startDate, $endDate];
    }

    /**
     * Mengambil total karyawan hadir pada bulan yang dipilih.
     * Kehadiran bertipe 'hadir' dihitung dari tabel tipe_kehadiran.
     */
    public function getTotalHadirProperty(): int
    {
        $karyawanIds = $this->karyawanByOutsourcing;
        [$startDate, $endDate] = $this->getDateRange();

        return (new KehadiranService)
            ->totalHadirByDateRange(
                $karyawanIds,
                $startDate,
                $endDate
            );
    }

    /**
     * Mengambil total kehadiran bertipe alpha / tidak hadir tanpa keterangan.
     */
    public function getTotalAlphaProperty(): int
    {
        $getKaryawan = $this->karyawanByOutsourcing;
        [$startDate, $endDate] = $this->getDateRange();

        return (new KehadiranService)->cekKehadiranBanyakKaryawanByDateRange('mankir', $getKaryawan, $startDate, $endDate);
    }

    /**
     * Mengambil total kehadiran bertipe izin atau sakit.
     */
    public function getTotalIzinSakitProperty(): int
    {
        $getKaryawan = $this->karyawanByOutsourcing;
        [$startDate, $endDate] = $this->getDateRange();

        return (new KehadiranService)->cekKehadiranBanyakKaryawanByDateRange(['izin', 'sakit'], $getKaryawan, $startDate, $endDate);
    }

    /**
     * Mengambil total karyawan aktif (tanpa tanggal_keluar / tanggal_keluar NULL).
     */
    public function getTotalKaryawanProperty(): int
    {
        return User::query()->whereNull('tanggal_keluar')
            ->where('role', UserRole::Karyawan->value)
            ->where('outsourcing_id', Auth::user()->outsourcing_id)
            ->count();
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
    protected function loadData(): void
    {
        $this->karyawanByOutsourcing = (new UserService)->getKaryawanByOutsourcing(Auth::user()->outsourcing_id, "array");

        // inisiasi array
        $this->datas = [];
        $this->koloms = [];

        [$tahun, $bulan] = $this->parseBulan();
        
        $currentMonthStart = Carbon::create($tahun, $bulan, 1);
        $prevMonthStart = $currentMonthStart->copy()->subMonth();
        
        $startDate = $prevMonthStart->copy()->day(26);
        $endDate = $currentMonthStart->copy()->day(25);
        
        $dates = [];
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dates[] = $date->copy();
            $this->koloms[] = $date->format('d/m');
        }

        $jenisData = "object";
        // mengambil semua karyawan outsourcing yang sedang login
        $karyawans = (new UserService)->getKaryawanByOutsourcing(Auth::user()->outsourcing_id, $jenisData);

        // Ambil ID karyawan
        $userIds = $karyawans->pluck('id_user');

        // Ambil seluruh kehadiran dalam range tanggal ini dalam 1 query
        $kehadirans = (new KehadiranService)->ambilBanyakKehadiranByDateRange($userIds, $startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

        // mengubah array menjadi object dengan key adalah karyawan_id dan value adalah object kehadiran
        $kehadiranMap = $kehadirans
            ->groupBy('karyawan_id')
            ->map(function ($items) {
                return $items->keyBy(function ($item) {
                    return Carbon::parse($item->tanggal)->format('Y-m-d');
                });
            });

        foreach ($karyawans as $karyawan) {

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

                    case 3: // Mangkir
                        $absens[] = [
                            'value' => 'M',
                            'warna' => 'bg-red-100 text-red-700',
                        ];
                        break;

                    case 5: // Izin
                        $absens[] = [
                            'value' => 'I',
                            'warna' => 'bg-blue-100 text-blue-700',
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

            $this->datas[] = [
                'no' => $karyawan->id_user,
                'nama' => $karyawan->nama_lengkap,
                'inisial' => $karyawan->inisial,
                'perusahaan' => $karyawan->perusahaan,
                'posisi' => $karyawan->posisi,
                'warna' => $karyawan->warna,
                'absens' => $absens,
            ];
        }
    }

    public function render()
    {
        return view('livewire.admin-outsourcing.rekapan-karyawan', [
            'totalHadir' => $this->totalHadir,
            'totalAlpha' => $this->totalAlpha,
            'totalIzinSakit' => $this->totalIzinSakit,
            'totalKaryawan' => $this->totalKaryawan,
            'labelBulan' => Carbon::createFromFormat('Y-m', $this->bulan)
                ->translatedFormat('F Y'),
        ]);
    }
}