<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Services\UserService;

/**
 * Class DashboardStats
 *
 * Livewire component untuk menampilkan statistik jumlah pengguna
 * pada dashboard Super Admin.
 */
class DashboardStats extends Component
{
    // jika memiliki update pada data pengguna, maka komponen ini akan di-refresh
    protected $listeners = [
        'userAdded' => '$refresh'
    ];
    // variabel untuk menyimpan data statistik
    protected UserService $userService;

    // boot method untuk menginisialisasi UserService
    public function boot(UserService $userService): void
    {
        $this->userService = $userService;
    }


    // query count untuk masing-masing role pengguna
    public function getTotalAdminVendorProperty(): int
    {
        return $this->userService->getUserAdmin()->count();
    }

    public function getTotalHrProperty(): int
    {
        return $this->userService->getUserHr()->count();
    }

    public function getTotalKepalaDepartemenProperty(): int
    {
        return $this->userService->getUserKepalaDepartemen()->count();
    }

    // menghitung total pengguna dengan menjumlahkan semua role
    public function getTotalPenggunaProperty(): int
    {
        return $this->totalAdminVendor + $this->totalHr + $this->totalKepalaDepartemen;
    }
    
    // render komponen
    public function render()
    {
        return view('livewire.super-admin.dashboard-stats', [
            'totalAdminVendor'      => $this->totalAdminVendor,
            'totalHr'               => $this->totalHr,
            'totalKepalaDepartemen' => $this->totalKepalaDepartemen,
            'totalPengguna'         => $this->totalPengguna,
        ]);
    }
}
