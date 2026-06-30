<?php

namespace App\Livewire\KepalaDepartemen;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class ManageKaryawan extends Component
{
    use WithPagination;

    public string $search = '';
    
    // State Modal Detail
    public bool $isDetailOpen = false;
    public ?int $selectedUserId = null;
    public ?array $selectedUser = null;

    protected UserService $userService;

    /**
     * Bootstrapping dependency injection untuk UserService.
     *
     * @param UserService $userService
     */
    public function boot(UserService $userService)
    {
        $this->userService = $userService;
    }

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Membuka modal detail data karyawan.
     *
     * @param int $userId
     */
    public function showDetail(int $userId)
    {
        $detail = $this->userService->getUserDetail($userId);
        if ($detail) {
            $this->selectedUserId = $userId;
            $this->selectedUser = $detail;
            $this->isDetailOpen = true;
        }
    }

    /**
     * Menutup modal detail.
     */
    public function closeDetail()
    {
        $this->isDetailOpen = false;
        $this->reset(['selectedUserId', 'selectedUser']);
    }

    public function render()
    {
        $deptId = Auth::check() ? Auth::user()->departemen_id : null;
        
        // Panggil data via service (tidak ada models yang digunakan pada livewire)
        $departemen = $this->userService->getDepartemenById($deptId);
        $karyawans = $this->userService->getKaryawanByDepartemenPaginated($deptId, $this->search, 10);

        return view('livewire.kepala-departemen.manage-karyawan', [
            'karyawans'  => $karyawans,
            'departemen' => $departemen,
        ]);
    }
}
