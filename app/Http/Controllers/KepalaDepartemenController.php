<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\UserRole;
use App\Enums\Status;
use App\Services\UserService;
use App\Services\JadwalService;
use App\Services\KehadiranService;
use App\Services\PerizinanSakitService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KepalaDepartemenController extends Controller
{
    protected $userService;
    protected $jadwalService;
    protected $kehadiranService;
    protected $perizinanSakitService;

    public function __construct(
        UserService $userService,
        JadwalService $jadwalService,
        KehadiranService $kehadiranService,
        PerizinanSakitService $perizinanSakitService
    ) {
        $this->userService = $userService;
        $this->jadwalService = $jadwalService;
        $this->kehadiranService = $kehadiranService;
        $this->perizinanSakitService = $perizinanSakitService;
    }

    public function dashboard()
    {
        return view('kepala-departement.dashboard');
    }

    public function getDashboardSummary()
    {
        $deptId = Auth::check() ? Auth::user()->departemen_id : null;

        $karyawans = $this->userService->getActiveKaryawanList($deptId);
        $totalKaryawan = $karyawans->count();
        $userIds = $karyawans->pluck('id_user')->toArray();

        $summary = $this->kehadiranService->getKehadiranSummaryToday($userIds);

        return response()->json([
            'totalKaryawan' => $totalKaryawan,
            'hadir' => $summary['hadir'],
            'terlambat' => $summary['terlambat'],
            'izinCuti' => $summary['izinCuti'],
        ]);
    }

    public function getJadwalKaryawan(Request $request)
    {
        // Ambil tanggal start dan end date-nya
        $startDate = $request->query('start_date', Carbon::now()->startOfWeek()->format('Y-m-d'));
        $endDate = $request->query('end_date', Carbon::now()->endOfWeek()->format('Y-m-d'));

        $deptId = (Auth::check() && Auth::user()->departemen_id) ? Auth::user()->departemen_id : null;
        $karyawans = $this->jadwalService->getJadwalKaryawanPaginated($deptId, $startDate, $endDate, 10);

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
                                $shiftsArr[$diffInDays] = [
                                    'nama' => $shiftType,
                                    'jam_masuk' => $j->shift->jam_masuk ? Carbon::parse($j->shift->jam_masuk)->format('H.i') : 'Libur',
                                ];
                            }
                        }
                    }
                }
            }

            $formattedData[] = [
                'id' => $karyawan->id_user,
                'name' => $karyawan->nama_lengkap,
                'role' => 'Karyawan',
                'initials' => $initials,
                'shifts' => $shiftsArr,
            ];
        }

        $shifts = $this->jadwalService->getAllShifts();

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
        $karyawans = $this->userService->getActiveKaryawanListSimple($deptId);

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

        $deptId = Auth::user()->departemen_id;
        if ($deptId) {
            $invalidCount = \App\Models\User::whereIn('id_user', $request->user_ids)
                ->where(function($q) use ($deptId) {
                    $q->where('departemen_id', '!=', $deptId)
                      ->orWhere('role', '!=', \App\Enums\UserRole::Karyawan->value);
                })
                ->count();

            if ($invalidCount > 0) {
                return response()->json([
                    'message' => 'Anda tidak memiliki hak untuk mengatur jadwal karyawan di luar departemen Anda.'
                ], 403);
            }
        }

        $createdJadwals = [];
        $createdCount = 0;

        foreach ($request->user_ids as $userId) {
            $jadwal = $this->jadwalService->createJadwalForUser(
                $userId,
                $request->only(['start_date', 'end_date', 'shift_id']),
                Auth::id() ?? 1
            );
            if ($jadwal) {
                $createdJadwals[] = $jadwal;
                $createdCount++;
            }
        }

        return response()->json([
            'message' => 'Jadwal berhasil ditambahkan untuk ' . $createdCount . ' karyawan.',
            'count' => $createdCount
        ]);
    }

    /**
     * Mengunduh berkas template jadwal Excel yang otomatis terisi
     * dengan nama dan email karyawan aktif di departemen terkait.
     */
    public function downloadTemplateJadwal(Request $request)
    {
        $templateFile = public_path('templates/tamplate_jadwal_ecogreen.xlsx');

        if (!file_exists($templateFile)) {
            return redirect()->back()->with('error', 'Berkas template excel tidak ditemukan.');
        }

        try {
            // Load berkas template menggunakan PhpSpreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templateFile);
            // Set ke sheet kedua "atur jadwal"
            $spreadsheet->setActiveSheetIndex(1);
            $sheet = $spreadsheet->getActiveSheet();

            $deptId = Auth::check() ? Auth::user()->departemen_id : null;
            $karyawans = $this->userService->getActiveKaryawanList($deptId);

            // Bulan dan Tahun dari parameter URL
            $month = (int) $request->query('month', Carbon::now()->month);
            $year = (int) $request->query('year', Carbon::now()->year);

            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $daysInMonth = $startDate->daysInMonth;

            // Header hari dan tanggal
            $hariIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
            $bulanIndoArr = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

            // Tulis Periode di B7
            $sheet->setCellValue('B7', $bulanIndoArr[$month] . ' ' . $year);

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $colStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($day + 2); // Kolom C = 3
                $currentDate = Carbon::create($year, $month, $day);
                
                // Baris 8: Nama Hari
                $sheet->setCellValue($colStr . '8', $hariIndo[$currentDate->format('l')]);
                $sheet->getStyle($colStr . '8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                
                // Baris 9: Tanggal
                $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($currentDate);
                $sheet->setCellValue($colStr . '9', $excelDate);
                $sheet->getStyle($colStr . '9')->getNumberFormat()->setFormatCode('d/m');
                $sheet->getStyle($colStr . '9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }

            // Isi nomor urut di kolom A dan nama_lengkap di kolom B mulai baris 10
            $startRow = 10;
            foreach ($karyawans as $index => $karyawan) {
                $currentRow = $startRow + $index;
                $sheet->setCellValue('A' . $currentRow, $index + 1);
                $sheet->setCellValue('B' . $currentRow, $karyawan->nama_lengkap);
            }

            // Styling Borders and Backgrounds
            if ($karyawans->count() > 0) {
                $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($daysInMonth + 2);
                $lastRow = $startRow + $karyawans->count() - 1;

                // Border seluruh area tabel (A8 hingga kolom terakhir dan baris terakhir)
                $tableRange = 'A8:' . $lastColLetter . $lastRow;
                $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Background D9D9D9 untuk Header Tanggal (C8 sampai ujung kanan baris 9)
                $dateRange = 'C8:' . $lastColLetter . '9';
                $sheet->getStyle($dateRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9');

                // Background D9D9D9 untuk Nomor dan Nama (A10 sampai B baris terakhir)
                $nameRange = 'A10:B' . $lastRow;
                $sheet->getStyle($nameRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9');
            }

            // Unduh file secara dinamis
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

            $fileName = 'template_jadwal_karyawan_' . strtolower(str_replace(' ', '_', Auth::user()->nama_lengkap ?? 'departemen')) . '.xlsx';

            $downloadToken = $request->input('download_token');
            if ($downloadToken) {
                setcookie('download_token', $downloadToken, time() + 60, '/');
            }

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses template Excel.');
        }
    }

    /**
     * Mengekspor jadwal bulanan seluruh karyawan dalam bentuk file Excel.
     */
    public function exportJadwal(Request $request)
    {
        $month = (int) $request->query('month', Carbon::now()->month);
        $year = (int) $request->query('year', Carbon::now()->year);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        $deptId = Auth::check() ? Auth::user()->departemen_id : null;
        $deptNama = Auth::user()->departemen->nama_departemen ?? 'Semua Departemen';

        $karyawans = $this->userService->getActiveKaryawanList($deptId);

        try {
            $templateFile = public_path('templates/tamplate_jadwal_ecogreen.xlsx');
            if (!file_exists($templateFile)) {
                return redirect()->back()->with('error', 'Berkas template excel tidak ditemukan.');
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templateFile);
            $spreadsheet->setActiveSheetIndex(1); // Gunakan sheet kedua
            $sheet = $spreadsheet->getActiveSheet();

            $hariIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
            $bulanIndoArr = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

            // Tulis Periode di B7
            $sheet->setCellValue('B7', $bulanIndoArr[$month] . ' ' . $year);

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $colStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($day + 2); // Kolom C = 3
                $currentDate = Carbon::create($year, $month, $day);
                
                // Baris 8: Nama Hari
                $sheet->setCellValue($colStr . '8', $hariIndo[$currentDate->format('l')]);
                $sheet->getStyle($colStr . '8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                
                // Baris 9: Tanggal
                $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($currentDate);
                $sheet->setCellValue($colStr . '9', $excelDate);
                $sheet->getStyle($colStr . '9')->getNumberFormat()->setFormatCode('d/m');
                $sheet->getStyle($colStr . '9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }

            // Isi Data Baris Karyawan mulai dari baris 10
            $rowStart = 10;
            $userIds = $karyawans->pluck('id_user')->toArray();

            // Penarikan data kolektif (Eager loading) untuk menghindari query berulang (N+1 Query)
            $allJadwals = \App\Models\Jadwal::whereIn('id_jadwal', function($q) use ($userIds) {
                $q->select('jadwal_id')
                    ->from('karyawan_jadwal')
                    ->whereIn('user_id', $userIds);
            })
            ->where('tanggal_mulai', '<=', $endDate->format('Y-m-d'))
            ->where('tanggal_akhir', '>=', $startDate->format('Y-m-d'))
            ->with('shift')
            ->get();

            // Petakan jadwal ke user_id
            $userJadwals = [];
            foreach ($karyawans as $karyawan) {
                $userJadwals[$karyawan->id_user] = collect();
            }
            // Karena relasi user-jadwal adalah many-to-many, kita bisa ambil relasi pivot atau query bridge.
            // Untuk mempermudah dan tetap cepat, mari ambil data pivot:
            $pivotData = \Illuminate\Support\Facades\DB::table('karyawan_jadwal')
                ->whereIn('user_id', $userIds)
                ->get()
                ->groupBy('user_id');

            $jadwalsById = $allJadwals->keyBy('id_jadwal');

            foreach ($userIds as $uId) {
                $jIds = isset($pivotData[$uId]) ? $pivotData[$uId]->pluck('jadwal_id')->toArray() : [];
                $userJadwals[$uId] = $allJadwals->filter(function($j) use ($jIds) {
                    return in_array($j->id_jadwal, $jIds);
                });
            }

            $allPerizinans = \App\Models\PerizinanSakit::whereIn('karyawan_id', $userIds)
                ->where('status', 'disetujui')
                ->where('tanggal_mulai', '<=', $endDate->format('Y-m-d'))
                ->where('tanggal_selesai', '>=', $startDate->format('Y-m-d'))
                ->get()
                ->groupBy('karyawan_id');

            foreach ($karyawans as $index => $karyawan) {
                $currentRow = $rowStart + $index;
                $sheet->setCellValue('A' . $currentRow, $index + 1);
                $sheet->setCellValue('B' . $currentRow, $karyawan->nama_lengkap);

                // Ambil jadwal karyawan dari memori
                $jadwals = $userJadwals[$karyawan->id_user] ?? collect();

                // Ambil perizinan cuti & sakit dari memori
                $perizinans = $allPerizinans->get($karyawan->id_user) ?? collect();

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $colStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($day + 2);
                    $targetDateStr = Carbon::create($year, $month, $day)->format('Y-m-d');
                    
                    $statusVal = '';

                    // 1. Cek Shift Kerja
                    $matchingJadwal = $jadwals->first(function ($j) use ($targetDateStr) {
                        return $targetDateStr >= $j->tanggal_mulai && $targetDateStr <= $j->tanggal_akhir;
                    });

                    if ($matchingJadwal && $matchingJadwal->shift) {
                        $statusVal = match(strtolower($matchingJadwal->shift->nama_shift)) {
                            'pagi' => 'P',
                            'sore' => 'S',
                            'malam' => 'M',
                            default => ''
                        };
                    }

                    // 2. Cek Cuti / Izin (Cuti & Izin menimpa shift kerja)
                    $matchingIzin = $perizinans->first(function ($p) use ($targetDateStr) {
                        return $targetDateStr >= $p->tanggal_mulai && $targetDateStr <= $p->tanggal_selesai;
                    });

                    if ($matchingIzin) {
                        $statusVal = str_contains(strtolower($matchingIzin->keterangan), 'cuti') ? 'C' : 'I';
                    }

                    $sheet->setCellValue($colStr . $currentRow, $statusVal);
                    $sheet->getStyle($colStr . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                }
            }

            // Styling Borders and Backgrounds
            if ($karyawans->count() > 0) {
                $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($daysInMonth + 2);
                $lastRow = $rowStart + $karyawans->count() - 1;

                // Border seluruh area tabel (A8 hingga kolom terakhir dan baris terakhir)
                $tableRange = 'A8:' . $lastColLetter . $lastRow;
                $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Background D9D9D9 untuk Header Tanggal (C8 sampai ujung kanan baris 9)
                $dateRange = 'C8:' . $lastColLetter . '9';
                $sheet->getStyle($dateRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9');

                // Background D9D9D9 untuk Nomor dan Nama (A10 sampai B baris terakhir)
                $nameRange = 'A10:B' . $lastRow;
                $sheet->getStyle($nameRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9');
            }

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $fileName = 'Jadwal_Kerja_' . str_replace(' ', '_', $deptNama) . '_' . $bulanIndoArr[$month] . '_' . $year . '.xlsx';

            $downloadToken = $request->input('download_token');
            if ($downloadToken) {
                setcookie('download_token', $downloadToken, time() + 60, '/');
            }

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor jadwal bulanan.');
        }
    }
}
