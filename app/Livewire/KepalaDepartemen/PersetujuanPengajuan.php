<?php

namespace App\Livewire\KepalaDepartemen;

use App\Services\LemburService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PersetujuanPengajuan extends Component
{
    use WithPagination;

    public ?string $filterDate = null;

    protected LemburService $lemburService;

    /**
     * Bootstrapping dependency injection untuk LemburService.
     *
     * @param LemburService $lemburService
     */
    public function boot(LemburService $lemburService)
    {
        $this->lemburService = $lemburService;
    }

    /**
     * Hook yang terpanggil saat properti filterDate berubah.
     */
    public function updatedFilterDate()
    {
        $this->resetPage();
    }

    /**
     * Menyetujui pengajuan lembur yang dipilih.
     *
     * @param int $id
     * @return void
     */
    public function approve(int $id)
    {
        $user = Auth::user();
        if ($user && $this->lemburService->approveLembur($id, $user->id_user, 'valid')) {
            $this->dispatch('flash-success', message: 'Pengajuan lembur berhasil disetujui.');
        }
    }

    /**
     * Menolak pengajuan lembur yang dipilih.
     *
     * @param int $id
     * @return void
     */
    public function reject(int $id)
    {
        $user = Auth::user();
        if ($user && $this->lemburService->approveLembur($id, $user->id_user, 'invalid')) {
            $this->dispatch('flash-success', message: 'Pengajuan lembur berhasil ditolak.');
        }
    }

    /**
     * Menyetujui semua pengajuan lembur yang masih pending di departemen yang sama.
     *
     * @return void
     */
    public function approveAllPending(?string $date = null)
    {
        $user = Auth::user();
        if (!$user || !$user->departemen_id) {
            return;
        }

        $count = $this->lemburService->approveAllPendingLembur($user->departemen_id, $user->id_user, $date);

        if ($count > 0) {
            $this->dispatch('flash-success', message: $count . ' pengajuan lembur berhasil disetujui.');
        } else {
            $this->dispatch('flash-error', message: 'Tidak ada pengajuan yang berstatus pending pada kriteria tersebut.');
        }
    }

    /**
     * Render view komponen Livewire.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $user = Auth::user();
        $deptId = $user ? $user->departemen_id : null;

        $lemburList = $this->lemburService->getLemburListByDepartemenPaginated($deptId, 20, $this->filterDate);
        $hasPending = $this->lemburService->hasPendingLembur($deptId);

        // Fetch pending list for notifications
        $pendingLemburs = [];
        $indicatorColor = 'slate'; // default
        $daysPending = 0;

        if ($deptId) {
            $pendingList = $this->lemburService->listTanggalPendingLembur($deptId);

            if ($pendingList->isNotEmpty()) {
                $oldest = $pendingList->first();
                $diffInHours = now()->diffInHours($oldest->created_at);
                $daysPending = $diffInHours / 24;

                if ($daysPending >= 3) {
                    $indicatorColor = 'red';
                } elseif ($daysPending >= 1) {
                    $indicatorColor = 'yellow';
                } else {
                    $indicatorColor = 'green';
                }

                // Prepare up to 3 oldest for the popup
                foreach ($pendingList->take(3) as $item) {
                    $pendingLemburs[] = [
                        'id' => $item->id_lembur,
                        'nama' => $item->karyawan->nama_lengkap ?? '-',
                        'tanggal' => Carbon::parse($item->mulai_lembur)->translatedFormat('d F Y'),
                        'jam' => Carbon::parse($item->mulai_lembur)->format('H:i') . ' - ' . Carbon::parse($item->selesai_lembur)->format('H:i'),
                        'status' => $item->status_validasi,
                        'keterangan' => $item->keterangan ?? 'Tidak ada keterangan.',
                        'selisih' => now()->diffForHumans($item->created_at, true),
                    ];
                }
            }
        }

        return view('livewire.kepala-departemen.persetujuan-pengajuan', [
            'lemburList' => $lemburList,
            'hasPending' => $hasPending,
            'indicatorColor' => $indicatorColor,
            'daysPending' => $daysPending,
            'pendingLemburs' => $pendingLemburs,
        ]);
    }
}
