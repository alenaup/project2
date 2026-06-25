<?php

namespace App\Livewire\KaryawanOutsourcing;

use App\Enums\Status;
use App\Enums\Validasi;
use App\Models\Lembur;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Carbon;

class PengajuanLembur extends Component
{
    public $tanggal_lembur;
    public $jam_mulai;
    public $jam_selesai;
    public $keterangan;

    // Filters for history
    public $filterValidasi = 'semua';

    protected $rules = [
        'tanggal_lembur' => 'required|date',
        'jam_mulai' => 'required',
        'jam_selesai' => 'required|after:jam_mulai',
        'keterangan' => 'required|string|max:255',
    ];

    protected $messages = [
        'tanggal_lembur.required' => 'Tanggal lembur wajib diisi.',
        'jam_mulai.required' => 'Jam mulai wajib diisi.',
        'jam_selesai.required' => 'Jam selesai wajib diisi.',
        'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
        'keterangan.required' => 'Keterangan pekerjaan wajib diisi.',
        'keterangan.max' => 'Keterangan maksimal 255 karakter.',
    ];

    public function mount()
    {
        $this->tanggal_lembur = date('Y-m-d');
        $this->jam_mulai = '17:00';
        $this->jam_selesai = '20:00';
    }

    public function submit()
    {
        $this->validate();

        $user = Auth::user();
        if (!$user) {
            session()->flash('error', 'Silakan login terlebih dahulu.');
            return;
        }

        // Combine date and time
        $mulaiLembur = Carbon::parse($this->tanggal_lembur . ' ' . $this->jam_mulai);
        $selesaiLembur = Carbon::parse($this->tanggal_lembur . ' ' . $this->jam_selesai);

        Lembur::create([
            'mulai_lembur' => $mulaiLembur,
            'selesai_lembur' => $selesaiLembur,
            'tanggal_dibuat' => now(),
            'status' => Status::Active->value,
            'status_validasi' => Validasi::Pending->value,
            'keterangan' => $this->keterangan,
            'karyawan_id' => $user->id_user,
        ]);

        $this->reset(['keterangan']);
        $this->tanggal_lembur = date('Y-m-d');
        $this->jam_mulai = '17:00';
        $this->jam_selesai = '20:00';

        session()->flash('success', 'Pengajuan lembur Anda berhasil dikirim dan sedang menunggu validasi.');
        
        // Dispatch browser event to show the success modal
        $this->dispatch('overtimeSubmitted');
    }

    public function getRiwayatLemburProperty()
    {
        $user = Auth::user();
        if (!$user) {
            return collect();
        }

        $query = Lembur::where('karyawan_id', $user->id_user)
            ->latest('tanggal_dibuat');

        if ($this->filterValidasi !== 'semua') {
            $query->where('status_validasi', $this->filterValidasi);
        }

        return $query->get();
    }

    public function render()
    {
        return view('livewire.karyawan-outsourcing.pengajuan-lembur', [
            'user' => Auth::user(),
            'riwayatLembur' => $this->riwayatLembur,
        ]);
    }
}
