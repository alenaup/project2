<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Jadwal;
use App\Models\Shift;
use App\Enums\UserRole;
use App\Enums\Status;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KepalaDepartemenController extends Controller
{
    public function dashboard()
    {
        return view('kepala-departement.dashboard');
    }

    public function getDashboardSummary()
    {
        $deptId = Auth::check() ? Auth::user()->departemen_id : null;

        $queryKaryawan = User::where('role', UserRole::Karyawan->value)->where('status', Status::Active->value);
        if ($deptId) {
            $queryKaryawan->where('departemen_id', $deptId);
        }

        $totalKaryawan = $queryKaryawan->count();
        $userIds = $queryKaryawan->pluck('id_user');

        $today = Carbon::today();

        $hadir = \App\Models\Kehadiran::whereDate('tanggal', $today)
            ->whereIn('karyawan_id', $userIds)
            ->whereHas('tipeKehadiran', function ($q) {
                $q->whereIn('status_kehadiran', ['hadir', 'terlambat']);
            })->count();

        $terlambat = \App\Models\Kehadiran::whereDate('tanggal', $today)
            ->whereIn('karyawan_id', $userIds)
            ->whereHas('tipeKehadiran', function ($q) {
                $q->where('status_kehadiran', 'terlambat');
            })->count();

        $izinCuti = \App\Models\Kehadiran::whereDate('tanggal', $today)
            ->whereIn('karyawan_id', $userIds)
            ->whereHas('tipeKehadiran', function ($q) {
                $q->whereIn('status_kehadiran', ['izin', 'cuti', 'sakit', 'mankir']);
            })->count();

        return response()->json([
            'totalKaryawan' => $totalKaryawan,
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'izinCuti' => $izinCuti,
        ]);
    }

    public function getJadwalKaryawan(Request $request)
    {
        // Ambil tanggal start dan end date-nya
        $startDate = $request->query('start_date', Carbon::now()->startOfWeek()->format('Y-m-d'));
        $endDate = $request->query('end_date', Carbon::now()->endOfWeek()->format('Y-m-d'));

        // Query users. Filter berdasarkan department
        $query = User::where('role', UserRole::Karyawan->value)
            ->with(['jadwal' => function($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->where('tanggal_mulai', '<=', $endDate)
                      ->where('tanggal_akhir', '>=', $startDate);
                })->with('shift');
            }]);

        // If logged in user has department, filter by it.
        if (Auth::check() && Auth::user()->departemen_id) {
            $query->where('departemen_id', Auth::user()->departemen_id);
        }

        $karyawans = $query->paginate(10);

        $formattedData = [];
        foreach ($karyawans->items() as $karyawan) {
            $names = explode(' ', $karyawan->nama_lengkap);
            $initials = strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));

            $shiftsArr = array_fill(0, 7, null);

            foreach ($karyawan->jadwal as $j) {
                if ($j->shift) {
                    $shiftType = strtolower($j->shift->nama_shift ?? '');

                    // Populate the shifts array for each day in the requested week that falls within the jadwal period
                    $jStart = Carbon::parse($j->tanggal_mulai);
                    $jEnd = Carbon::parse($j->tanggal_akhir);

                    $weekStart = Carbon::parse($startDate);
                    $weekEnd = Carbon::parse($endDate);

                    // Determine overlap
                    $overlapStart = $jStart->max($weekStart);
                    $overlapEnd = $jEnd->min($weekEnd);

                    if ($overlapStart->lte($overlapEnd)) {
                        for ($date = $overlapStart->copy(); $date->lte($overlapEnd); $date->addDay()) {
                            $diffInDays = $weekStart->diffInDays($date);
                            if ($diffInDays >= 0 && $diffInDays < 7) {
                                $shiftsArr[$diffInDays] = $shiftType;
                            }
                        }
                    }
                }
            }

            $formattedData[] = [
                'id' => $karyawan->id_user,
                'name' => $karyawan->nama_lengkap,
                'role' => 'Karyawan', // Or specific role string if you have it in user model
                'initials' => $initials,
                'shifts' => $shiftsArr,
            ];
        }

        $shifts = Shift::all();

        return response()->json([
            'employees' => $formattedData,
            'pagination' => [
                'current_page' => $karyawans->currentPage(),
                'last_page' => $karyawans->lastPage(),
                'total' => $karyawans->total(),
            ],
            'shifts' => $shifts,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }

    public function getAllKaryawan()
    {
        $deptId = Auth::check() ? Auth::user()->departemen_id : null;

        $query = User::where('role', UserRole::Karyawan->value)->where('status', Status::Active->value);
        if ($deptId) {
            $query->where('departemen_id', $deptId);
        }

        $karyawans = $query->orderBy('nama_lengkap', 'asc')->get(['id_user', 'nama_lengkap']);

        return response()->json($karyawans);
    }

    public function storeJadwalKaryawan(Request $request)
    {
        $todayStr = Carbon::today()->format('Y-m-d');

        // Normalisasi input user_ids jika frontend mengirimkan user_id tunggal
        $input = $request->all();
        if (isset($input['user_id']) && !isset($input['user_ids'])) {
            $input['user_ids'] = [$input['user_id']];
        }
        $request->merge($input);

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:user,id_user',
            'shift_id' => 'required|exists:shift,id_shift',
            'start_date' => 'required|date|after_or_equal:' . $todayStr,
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'user_ids.required' => 'Karyawan harus dipilih.',
            'user_ids.array' => 'Format karyawan tidak valid.',
            'user_ids.*.exists' => 'Salah satu karyawan tidak terdaftar.',
            'shift_id.required' => 'Shift harus dipilih.',
            'start_date.required' => 'Tanggal mulai harus diisi.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'end_date.required' => 'Tanggal selesai harus diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ]);

        $createdJadwals = [];
        $createdCount = 0;

        foreach ($request->user_ids as $userId) {
            $jadwal = Jadwal::create([
                'status' => Status::Active->value,
                'tanggal_mulai' => $request->start_date,
                'tanggal_akhir' => $request->end_date,
                'shift_id' => $request->shift_id,
                'dibuat_oleh' => Auth::id() ?? 1,
                'nama_periode' => 'Periode ' . Carbon::parse($request->start_date)->format('M Y'),
            ]);

            $user = User::find($userId);
            if ($user) {
                $user->jadwal()->attach($jadwal->id_jadwal);
                $createdJadwals[] = $jadwal;
                $createdCount++;
            }
        }

        return response()->json([
            'message' => 'Jadwal berhasil ditambahkan untuk ' . $createdCount . ' karyawan.',
            'count' => $createdCount
        ]);
    }
}

