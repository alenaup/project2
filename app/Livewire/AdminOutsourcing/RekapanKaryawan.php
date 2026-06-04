<?php

namespace App\Livewire\AdminOutsourcing;

use App\Models\Kehadiran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RekapanKaryawan extends Component
{
    public array $datas = [];

    public function mount()
    {
        $this->loadData();
    }

    protected function loadData(): void
    {
        $this->datas = [];

        $bulan = now()->month;
        $tahun = now()->year;

        $jumlahHari = Carbon::create($tahun, $bulan)->daysInMonth;

        // Ambil semua karyawan outsourcing yang sedang login
        $karyawans = User::where('role', 'karyawan')
            ->where('outsourcing_id', Auth::user()->outsourcing_id)
            ->get();

        // Ambil ID karyawan
        $userIds = $karyawans->pluck('id_user');

        // Ambil seluruh kehadiran bulan ini dalam 1 query
        $kehadirans = Kehadiran::whereIn('karyawan_id', $userIds)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        /*
         * Bentuk hasil:
         * [
         *   user_id => [
         *      '2026-06-01' => Kehadiran,
         *      '2026-06-02' => Kehadiran,
         *   ]
         * ]
         */
        $kehadiranMap = $kehadirans
            ->groupBy('karyawan_id')
            ->map(function ($items) {
                return $items->keyBy(function ($item) {
                    return Carbon::parse($item->tanggal)->format('Y-m-d');
                });
            });

        foreach ($karyawans as $karyawan) {

            $absens = [];

            for ($hari = 1; $hari <= $jumlahHari; $hari++) {

                $tanggal = Carbon::create(
                    $tahun,
                    $bulan,
                    $hari
                )->format('Y-m-d');

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
        return view('livewire.admin-outsourcing.rekapan-karyawan');
    }
}