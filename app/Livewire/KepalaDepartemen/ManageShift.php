<?php

namespace App\Livewire\KepalaDepartemen;

use App\Services\JadwalService;
use Livewire\Component;

class ManageShift extends Component
{
    public array $shifts = [];

    // State Modal & Edit
    public bool $isModalOpen = false;
    public $editingShiftId = null;
    public string $editingNama = '';
    public string $jam_masuk = '';
    public string $jam_keluar = '';

    public function mount()
    {
        $this->loadShifts();
    }

    /**
     * Memuat daftar shift default (Pagi, Siang/Sore, Malam) dari database.
     */
    public function loadShifts()
    {
        // Ambil shift dengan ID 1, 2, 3 sesuai seed data default
        $this->shifts = (new JadwalService())->ambilShift();
    }

    /**
     * Membuka form edit waktu shift untuk ID tertentu.
     *
     * @param int $id
     */
    public function editShift($id)
    {
        $shift = (new JadwalService())->getShiftData($id);

        if ($shift) {
            $this->editingShiftId = $id;

            // Tampilkan nama shift Pagi/Siang/Malam secara statik
            $this->editingNama = match((int) $id) {
                1 => 'Pagi',
                2 => 'Siang',
                3 => 'Malam',
                default => $shift->nama_shift
            };

            // Format jam kerja agar pas dengan input type="time" (HH:MM)
            $this->jam_masuk = date('H:i', strtotime($shift->jam_masuk));
            $this->jam_keluar = date('H:i', strtotime($shift->jam_keluar));

            $this->isModalOpen = true;
        }
    }

    /**
     * Menyimpan pembaruan jam_masuk & jam_keluar ke database.
     */
    public function updateShift()
    {
        $this->validate([
            'jam_masuk'  => 'required',
            'jam_keluar' => 'required',
        ], [
            'jam_masuk.required'  => 'Jam masuk harus diisi.',
            'jam_keluar.required' => 'Jam keluar harus diisi.',
        ]);

        $shift = (new JadwalService())->updateShift($this->editingShiftId, $this->jam_masuk, $this->jam_keluar);

        if ($shift ) {
            $this->closeModal();
            $this->loadShifts();

            // Memicu flash success global
            $this->dispatch('flash-success', message: 'Waktu kerja Shift ' . $this->editingNama . ' berhasil diperbarui!');
        } else {
            $this->dispatch('flash-error', message: 'Shift tidak ditemukan di database.');
        }       
    }

    /**
     * Menutup modal edit.
     */
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->reset(['editingShiftId', 'editingNama', 'jam_masuk', 'jam_keluar']);
    }

    public function render()
    {
        return view('livewire.kepala-departemen.manage-shift');
    }
}
