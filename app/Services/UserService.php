<?php

namespace App\Services;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\Departemen;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    // CREATE user baru untuk user Super Admin, Kepala Departemen, Admin Vendor, dan HR
    // dipakai di:
    // UserManagement super Admin
    public function generateUser($nama, $email, $no_tlp, $role, $pass, $dept_id = null)
    {
        if(Auth::check() && Auth::user()->role !== UserRole::SuperAdmin->value) {
            throw new \Exception('Hanya Super Admin yang dapat membuat user baru.');
        }
        $query = User::create([
            'nama_lengkap'  => $nama,
            'email'         => $email,
            'nomor_tlp'     => $no_tlp,
            'role'          => $role,
            'password'      => Hash::make($pass),
            'status'        => Status::Active->value,
            'user_id'       => Auth::id(),
            'departemen_id' => $dept_id,
        ]);

        return $query;
    }

    // method mengambil detail user
    public function getUserDetail(int $userId): ?array
    {
        $user = User::with([
            'departemen',
            'outsourcing'
        ])->find($userId);

        if (!$user) {
            return null;
        }
        return [
            'nama_lengkap'    => $user->nama_lengkap,
            'email'           => $user->email,
            'nomor_tlp'       => $user->nomor_tlp ?? '-',
            'nip'             => $user->nip ?? '-',
            'alamat'          => $user->alamat ?? '-',
            'status'          => $user->status,
            'tanggal_masuk'   => $user->tanggal_masuk
                ? date('d F Y', strtotime($user->tanggal_masuk))
                : '-',
            'departemen_nama' => $user->departemen->nama_departemen ?? '-',
            'vendor_nama'     => $user->outsourcing->nama_outsourcing ?? '-',
        ];
    }

    //
    public function getUserById($id)
    {
        return User::findOrFail($id);;
    }

    public function getUserSuperAdmin()
    {
        return User::where('role', UserRole::SuperAdmin->value)
            ->where('status', Status::Active->value)
            ->get();
    }

    public function getUserKepalaDepartemen()
    {
        return User::where('role', UserRole::KepalaDepartemen->value)
            ->where('status', Status::Active->value)
            ->get();
    }


    public function getUserAdmin()
    {
        return User::where('role', UserRole::AdminVendor->value)
            ->where('status', Status::Active->value)
            ->get();
    }

    public function getUserHr()
    {
        return User::where('role', UserRole::Hr->value)
            ->where('status', Status::Active->value)
            ->get();
    }

    public function getUserKaryawanByDepartemen($departemen)
    {
        return User::where('role', UserRole::Karyawan->value)
            ->where('departemen_id', $departemen)
            ->where('status', Status::Active->value)
            ->get();
    }

    public function getUserKaryawanById($user)
    {
        return User::where('role', UserRole::Karyawan->value)
            ->where('id_user', $user)
            ->first();
    }

    public function getKaryawanByOutsourcing($outsourcing, $jenis)
    {
        if ($jenis == 'array') {
            return User::where('role', UserRole::Karyawan->value)
                ->where('outsourcing_id', $outsourcing)
                ->where('status', Status::Active->value)
                ->whereNull('tanggal_keluar')
                ->pluck('id_user')
                ->toArray();
        } elseif ($jenis == 'object') {
            return User::where('role', UserRole::Karyawan->value)
                ->where('outsourcing_id', $outsourcing)
                ->where('status', Status::Active->value)
                ->whereNull('tanggal_keluar')
                ->get();
        }
    }

    public function getOutsourcing()
    {
        if (!Auth::check()) {
            return [];
        }
        $query = User::where('outsourcing_id', Auth::user()->outsourcing_id)
            ->where('status', Status::Active->value)
            ->where('role', UserRole::Karyawan->value)
            ->pluck('id_user')
            ->toArray();

        return $query;
    }

    public function updateUser(int $userId, array $data): bool
    {
        $user = User::findOrFail($userId);
        return $user->update($data);
    }

    public function toggleUserStatus(int $userId): array
    {
        $user = User::findOrFail($userId);
        $newStatus = $user->status === Status::Active->value
            ? Status::Inactive->value
            : Status::Active->value;

        $user->update(['status' => $newStatus]);

        $label = $newStatus === Status::Active->value ? 'diaktifkan' : 'dinonaktifkan';

        return [
            'user' => $user,
            'label' => $label
        ];
    }

    public function deleteUser(int $userId): bool
    {
        $user = User::findOrFail($userId);
        return $user->delete();
    }

    /**
     * Mengambil data karyawan per departemen dengan filter pencarian dan paginasi.
     */
    public function getKaryawanByDepartemenPaginated($deptId, string $search = '', int $perPage = 10)
    {
        $query = User::where('role', UserRole::Karyawan->value);

        if ($deptId) {
            $query->where('departemen_id', $deptId);
        } else {
            $query->whereNull('id_user');
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%' . $search . '%')
                  ->orWhere('nip', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        return $query->orderBy('nama_lengkap', 'asc')->paginate($perPage);
    }

    /**
     * Mengambil objek departemen berdasarkan ID.
     */
    public function getDepartemenById($deptId)
    {
        return Departemen::find($deptId);
    }

    /**
     * Mengambil data user terpaginasi dengan filter activeTab, search query, dan filterStatus.
     */

    // dipakai di :
    // UserManagement super Admin
    public function getUsersPaginated(string $activeTab, string $search = '', string $filterStatus = 'semua', int $perPage = 10)
    {
        $query = User::query();

        // Filter berdasarkan tab aktif
        match ($activeTab) {
            'admin_outsourcing' => $query->where('role', UserRole::AdminVendor->value),
            'hr'                => $query->where('role', UserRole::Hr->value),
            'kepala_departemen' => $query->where('role', UserRole::KepalaDepartemen->value),
            default             => $query->whereIn('role', [
                UserRole::AdminVendor->value,
                UserRole::Hr->value,
                UserRole::KepalaDepartemen->value,
            ]),
        };

        // Pencarian nama atau email
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan status aktif/nonaktif
        if ($filterStatus !== 'semua') {
            $query->where('status', $filterStatus);
        }

        return $query->latest('id_user')->paginate($perPage);
    }

    /**
     * Dapatkan statistik karyawan outsourcing untuk dashboard HR.
     */
    public function getOutsourcingStats(): array
    {
        $totalOutsourcingAktif = User::whereNotNull('outsourcing_id')
            ->whereNull('tanggal_keluar')
            ->count();

        $totalOutsourcingTerdaftar = User::whereNotNull('outsourcing_id')->count();

        $totalAjuanPending = User::whereNotNull('outsourcing_id')
            ->where('role', UserRole::Karyawan->value)
            ->where('status', Status::Inactive->value)
            ->count();

        return [
            'outsourcing_aktif'     => $totalOutsourcingAktif,
            'outsourcing_terdaftar' => $totalOutsourcingTerdaftar,
            'ajuan_pending'         => $totalAjuanPending,
        ];
    }

    /**
     * Dapatkan detail user beserta data outsourcing.
     */
    public function getUserWithOutsourcing(int $userId): ?User
    {
        return User::with('outsourcing')->find($userId);
    }

    /**
     * Mengambil data karyawan outsourcing yang berstatus pending (menunggu persetujuan).
     * Mendukung pencarian berdasarkan NIP, nama, dan nama vendor.
     */
    public function getKaryawanPendingPaginated(string $search = '', int $perPage = 10)
    {
        return User::with('outsourcing')
            ->where('role', UserRole::Karyawan->value)
            ->where('status', Status::Pending->value)
            ->whereNull('tanggal_keluar')
            ->when($search, function ($query) use ($search) {
                $keyword = '%' . $search . '%';
                $query->where(function ($q) use ($keyword) {
                    $q->where('nip', 'like', $keyword)
                      ->orWhere('nama_lengkap', 'like', $keyword)
                      ->orWhereHas('outsourcing', function ($sub) use ($keyword) {
                          $sub->where('nama_outsourcing', 'like', $keyword);
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Menyetujui ajuan karyawan outsourcing.
     */
    public function approveKaryawan(int $userId): ?User
    {
        $user = User::find($userId);
        if ($user) {
            $user->update(['status' => Status::Active->value]);
        }
        return $user;
    }

    /**
     * Menolak ajuan karyawan outsourcing.
     */
    public function rejectKaryawan(int $userId, string $alasan): ?User
    {
        $user = User::find($userId);
        if ($user) {
            $user->update([
                'status'         => Status::Inactive->value,
                'tanggal_keluar' => null,
            ]);
        }
        return $user;
    }

    /**
     * Hitung total karyawan aktif untuk vendor tertentu.
     */
    public function getKaryawanByVendorCount(int $vendorId): int
    {
        return User::where('role', UserRole::Karyawan->value)
            ->where('status', Status::Active->value)
            ->where('outsourcing_id', $vendorId)
            ->count();
    }

    /**
     * Ambil data karyawan aktif untuk vendor tertentu dengan batas paging manual.
     */
    public function getKaryawanByVendorPaginated(int $vendorId, int $page, int $perPage)
    {
        return User::with(['outsourcing', 'departemen'])
            ->where('role', UserRole::Karyawan->value)
            ->where('status', Status::Active->value)
            ->where('outsourcing_id', $vendorId)
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();
    }

    /**
     * Membuat pengajuan karyawan baru oleh Admin Outsourcing.
     */
    public function createKaryawanSubmission(array $data, int $authId, int $outsourcingId): User
    {
        return User::create([
            'nama_lengkap' => $data['nama_lengkap'],
            'email' => $data['email'],
            'password' => bcrypt('admin123'),
            'nomor_tlp' => $data['nomor_tlp'],
            'alamat' => $data['alamat'],
            'nip' => 'NIP-' . ltrim(str_replace('NIP-', '', $data['nip'])),
            'departemen_id' => $data['departemen_id'],
            'tanggal_keluar' => null,
            'tanggal_masuk' => null,
            'role' => UserRole::Karyawan->value,
            'status' => Status::Pending->value,
            'user_id' => $authId,
            'outsourcing_id' => $outsourcingId,
        ]);
    }

    /**
     * Membatalkan/menghapus pengajuan karyawan yang masih pending.
     */
    public function cancelKaryawanSubmission(int $userId): bool
    {
        $user = User::where('id_user', $userId)
            ->where('role', UserRole::Karyawan->value)
            ->where('status', Status::Pending->value)
            ->first();

        if ($user) {
            $user->delete();
            return true;
        }
        return false;
    }

    /**
     * Mengambil data pengajuan yang ditolak (inactive) untuk diajukan kembali.
     */
    public function getInactiveSubmission(int $userId): ?User
    {
        return User::where('id_user', $userId)
            ->where('role', UserRole::Karyawan->value)
            ->where('status', Status::Inactive->value)
            ->whereNull('tanggal_keluar')
            ->first();
    }

    /**
     * Mengirim ulang pengajuan karyawan yang sebelumnya ditolak.
     */
    public function resubmitKaryawan(int $userId, array $data): bool
    {
        $user = $this->getInactiveSubmission($userId);

        if ($user) {
            return $user->update([
                'nip' => 'NIP-' . ltrim(str_replace('NIP-', '', $data['nip'])),
                'nama_lengkap' => $data['nama_lengkap'],
                'email' => $data['email'],
                'nomor_tlp' => $data['nomor_tlp'],
                'alamat' => $data['alamat'],
                'departemen_id' => $data['departemen_id'],
                'status' => Status::Pending->value,
            ]);
        }
        return false;
    }

    /**
     * Mengambil data seluruh ajuan karyawan yang diajukan oleh Admin Outsourcing tertentu (terpaginasi).
     */
    public function getSubmissionsPaginated(int $authId, int $outsourcingId, string $search = '', int $perPage = 10)
    {
        return User::with('departemen')
            ->where('role', UserRole::Karyawan->value)
            ->where('user_id', $authId)
            ->where('outsourcing_id', $outsourcingId)
            ->when($search, function ($query) use ($search) {
                $keyword = '%' . $search . '%';
                $query->where(function ($q) use ($keyword) {
                    $q->where('nip', 'like', $keyword)
                      ->orWhere('nama_lengkap', 'like', $keyword)
                      ->orWhere('email', 'like', $keyword);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Update data profil karyawan oleh Admin Outsourcing.
     */
    public function updateKaryawan(int $userId, array $data): ?User
    {
        $user = User::find($userId);
        if ($user) {
            $user->update([
                'nama_lengkap' => $data['nama_lengkap'],
                'email'        => $data['email'],
                'nomor_tlp'    => $data['nomor_tlp'] ?? null,
                'alamat'       => $data['alamat'] ?? null,
            ]);
        }
        return $user;
    }

    /**
     * Menghapus karyawan dari database.
     */
    public function deleteKaryawan(int $userId): ?User
    {
        $user = User::find($userId);
        if ($user) {
            $user->delete();
        }
        return $user;
    }

    /**
     * Mengambil data karyawan aktif terpaginasi dengan filter pencarian dan vendor.
     */
    public function getKaryawanAktifPaginated(string $search = '', ?int $outsourcingId = null, int $perPage = 10)
    {
        return User::with('outsourcing')
            ->where('role', UserRole::Karyawan->value)
            ->where('status', Status::Active->value)
            ->when($outsourcingId, function ($query) use ($outsourcingId) {
                $query->where('outsourcing_id', $outsourcingId);
            })
            ->when($search, function ($query) use ($search) {
                $keyword = '%' . $search . '%';
                $query->where(function ($q) use ($keyword) {
                    $q->where('nip', 'like', $keyword)
                      ->orWhere('nama_lengkap', 'like', $keyword)
                      ->orWhere('email', 'like', $keyword);
                });
            })
            ->orderBy('nama_lengkap')
            ->paginate($perPage);
    }

    public function getKaryawanPaginated(string $search = '', string $status = '', ?int $vendorId = null, int $perPage = 10)
    {
        $query = User::with('outsourcing')
            ->where('role', UserRole::Karyawan->value);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($vendorId)) {
            $query->where('outsourcing_id', $vendorId);
        }

        return $query->paginate($perPage);
    }

    public function getKaryawanByOutsourcingWithDepartemen(int $outsourcingId)
    {
        return User::with('departemen')
            ->where('role', UserRole::Karyawan->value)
            ->where('outsourcing_id', $outsourcingId)
            ->where('status', Status::Active->value)
            ->whereNull('tanggal_keluar')
            ->orderBy('nama_lengkap', 'asc')
            ->get();
    }

    public function getActiveKaryawanList($deptId = null)
    {
        $query = User::where('role', UserRole::Karyawan->value)
            ->where('status', Status::Active->value);

        if ($deptId) {
            $query->where('departemen_id', $deptId);
        }

        return $query->orderBy('nama_lengkap', 'asc')->get();
    }

    public function getActiveKaryawanListSimple($deptId = null)
    {
        $query = User::where('role', UserRole::Karyawan->value)
            ->where('status', Status::Active->value);

        if ($deptId) {
            $query->where('departemen_id', $deptId);
        }

        return $query->orderBy('nama_lengkap', 'asc')->get(['id_user', 'nama_lengkap']);
    }
}

