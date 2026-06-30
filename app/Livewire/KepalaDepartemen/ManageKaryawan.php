<?php

namespace App\Livewire\KepalaDepartemen;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Departemen;
use App\Enums\UserRole;
use App\Enums\Status;
use Illuminate\Support\Facades\Auth;

class ManageKaryawan extends Component
{
    use WithPagination;

    public string $search = '';
    
    // State Modal Detail
    public bool $isDetailOpen = false;
    public ?int $selectedUserId = null;
    public ?array $selectedUser = null;

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
        $user = User::with(['departemen', 'outsourcing'])->find($userId);

        if ($user) {
            $this->selectedUserId = $userId;
            $this->selectedUser = [
                'nama_lengkap'    => $user->nama_lengkap,
                'email'           => $user->email,
                'nomor_tlp'       => $user->nomor_tlp ?? '-',
                'nip'             => $user->nip ?? '-',
                'alamat'          => $user->alamat ?? '-',
                'status'          => $user->status,
                'tanggal_masuk'   => $user->tanggal_masuk ? date('d F Y', strtotime($user->tanggal_masuk)) : '-',
                'departemen_nama' => $user->departemen->nama_departemen ?? '-',
                'vendor_nama'     => $user->outsourcing->nama_outsourcing ?? '-',
            ];
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
        $departemen = $deptId ? Departemen::find($deptId) : null;
        
        $query = User::where('role', UserRole::Karyawan->value);

        // Hanya tampilkan karyawan yang berada di departemen Kepala Departemen yang login
        if ($deptId) {
            $query->where('departemen_id', $deptId);
        } else {
            // Jika departemen tidak di-set, filter agar kosong
            $query->whereNull('id_user');
        }

        // Pencarian berdasarkan Nama Lengkap, NIP, atau Email
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                  ->orWhere('nip', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $karyawans = $query->orderBy('nama_lengkap', 'asc')->paginate(10);

        return view('livewire.kepala-departemen.manage-karyawan', [
            'karyawans'  => $karyawans,
            'departemen' => $departemen,
        ]);
    }
}
