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

        $karyawans = $query->paginate(5);

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

    public function storeJadwalKaryawan(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:user,id_user',
            'shift_id' => 'required|exists:shift,id_shift',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $jadwal = Jadwal::create([
            'status' => Status::Active->value,
            'tanggal_mulai' => $request->start_date,
            'tanggal_akhir' => $request->end_date,
            'shift_id' => $request->shift_id,
            'dibuat_oleh' => Auth::id() ?? 1, // Fallback if no auth
            'nama_periode' => 'Periode ' . Carbon::parse($request->start_date)->format('M Y'),
        ]);

        // Attach to user
        $user = User::find($request->user_id);
        $user->jadwal()->attach($jadwal->id_jadwal);

        return response()->json(['message' => 'Jadwal berhasil ditambahkan', 'jadwal' => $jadwal]);
    }
}
