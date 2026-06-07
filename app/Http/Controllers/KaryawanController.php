<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    /**
     * Retrieve paginated list of outsourcing employees (role: karyawan).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // 1. Ambil query filter/pencarian jika ada
        $search = $request->query('search');
        $status = $request->query('status');
        $vendorId = $request->query('vendor_id');

        // 2. Query data user dengan role karyawan
        $query = User::with('outsourcing')
            ->where('role', UserRole::Karyawan->value);

        // Filter Pencarian (Nama Lengkap, NIP, atau Email)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter Status
        if (!empty($status)) {
            $query->where('status', $status);
        }

        // Filter Vendor/Outsourcing
        if (!empty($vendorId)) {
            $query->where('outsourcing_id', $vendorId);
        }

        // 3. Paginate data
        $karyawan = $query->paginate(10);

        // 4. Return response JSON
        return response()->json($karyawan);
    }
}
