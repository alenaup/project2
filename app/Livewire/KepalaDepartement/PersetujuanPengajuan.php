<?php

namespace App\Livewire\KepalaDepartement;

use App\Services\LemburService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PersetujuanPengajuan extends Component
{
    use WithPagination;

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
     * Menyetujui pengajuan lembur yang dipilih.
     *
     * @param int $id
     * @return void
     */
    public function approve(int $id)
    {
        $user = Auth::user();
        if ($user && $this->lemburService->approveLembur($id, $user->id_user)) {
            session()->flash('success', 'Pengajuan lembur berhasil disetujui.');
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
        if ($user && $this->lemburService->rejectLembur($id, $user->id_user)) {
            session()->flash('success', 'Pengajuan lembur berhasil ditolak.');
        }
    }

    /**
     * Menyetujui semua pengajuan lembur yang masih pending di departemen yang sama.
     *
     * @return void
     */
    public function approveAllPending()
    {
        $user = Auth::user();
        if (!$user || !$user->departemen_id) {
            return;
        }

        $count = $this->lemburService->approveAllPendingLembur($user->departemen_id, $user->id_user);

        if ($count > 0) {
            session()->flash('success', $count . ' pengajuan lembur berhasil disetujui.');
        } else {
            session()->flash('success', 'Tidak ada pengajuan yang berstatus pending.');
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

        $lemburList = $this->lemburService->getLemburListByDepartemenPaginated($deptId, 20);
        $hasPending = $this->lemburService->hasPendingLembur($deptId);

        return view('livewire.kepala-departement.persetujuan-pengajuan', [
            'lemburList' => $lemburList,
            'hasPending' => $hasPending,
        ]);
    }
}
