<?php

namespace App\Livewire\Karyawan;

use App\Services\PerizinanSakitService;
use Livewire\Component;
use Livewire\WithFileUploads;

class FormPerizinanSakit extends Component
{
    use WithFileUploads;

    public $tanggal_mulai;

    public $tanggal_selesai;

    public $keterangan;

    public $file_surat;
    
    public $latitude;
    public $longitude;
    public $perluAbsenKeluar = false;

    public function mount()
    {
        // Cek apakah karyawan saat ini sudah absen masuk tapi belum keluar
        $kehadiranService = new \App\Services\KehadiranService;
        $kehadiran = $kehadiranService->cekKehadiran();

        if ($kehadiran && $kehadiran->waktu_masuk && !$kehadiran->waktu_keluar) {
            $this->perluAbsenKeluar = true;
        }
    }

    public function rules()
    {
        $rules = [
            'tanggal_mulai' => [
                'required',
                'date',
                'after_or_equal:'.now()->subDays(7)->format('Y-m-d'),
                'before_or_equal:'.now()->format('Y-m-d'),
            ],

            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
            ],

            'keterangan' => [
                'required',
                'string',
                'max:1000',
            ],

            'file_surat' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],
        ];
        
        if ($this->perluAbsenKeluar) {
            $rules['latitude'] = 'required';
            $rules['longitude'] = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'keterangan.required' => 'Keterangan wajib diisi.',
            'file_surat.mimes' => 'Format file harus berupa JPG, PNG, atau PDF.',
            'file_surat.max' => 'Ukuran file maksimal 5MB.',
            'latitude.required' => 'Lokasi harus diambil karena Anda perlu melakukan absen keluar otomatis.',
            'longitude.required' => 'Lokasi harus diambil karena Anda perlu melakukan absen keluar otomatis.',
        ];
    }

    public function submitForm()
    {
        $this->validate();

        $path = $this->file_surat ? $this->file_surat->store('surat_sakit', 'public') : null;

        (new PerizinanSakitService)->membuatFormulir(
            $this->tanggal_mulai,
            $this->tanggal_selesai,
            $this->keterangan,
            $path
        );

        $wasCheckedOut = false;
        if ($this->perluAbsenKeluar) {
            $kehadiranService = new \App\Services\KehadiranService;
            $kehadiran = $kehadiranService->cekKehadiran();
            if ($kehadiran) {
                $kehadiran->update([
                    'waktu_keluar' => now(),
                    'latitude_keluar' => $this->latitude,
                    'longitude_keluar' => $this->longitude,
                ]);
            }
            $this->perluAbsenKeluar = false;
            $wasCheckedOut = true;
        }

        $this->resetForm(true);
        $this->dispatch('perizinan-dikirim');
        session()->flash('success', 'Pengajuan izin sakit berhasil dikirim' . ($wasCheckedOut ? ' dan Anda telah otomatis diabsen keluar!' : '!'));
    }

    public function resetForm($isSubmit = false)
    {
        $this->reset(['tanggal_mulai', 'tanggal_selesai', 'keterangan', 'file_surat', 'latitude', 'longitude']);
        $this->resetValidation();
        
        if (!$isSubmit) {
            $this->dispatch('form-reset');
        }
    }

    public function render()
    {
        return view('livewire.karyawan.form-perizinan-sakit');
    }
}
