<?php

namespace App\Livewire\Karyawan;

use App\Models\PerizinanSakit;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class FormPerizinanSakit extends Component
{
    use WithFileUploads;

    public $tanggal_mulai;
    public $tanggal_selesai;
    public $keterangan;
    public $file_surat;

    public function rules()
    {
        return [
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'      => 'required|string|max:1000',
            'file_surat'      => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'tanggal_mulai.required'   => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'keterangan.required'      => 'Keterangan wajib diisi.',
            'file_surat.required'      => 'Surat keterangan sakit wajib diunggah.',
            'file_surat.mimes'         => 'Format file harus berupa JPG, PNG, atau PDF.',
            'file_surat.max'           => 'Ukuran file maksimal 5MB.',
        ];
    }

    public function submitForm()
    {
        $this->validate();

        $path = $this->file_surat->store('surat_sakit', 'public');

        PerizinanSakit::create([
            'karyawan_id'       => Auth::id() ?? \App\Models\User::first()->id_user,
            'tanggal_mulai'     => $this->tanggal_mulai,
            'tanggal_selesai'   => $this->tanggal_selesai,
            'keterangan'        => $this->keterangan,
            'file_surat'        => $path,
            'status'            => 'menunggu',
            'tanggal_pengajuan' => now(),
        ]);

        $this->reset(['tanggal_mulai', 'tanggal_selesai', 'keterangan', 'file_surat']);
        $this->dispatch('perizinan-dikirim');
        session()->flash('success', 'Pengajuan izin sakit berhasil dikirim!');
    }

    public function render()
    {
        return view('livewire.karyawan.form-perizinan-sakit');
    }
}
