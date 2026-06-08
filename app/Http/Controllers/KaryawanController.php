<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Outsourcing;
use App\Enums\UserRole;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $vendorId = $request->query('vendor_id');

        $query = User::with('outsourcing')
            ->where('role', UserRole::Karyawan->value);

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

        // Paginasi
        $karyawan = $query->paginate(10);

        // Return JSON
        return response()->json($karyawan);
    }

    public function getVendors(Request $request)
    {
        $vendors = Outsourcing::select('id_outsourcing', 'nama_outsourcing')->paginate(5);
        return response()->json($vendors);
    }
}
