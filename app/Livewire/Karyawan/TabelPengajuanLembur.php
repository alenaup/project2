<?php

namespace App\Livewire\Karyawan;

use App\Models\Lembur;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TabelPengajuanLembur extends Component
{
    use WithPagination;

    public string $filterStatus = 'semua';

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    #[On('lembur-diajukan')]
    public function refreshData(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Lembur::where('karyawan_id', Auth::id() ?? \App\Models\User::first()->id_user)
            ->where('status', 'active');

        if ($this->filterStatus !== 'semua') {
            $query->where('status_validasi', $this->filterStatus);
        }

        $pengajuan = $query->latest('created_at')->paginate(10);

        return view('livewire.karyawan.tabel-pengajuan-lembur', [
            'pengajuan' => $pengajuan,
        ]);
    }
}
