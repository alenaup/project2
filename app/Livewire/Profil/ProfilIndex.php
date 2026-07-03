<?php

namespace App\Livewire\Profil;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfilIndex extends Component
{
    use WithFileUploads;

    public $nama_lengkap;
    public $email;
    public $nomor_tlp;
    public $nip;

    public $password_lama;
    public $password_baru;
    public $password_baru_confirmation;

    public $foto_baru;
    public $versi_foto;

    public function mount()
    {
        $user = Auth::user();

        $this->nama_lengkap = $user->nama_lengkap;
        $this->email = $user->email;
        $this->nomor_tlp = $user->nomor_tlp;
        $this->nip = $user->nip;
        $this->versi_foto = time();
    }

    public function updateProfil()
    {
        $this->validate([
            'nomor_tlp' => 'nullable|string|max:20',
        ], [
            'nomor_tlp.max' => 'Nomor telepon maksimal 20 karakter.',
        ]);

        $user = Auth::user();
        $user->nomor_tlp = $this->nomor_tlp;
        $user->save();

        session()->flash('success_profil', 'Profil berhasil diperbarui!');
    }

    public function updatePassword()
    {
        $this->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:6|confirmed',
        ], [
            'password_lama.required' => 'Password lama wajib diisi.',
            'password_baru.required' => 'Password baru wajib diisi.',
            'password_baru.min' => 'Password minimal 6 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($this->password_lama, $user->password)) {
            $this->addError('password_lama', 'Password lama tidak sesuai.');
            return;
        }

        $user->password = Hash::make($this->password_baru);
        $user->save();

        $this->reset(['password_lama', 'password_baru', 'password_baru_confirmation']);
        session()->flash('success_password', 'Password berhasil diubah!');
    }

    public function updateFoto()
    {
        $this->validate([
            'foto_baru' => 'required|image|max:2048', // Maksimal 2MB
        ], [
            'foto_baru.required' => 'Pilih foto terlebih dahulu.',
            'foto_baru.image' => 'File harus berupa gambar.',
            'foto_baru.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $filename = 'profil_' . Auth::id() . '.jpg';

        // Simpan ke storage/app/public/profiles/
        $this->foto_baru->storeAs('profiles', $filename, 'public');

        // Reset file input dan perbarui cache buster
        $this->reset('foto_baru');
        $this->versi_foto = time();

        session()->flash('success_foto', 'Foto profil berhasil diperbarui!');
    }

    public function render()
    {
        return view('livewire.profil.profil-index');
    }
}
