<?php

namespace App\Livewire\HR;

use Livewire\Component;
use App\Models\User;
use App\Models\Outsourcing;
use App\Enums\UserRole;
use App\Enums\Status;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    public function mount(): void
    {
        $this->bulan   = now()->format('Y-m');
        $this->vendors = Outsourcing::all();
        $this->tampilkanRekap();
    }

    public function tampilkanRekap(): void
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

        $awal                    = $carbonBulan->copy()->subMonth()->setDay(25);
        $akhir                   = $carbonBulan->copy()->setDay(24);
        $this->periodeAwal       = $awal->format('Y-m-d');
        $this->periodeAkhir      = $akhir->format('Y-m-d');
        $this->labelPeriode      = $awal->translatedFormat('d M Y') . ' – ' . $akhir->translatedFormat('d M Y');
        $this->jumlahHariDalamBulan = (int) $awal->diffInDays($akhir) + 1;

        $this->halamanAktif = 1;
        $this->loadData();
    }

    public function loadData(): void
    {
        $query = User::with(['outsourcing', 'departemen'])
            ->where('role', UserRole::Karyawan->value)
            ->where('status', Status::Active->value);

        if ($this->vendorId) {
            $query->where('outsourcing_id', $this->vendorId);
        }

        $this->totalKaryawan = $query->count();

        $rawUsers = $query
            ->skip(($this->halamanAktif - 1) * $this->perPage)
            ->take($this->perPage)
            ->get();

        $formatted      = [];
        $this->totalH   = 0;
        $this->totalA   = 0;
        $this->totalSI  = 0;
        $this->totalL   = 0;

        $awalCarbon = Carbon::parse($this->periodeAwal);

        foreach ($rawUsers as $user) {
            $formatted[] = $this->processKehadiranData($user, $awalCarbon);
        }

        $this->users       = $formatted;
        $this->sudahFilter = true;
    }

    /**
     * Memproses data kehadiran untuk satu karyawan dan menghitung rekapannya.
     */
    private function processKehadiranData(User $user, Carbon $awalCarbon): array
    {
        $kehadiranData = DB::table('kehadiran')
            ->join('jadwal', 'kehadiran.jadwal_id', '=', 'jadwal.id_jadwal')
            ->join('karyawan_jadwal', 'jadwal.id_jadwal', '=', 'karyawan_jadwal.jadwal_id')
            ->join('tipe_kehadiran', 'kehadiran.tipe_kehadiran_id', '=', 'tipe_kehadiran.id_tipe_kehadiran')
            ->where('karyawan_jadwal.user_id', $user->id_user)
            ->whereBetween('kehadiran.tanggal', [$this->periodeAwal, $this->periodeAkhir])
            ->select('kehadiran.tanggal', 'tipe_kehadiran.status_kehadiran')
            ->get();

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

    public function pilihVendor(?int $id): void
    {
        $this->vendorId   = $id;
        $this->halamanAktif = 1;

        // Jika rekap sudah pernah ditampilkan, langsung reload dengan filter baru
        if ($this->sudahFilter) {
            $this->loadData();
        }
    }

    public function gantiHalaman(int $halaman): void
    {
        $this->halamanAktif = $halaman;
        $this->loadData();
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
    }

    public function setujuiRekap(): void
    {
        $this->statusRekap = 'Disetujui';
        session()->flash('success', 'Rekap berhasil disetujui.');
    }

    public function tolakRekap(): void
    {
        $this->statusRekap = 'Ditolak';
        session()->flash('success', 'Rekap telah ditolak.');
    }

    public function render()
    {
        return view('livewire.hr.rekapan-detail');
    }
}
