<?php

namespace App\Livewire\Karyawan;

use App\Services\LemburService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FormPengajuanLembur extends Component
{
    public string $tanggal_lembur = '';
    public string $jam_mulai = '';
    public string $jam_selesai = '';
    public string $keterangan = '';

    public function rules(): array
    {
        return [
            'tanggal_lembur' => 'required|date',
            'jam_mulai'      => 'required',
            'jam_selesai'    => 'required',
            'keterangan'     => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_lembur.required'  => 'Tanggal lembur wajib diisi.',
            'tanggal_lembur.date'      => 'Format tanggal tidak valid.',
            'jam_mulai.required'       => 'Jam mulai wajib diisi.',
            'jam_mulai.date_format'    => 'Format jam mulai tidak valid.',
            'jam_selesai.required'     => 'Jam selesai wajib diisi.',
            'jam_selesai.date_format'  => 'Format jam selesai tidak valid.',
            'jam_selesai.after'        => 'Jam selesai harus setelah jam mulai.',
            'keterangan.required'      => 'Keterangan wajib diisi.',
            'keterangan.max'           => 'Keterangan maksimal 255 karakter.',
        ];
    }

    public function updated(string $propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    // melakukan validasi sebelum menyimpan pengajuan lembur
    // input berupa tanggal lembur, jam mulai, jam selesai, dan keterangan
    // output berupa validasi berhasil atau gagal, jika berhasil maka data akan disimpan ke database
    public function simpanPengajuan(): void
    {
        \Log::info('simpanPengajuan triggered', [
            'tanggal' => $this->tanggal_lembur,
            'mulai' => $this->jam_mulai,
            'selesai' => $this->jam_selesai,
            'keterangan' => $this->keterangan
        ]);

        $this->validate();

        (new LemburService)->createLembur();

        $this->reset(['tanggal_lembur', 'jam_mulai', 'jam_selesai', 'keterangan']);
        $this->resetValidation();
        $this->dispatch('lembur-diajukan');
        session()->flash('success', 'Pengajuan lembur berhasil dikirim!');
    }

    public function render()
    {
        $user = Auth::user();
        if (!$user) {
            $user = \App\Models\User::first();
        }

        return view('livewire.Karyawan.form-pengajuan-lembur', [
            'user' => $user,
        ]);
    }
}
