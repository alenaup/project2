<?php

namespace App\Livewire\Karyawan;

use App\Models\PerizinanSakit;
use App\Models\User;
use App\Services\PerizinanSakitService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class RiwayatPerizinanSakit extends Component
{
    use WithFileUploads;

    public $filterStatus = 'semua';

    // Untuk fitur Edit
    public $edit_id;

    public $edit_tanggal_mulai;

    public $edit_tanggal_selesai;

    public $edit_keterangan;

    public $edit_file_surat;

    public $edit_file_lama;

    // Modal Preview state handled by Alpine

    #[On('perizinan-dikirim')]
    public function refreshRiwayat()
    {
        // akan di-refresh otomatis karena render akan memanggil ulang data
    }

    public function setFilter($status)
    {
        $this->filterStatus = $status;
    }

    public function deletePengajuan($id)
    {
        $perizinan = (new PerizinanSakitService)->ambilStatus($id);

        if ($perizinan) {
            // Hapus file dari storage
            if (Storage::disk('public')->exists($perizinan->file_surat)) {
                Storage::disk('public')->delete($perizinan->file_surat);
            }
            $perizinan->delete();
            session()->flash('success_riwayat', 'Pengajuan berhasil dibatalkan dan dihapus.');
        }
    }

    public function simpanEdit()
    {
        $this->validate([
            'edit_tanggal_mulai' => 'required|date',
            'edit_tanggal_selesai' => 'required|date|after_or_equal:edit_tanggal_mulai',
            'edit_keterangan' => 'required|string|max:1000',
            'edit_file_surat' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $service = new PerizinanSakitService;

        $hasil = $service->updatePerizinan(
            $this->edit_id,
            $this->edit_tanggal_mulai,
            $this->edit_tanggal_selesai,
            $this->edit_keterangan,
            $this->edit_file_surat
        );

        if ($hasil) {

            session()->flash(
                'success_riwayat',
                'Pengajuan berhasil diperbarui.'
            );
            $this->reset(['edit_id', 'edit_tanggal_mulai', 'edit_tanggal_selesai', 'edit_keterangan', 'edit_file_surat', 'edit_file_lama']);
        }
    }

    public function render()
    {
        $query = (new PerizinanSakitService)->ambilPerizinanSakitUserLogin();

        if ($this->filterStatus !== 'semua') {
            $query->where('status', $this->filterStatus);
        }

        $riwayat = $query->orderBy('created_at', 'desc')->get();

        return view('livewire.Karyawan.riwayat-perizinan-sakit', [
            'riwayat' => $riwayat,
        ]);
    }
}
