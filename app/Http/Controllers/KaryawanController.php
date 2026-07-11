<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Services\OutsourcingService;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    protected $userService;
    protected $outsourcingService;

    public function __construct(UserService $userService, OutsourcingService $outsourcingService)
    {
        $this->userService = $userService;
        $this->outsourcingService = $outsourcingService;
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $vendorId = $request->query('vendor_id');

        $karyawan = $this->userService->getKaryawanPaginated(
            $search ?? '',
            $status ?? '',
            $vendorId ? (int)$vendorId : null,
            10
        );

        return response()->json($karyawan);
    }

    public function getVendors(Request $request)
    {
        $vendors = $this->outsourcingService->getOutsourcingPaginated(5);
        return response()->json($vendors);
    }
}
