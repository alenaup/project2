<?php

namespace App\Livewire\Hr;

use Livewire\Component;
use App\Models\User;
use App\Services\OutsourcingService;
use App\Services\UserService;
use App\Services\KehadiranService;
use App\Services\RekapService;
use Carbon\Carbon;

class RekapanDetail extends Component
{
    public ?int    $vendorId   = null;
    public ?string $bulan      = null;

    public array  $users   = [];
    public        $vendors = [];
    public bool   $sudahFilter = false;

    public ?int    $tahun               = null;
    public ?int    $bulanAngka          = null;
    public int     $jumlahHariDalamBulan = 31;
    public ?string $periodeAwal         = null;
    public ?string $periodeAkhir        = null;
    public string  $labelPeriode        = '';

    public int $totalH  = 0;
    public int $totalA  = 0;
    public int $totalSI = 0;
    public int $totalL  = 0;

    public string $statusRekap  = 'Menunggu Persetujuan';
    public int    $perPage      = 10;
    public int    $halamanAktif = 1;
    public int    $totalKaryawan = 0;

    protected array $mappingKode = [
        'hadir'     => 'H',
        'sakit'     => 'S',
        'izin'      => 'I',
        'mankir'    => 'A',
        'cuti'      => 'L',
        'terlambat' => 'H',
    ];

    public function mount(OutsourcingService $outsourcingService, UserService $userService, RekapService $rekapService): void
    {
        $this->bulan   = now()->format('Y-m');
        $this->vendors = $outsourcingService->ambilSemuaOutsourcing();
        $this->tampilkanRekap($userService, $rekapService, app(KehadiranService::class));
    }

    // melakukan validasi input bulan, mengambil data karyawan berdasarkan vendor, menghitung rekapitulasi kehadiran, dan menampilkan hasilnya
    // input bulan, vendor, dan halaman aktif, memberikan output daftar karyawan dengan rekapitulasi kehadiran, total karyawan, dan status rekap
    public function tampilkanRekap(UserService $userService, RekapService $rekapService, KehadiranService $kehadiranService): void
    {
        $this->validate(
            ['bulan' => 'required'],
            ['bulan.required' => 'Pilih bulan terlebih dahulu.']
        );

        $carbonBulan             = Carbon::createFromFormat('Y-m', $this->bulan);
        $this->tahun             = $carbonBulan->year;

        // reset total page saat filter baru
        $this->halamanAktif      = 1;
        $this->bulanAngka        = $carbonBulan->month;

        $awal                    = $carbonBulan->copy()->subMonth()->setDay(26);
        $akhir                   = $carbonBulan->copy()->setDay(25);
        $this->periodeAwal       = $awal->format('Y-m-d');
        $this->periodeAkhir      = $akhir->format('Y-m-d');
        $this->labelPeriode      = $awal->translatedFormat('d M Y') . ' – ' . $akhir->translatedFormat('d M Y');
        $this->jumlahHariDalamBulan = (int) $awal->diffInDays($akhir) + 1;

        $this->halamanAktif = 1;
        $this->loadData($userService, $rekapService, $kehadiranService);
    }

    // melakukan paginasi data karyawan berdasarkan vendor, bulan, dan halaman aktif
    // input vendor, bulan, dan halaman aktif, memberikan output daftar karyawan dengan rekap
    public function loadData(UserService $userService, RekapService $rekapService, KehadiranService $kehadiranService): void
    {
        $this->users = [];
        $this->totalKaryawan = 0;
        $this->totalH = 0;
        $this->totalA = 0;
        $this->totalSI = 0;
        $this->totalL = 0;

        $this->loadRekapRecord($rekapService);

        if (!$this->vendorId || !$this->rekapRecord || !$this->rekapRecord->tanggal_rekap) {
            $this->sudahFilter = true;
            return;
        }

        $this->totalKaryawan = $userService->getKaryawanByVendorCount($this->vendorId);

        $rawUsers = $userService->getKaryawanByVendorPaginated(
            $this->vendorId,
            $this->halamanAktif,
            $this->perPage
        );

        $formatted      = [];
        $awalCarbon = Carbon::parse($this->periodeAwal);

        foreach ($rawUsers as $user) {
            $formatted[] = $this->processKehadiranData($user, $awalCarbon, $kehadiranService);
        }

        $this->users       = $formatted;
        $this->sudahFilter = true;
    }

