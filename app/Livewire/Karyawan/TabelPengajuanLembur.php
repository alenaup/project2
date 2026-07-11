<?php

namespace App\Livewire\Karyawan;

use App\Services\LemburService;
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

    // melakukan penyimpanan data pengajuan lembur yang diedit
    // input berupa id pengajuan lembur, tanggal lembur, jam mulai, jam selesai, dan keterangan
    // output berupa validasi berhasil atau gagal, jika berhasil maka data akan diperbarui di database
    public function saveLemburEdit($id, $tanggal, $mulai, $selesai, $keterangan): void
    {
        $this->edit_id = $id;
        $this->edit_tanggal = $tanggal;
        $this->edit_mulai = $mulai;
        $this->edit_selesai = $selesai;
        $this->edit_keterangan = $keterangan;

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

        $lembur = (new LemburService)->getLemburOrFail($id);

        if ($lembur->status_validasi !== 'pending') {
            session()->flash('error', 'Hanya pengajuan dengan status Menunggu yang dapat diubah.');
            return;
        }

        $lembur->update([
            'mulai_lembur'   => $tanggal . ' ' . $mulai . ':00',
            'selesai_lembur' => $tanggal . ' ' . $selesai . ':00',
            'keterangan'     => $keterangan,
        ]);

        $this->dispatch('close-modal-edit');
        session()->flash('success_riwayat', 'Data pengajuan lembur berhasil diperbarui!');
    }

    // melakukan penghapusan data pengajuan lembur
    // input berupa id pengajuan lembur
    // output berupa validasi berhasil atau gagal, jika berhasil maka data akan dihapus dari database
    public function deleteLembur($id): void
    {
        $lembur = (new LemburService)->getLemburOrFail($id);

        if ($lembur->status_validasi !== 'pending') {
            session()->flash('error', 'Hanya pengajuan dengan status Menunggu yang dapat dihapus.');
            return;
        }

        $lembur->delete();
        session()->flash('success_riwayat', 'Data pengajuan lembur berhasil dihapus.');
    }

    public function render()
    {
        $query = (new LemburService)->getLemburByIdAuth()->karyawan->lembur();

        if ($this->filterStatus !== 'semua') {
            $query->where('status_validasi', $this->filterStatus);
        }

        $pengajuan = $query->latest('created_at')->paginate(10);

        return view('livewire.Karyawan.tabel-pengajuan-lembur', [
            'pengajuan' => $pengajuan,
        ]);
    }
}
