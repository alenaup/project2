<?php

namespace App\Livewire\KepalaDepartement;

use App\Enums\Validasi;
use App\Models\Lembur;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PersetujuanPengajuan extends Component
{
    /**
     * ID pengajuan lembur yang sedang dipilih untuk detail modal.
     */
    public ?int $selectedLemburId = null;

    /**
     * Memilih pengajuan lembur untuk ditampilkan pada modal.
     *
     * @param int $id
     * @return void
     */
    public function selectLembur(int $id)
    {
        $this->selectedLemburId = $id;
    }

    /**
     * Menutup modal detail dan mereset pilihan lembur.
     *
     * @return void
     */
    public function closeModal()
    {
        $this->selectedLemburId = null;
    }

    /**
     * Mendapatkan daftar pengajuan lembur untuk karyawan di departemen yang sama.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getLemburListProperty()
    {
        $user = Auth::user();
        if (!$user || !$user->departemen_id) {
            return collect();
        }

        return Lembur::with('karyawan')
            ->whereHas('karyawan', function ($query) use ($user) {
                $query->where('departemen_id', $user->departemen_id);
            })
            ->latest('tanggal_dibuat')
            ->get();
    }

    /**
     * Mendapatkan data pengajuan lembur yang saat ini dipilih.
     *
     * @return Lembur|null
     */
    public function getSelectedLemburProperty()
    {
        if (!$this->selectedLemburId) {
            return null;
        }

        return Lembur::with('karyawan')->find($this->selectedLemburId);
    }

    /**
     * Menyetujui pengajuan lembur yang dipilih.
     *
     * @return void
     */
    public function approve()
    {
        if (!$this->selectedLemburId) {
            return;
        }

        $lembur = Lembur::find($this->selectedLemburId);
        if ($lembur && $lembur->status_validasi === Validasi::Pending->value) {
            $lembur->update([
                'status_validasi' => Validasi::Valid->value,
                'pemvalidasi_id' => Auth::id(),
            ]);

            session()->flash('success', 'Pengajuan lembur berhasil disetujui.');
        }

        $this->closeModal();
    }

    /**
     * Menolak pengajuan lembur yang dipilih.
     *
     * @return void
     */
    public function reject()
    {
        if (!$this->selectedLemburId) {
            return;
        }

        $lembur = Lembur::find($this->selectedLemburId);
        if ($lembur && $lembur->status_validasi === Validasi::Pending->value) {
            $lembur->update([
                'status_validasi' => Validasi::Invalid->value,
                'pemvalidasi_id' => Auth::id(),
            ]);

            session()->flash('success', 'Pengajuan lembur berhasil ditolak.');
        }

        $this->closeModal();
    }

    /**
     * Render view komponen Livewire.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.kepala-departement.persetujuan-pengajuan', [
            'lemburList' => $this->lemburList
        ]);
    }
}