    /**
     * Memproses data kehadiran untuk satu karyawan dan menghitung rekapannya.
     */
    // melakukan pemetaan data kehadiran karyawan, menghitung jumlah hadir, mangkir, sakit/izin, dan cuti
    // input data karyawan, tanggal awal periode, dan service kehadiran, memberikan output array berisi data karyawan, peta kehadiran, dan ringkasan jumlah kehad
    private function processKehadiranData(User $user, Carbon $awalCarbon, KehadiranService $kehadiranService): array
    {
        $kehadiranData = $kehadiranService->getKehadiranDetailByKaryawan($user->id_user, $this->periodeAwal, $this->periodeAkhir);

        $kehadiranMap = [];
        foreach ($kehadiranData as $kehadiran) {
            $tgl  = Carbon::parse($kehadiran->tanggal);
            $urut = (int) $awalCarbon->diffInDays($tgl) + 1;
            $kehadiranMap[$urut] = $this->mappingKode[$kehadiran->status_kehadiran] ?? '-';
        }

        $hadir    = collect($kehadiranMap)->filter(fn($v) => $v === 'H')->count();
        $mangkir  = collect($kehadiranMap)->filter(fn($v) => $v === 'A')->count();
        $sakitIzin= collect($kehadiranMap)->filter(fn($v) => in_array($v, ['S', 'I']))->count();
        $cuti     = collect($kehadiranMap)->filter(fn($v) => $v === 'L')->count();

        $this->totalH  += $hadir;
        $this->totalA  += $mangkir;
        $this->totalSI += $sakitIzin;
        $this->totalL  += $cuti;

        return [
            'user'          => $user,
            'kehadiran_map' => $kehadiranMap,
            'summary'       => [
                'h'  => $hadir,
                'a'  => $mangkir,
                'si' => $sakitIzin,
                'l'  => $cuti,
            ],
        ];
    }

    public function pilihVendor(?int $id, UserService $userService, RekapService $rekapService, KehadiranService $kehadiranService): void
    {
        $this->vendorId   = $id;
        $this->halamanAktif = 1;

        // Jika rekap sudah pernah ditampilkan, langsung reload dengan filter baru
        if ($this->sudahFilter) {
            $this->loadData($userService, $rekapService, $kehadiranService);
        }
    }

    public function gantiHalaman(int $halaman, UserService $userService, RekapService $rekapService, KehadiranService $kehadiranService): void
    {
        $this->halamanAktif = $halaman;
        $this->loadData($userService, $rekapService, $kehadiranService);
    }

    public function resetFilter(): void
    {
        $this->vendorId          = null;
        $this->bulan             = now()->format('Y-m');
        $this->users             = [];
        $this->sudahFilter       = false;
        $this->halamanAktif      = 1;
        $this->totalH            = 0;
        $this->totalA            = 0;
        $this->totalSI           = 0;
        $this->totalL            = 0;
        $this->periodeAwal       = null;
        $this->periodeAkhir      = null;
        $this->labelPeriode      = '';
        $this->rekapRecord       = null;
        $this->statusRekap       = 'Belum Diajukan';
    }

    public ?\App\Models\RekapKehadiran $rekapRecord = null;

    // melakukan pemuatan data rekapan kehadiran berdasarkan vendor dan periode, serta menentukan status rekap
    // input vendor, periode awal, dan periode akhir, memberikan output data rekapan kehadiran dan status rekap (Disetujui, Ditolak, Menunggu Persetujuan,
    public function loadRekapRecord(RekapService $rekapService): void
    {
        if (!$this->vendorId || !$this->periodeAwal || !$this->periodeAkhir) {
            $this->rekapRecord = null;
            $this->statusRekap = 'Belum Diajukan';
            return;
        }

        $this->rekapRecord = $rekapService->getRekapRecord($this->vendorId, $this->periodeAwal, $this->periodeAkhir);

        if ($this->rekapRecord) {
            $statusVal = $this->rekapRecord->status_validasi;
            if ($statusVal === \App\Enums\Validasi::Valid->value) {
                $this->statusRekap = 'Disetujui';
            } elseif ($statusVal === \App\Enums\Validasi::Invalid->value) {
                $this->statusRekap = 'Ditolak';
            } else {
                $this->statusRekap = 'Menunggu Persetujuan';
            }
        } else {
            $this->rekapRecord = null;
            $this->statusRekap = 'Belum Diajukan';
        }
    }

    public function setujuiRekap(RekapService $rekapService): void
    {
        $this->loadRekapRecord($rekapService);
        if ($this->rekapRecord) {
            $rekapService->updateStatusValidasi($this->rekapRecord->id_rekapan, \App\Enums\Validasi::Valid->value, auth()->id());
            $this->statusRekap = 'Disetujui';
            session()->flash('success', 'Rekap berhasil disetujui.');
        } else {
            session()->flash('error', 'Data rekapan tidak ditemukan.');
        }
    }

    public function tolakRekap(RekapService $rekapService): void
    {
        $this->loadRekapRecord($rekapService);
        if ($this->rekapRecord) {
            $rekapService->updateStatusValidasi($this->rekapRecord->id_rekapan, \App\Enums\Validasi::Invalid->value, auth()->id());
            $this->statusRekap = 'Ditolak';
            session()->flash('success', 'Rekap telah ditolak.');
        } else {
            session()->flash('error', 'Data rekapan tidak ditemukan.');
        }
    }

    public function render()
    {
        return view('livewire.hr.rekapan-detail');
    }
}
