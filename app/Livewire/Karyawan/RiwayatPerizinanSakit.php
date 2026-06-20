<?php

namespace App\Livewire\Karyawan;

use App\Models\PerizinanSakit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;

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
    public $isEditing = false;

    // Untuk modal preview
    public $preview_file_url;
    public $preview_file_type;
    public $showPreviewModal = false;

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
        $perizinan = PerizinanSakit::where('id_perizinan', $id)
            ->where('karyawan_id', Auth::id() ?? \App\Models\User::first()->id_user)
            ->where('status', 'menunggu')
            ->first();

        if ($perizinan) {
            // Hapus file dari storage
            if (Storage::disk('public')->exists($perizinan->file_surat)) {
                Storage::disk('public')->delete($perizinan->file_surat);
            }
            $perizinan->delete();
            session()->flash('success_riwayat', 'Pengajuan berhasil dibatalkan dan dihapus.');
        }
    }

    public function editPengajuan($id)
    {
        $perizinan = PerizinanSakit::where('id_perizinan', $id)
            ->where('karyawan_id', Auth::id() ?? \App\Models\User::first()->id_user)
            ->where('status', 'menunggu')
            ->first();

        if ($perizinan) {
            $this->edit_id = $perizinan->id_perizinan;
            $this->edit_tanggal_mulai = $perizinan->tanggal_mulai;
            $this->edit_tanggal_selesai = $perizinan->tanggal_selesai;
            $this->edit_keterangan = $perizinan->keterangan;
            $this->edit_file_lama = $perizinan->file_surat;
            $this->edit_file_surat = null;
            $this->isEditing = true;
        }
    }

    public function batalEdit()
    {
        $this->isEditing = false;
        $this->reset(['edit_id', 'edit_tanggal_mulai', 'edit_tanggal_selesai', 'edit_keterangan', 'edit_file_surat', 'edit_file_lama']);
    }

    public function simpanEdit()
    {
        $this->validate([
            'edit_tanggal_mulai'   => 'required|date',
            'edit_tanggal_selesai' => 'required|date|after_or_equal:edit_tanggal_mulai',
            'edit_keterangan'      => 'required|string|max:1000',
            'edit_file_surat'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $perizinan = PerizinanSakit::find($this->edit_id);

        if ($perizinan) {
            $perizinan->tanggal_mulai = $this->edit_tanggal_mulai;
            $perizinan->tanggal_selesai = $this->edit_tanggal_selesai;
            $perizinan->keterangan = $this->edit_keterangan;

            if ($this->edit_file_surat) {
                // Hapus file lama
                if (Storage::disk('public')->exists($perizinan->file_surat)) {
                    Storage::disk('public')->delete($perizinan->file_surat);
                }
                // Simpan file baru
                $path = $this->edit_file_surat->store('surat_sakit', 'public');
                $perizinan->file_surat = $path;
            }

            $perizinan->save();
            session()->flash('success_riwayat', 'Pengajuan berhasil diperbarui.');
            $this->batalEdit();
        }
    }

    public function previewFile($filePath)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $this->preview_file_type = strtolower($extension) === 'pdf' ? 'pdf' : 'image';
        $this->preview_file_url = Storage::url($filePath);
        $this->showPreviewModal = true;
    }

    public function closePreview()
    {
        $this->showPreviewModal = false;
        $this->preview_file_url = null;
    }

    public function render()
    {
        $query = PerizinanSakit::where('karyawan_id', Auth::id() ?? \App\Models\User::first()->id_user);

        if ($this->filterStatus !== 'semua') {
            $query->where('status', $this->filterStatus);
        }

        $riwayat = $query->orderBy('created_at', 'desc')->get();

        return view('livewire.karyawan.riwayat-perizinan-sakit', [
            'riwayat' => $riwayat
        ]);
    }
}
