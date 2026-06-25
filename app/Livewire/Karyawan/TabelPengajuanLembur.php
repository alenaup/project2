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

    // Properti untuk Form Edit
    public $edit_id;
    public string $edit_tanggal = '';
    public string $edit_mulai = '';
    public string $edit_selesai = '';
    public string $edit_keterangan = '';

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    #[On('lembur-diajukan')]
    public function refreshData(): void
    {
        $this->resetPage();
    }

    public function editLembur($id): void
    {
        $lembur = Lembur::findOrFail($id);
        
        // Hanya yang status pending yang bisa diubah
        if ($lembur->status_validasi !== 'pending') {
            session()->flash('error', 'Hanya pengajuan dengan status Menunggu yang dapat diubah.');
            return;
        }

        $this->edit_id = $lembur->id_lembur;
        $this->edit_tanggal = \Carbon\Carbon::parse($lembur->mulai_lembur)->format('Y-m-d');
        $this->edit_mulai = \Carbon\Carbon::parse($lembur->mulai_lembur)->format('H:i');
        $this->edit_selesai = \Carbon\Carbon::parse($lembur->selesai_lembur)->format('H:i');
        $this->edit_keterangan = $lembur->keterangan;

        $this->dispatch('open-modal-edit');
    }

    public function updateLembur(): void
    {
        $this->validate([
            'edit_tanggal'    => 'required|date',
            'edit_mulai'      => 'required',
            'edit_selesai'    => 'required',
            'edit_keterangan' => 'required|string|max:255',
        ], [
            'edit_tanggal.required'  => 'Tanggal lembur wajib diisi.',
            'edit_tanggal.date'      => 'Format tanggal tidak valid.',
            'edit_mulai.required'    => 'Jam mulai wajib diisi.',
            'edit_selesai.required'  => 'Jam selesai wajib diisi.',
            'edit_keterangan.required' => 'Keterangan wajib diisi.',
        ]);

        $lembur = Lembur::findOrFail($this->edit_id);
        
        if ($lembur->status_validasi !== 'pending') {
            session()->flash('error', 'Hanya pengajuan dengan status Menunggu yang dapat diubah.');
            return;
        }

        $lembur->update([
            'mulai_lembur'   => $this->edit_tanggal . ' ' . $this->edit_mulai . ':00',
            'selesai_lembur' => $this->edit_tanggal . ' ' . $this->edit_selesai . ':00',
            'keterangan'     => $this->edit_keterangan,
        ]);

        $this->dispatch('close-modal-edit');
        session()->flash('success_riwayat', 'Data pengajuan lembur berhasil diperbarui!');
    }

    public function deleteLembur($id): void
    {
        $lembur = Lembur::findOrFail($id);

        if ($lembur->status_validasi !== 'pending') {
            session()->flash('error', 'Hanya pengajuan dengan status Menunggu yang dapat dihapus.');
            return;
        }

        $lembur->delete();
        session()->flash('success_riwayat', 'Data pengajuan lembur berhasil dihapus.');
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
