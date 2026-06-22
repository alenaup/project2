<?php

namespace App\Livewire\KepalaDepartemen;

use App\Models\Lembur;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ValidasiLembur extends Component
{
    use WithPagination;

    public string $filterStatus = 'semua';
    public ?int $selectedId = null;

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function lihatDetail(int $id): void
    {
        $this->selectedId = $id;
    }

    public function tutupDetail(): void
    {
        $this->selectedId = null;
    }

    public function setujui(int $id): void
    {
        $lembur = Lembur::findOrFail($id);
        $lembur->update([
            'status_validasi' => 'valid',
            'pemvalidasi_id'  => Auth::id(),
        ]);

        $this->tutupDetail();
        session()->flash('success', 'Pengajuan lembur berhasil disetujui.');
    }

    public function tolak(int $id): void
    {
        $lembur = Lembur::findOrFail($id);
        $lembur->update([
            'status_validasi' => 'invalid',
            'pemvalidasi_id'  => Auth::id(),
        ]);

        $this->tutupDetail();
        session()->flash('success', 'Pengajuan lembur berhasil ditolak.');
    }

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Lembur::with('karyawan')
            ->whereHas('karyawan', function ($q) use ($user) {
                $q->where('departemen_id', $user->departemen_id);
            })
            ->where('status', 'active');

        if ($this->filterStatus !== 'semua') {
            $query->where('status_validasi', $this->filterStatus);
        }

        $pengajuan = $query->latest('created_at')->paginate(10);

        $selectedLembur = null;
        if ($this->selectedId) {
            $selectedLembur = Lembur::with('karyawan')->find($this->selectedId);
        }

        return view('livewire.kepala-departemen.validasi-lembur', [
            'pengajuan'      => $pengajuan,
            'selectedLembur' => $selectedLembur,
        ]);
    }
}
