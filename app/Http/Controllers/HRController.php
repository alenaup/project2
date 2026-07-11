<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Enums\Validasi;
use App\Services\LemburService;
use App\Services\RekapService;
use App\Services\UserService;
use App\Services\OutsourcingService;
use App\Services\DepartemenService;
use App\Services\KehadiranService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class HRController extends Controller
{
    protected $lemburService;
    protected $rekapService;
    protected $userService;
    protected $outsourcingService;
    protected $departemenService;
    protected $kehadiranService;

    public function __construct(
        LemburService $lemburService,
        RekapService $rekapService,
        UserService $userService,
        OutsourcingService $outsourcingService,
        DepartemenService $departemenService,
        KehadiranService $kehadiranService
    ) {
        $this->lemburService = $lemburService;
        $this->rekapService = $rekapService;
        $this->userService = $userService;
        $this->outsourcingService = $outsourcingService;
        $this->departemenService = $departemenService;
        $this->kehadiranService = $kehadiranService;
    }

    /**
     * Export rekap lembur karyawan ke Excel.
     */
    // memuat data yang dimasukkan dan membuat struktur tamplate excel 
    // 
    public function exportLembur(Request $request)
    {
        $request->validate([
            'month' => 'required|string|size:2',
            'year' => 'required|integer',
            'departemen_id' => 'nullable|string',
        ]);

        $month = (int) $request->input('month');
        $year = (int) $request->input('year');
        $departemenId = $request->input('departemen_id');

        if ($month === 1) {
            $bulanLalu = 12;
            $tahunLalu = $year - 1;
        } else {
            $bulanLalu = $month - 1;
            $tahunLalu = $year;
        }

        $startDate = sprintf('%04d-%02d-26', $tahunLalu, $bulanLalu);
        $endDate = sprintf('%04d-%02d-25', $year, $month);

        $deptNama = 'Semua Departemen';
        if (!empty($departemenId)) {
            $dept = $this->departemenService->findDepartemen($departemenId);
            if ($dept) {
                $deptNama = $dept->nama_departemen;
            }
        }

        $lemburs = $this->lemburService->getValidLemburForExport($startDate, $endDate, $departemenId);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Heading Excel
        $sheet->setCellValue('A1', 'ECOGREEN LEMBUR');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('065F46')); // Emerald-800
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Departemen: ' . $deptNama);
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $periodeStr = Carbon::parse($startDate)->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($endDate)->translatedFormat('d M Y');
        $sheet->setCellValue('A3', 'Periode: ' . $periodeStr);
        $sheet->mergeCells('A3:K3');
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 2. Table Headers
        $headers = [
            'No',
            'NIP',
            'Nama Karyawan',
            'Departemen',
            'Vendor',
            'Tanggal Mulai',
            'Jam Mulai',
            'Tanggal Selesai',
            'Jam Selesai',
            'Durasi (Menit)',
            'Status Validasi'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '5', $header);
            $sheet->getStyle($col . '5')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
            $sheet->getStyle($col . '5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('047857'); // Emerald-700
            $sheet->getStyle($col . '5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }

        // 3. Populate Data Rows
        $row = 6;
        foreach ($lemburs as $index => $lembur) {
            $nip = ($lembur->karyawan->nip ?? null) && (int) $lembur->karyawan->nip !== 0
                ? 'NIP-' . $lembur->karyawan->nip
                : '-';
            
            $startDt = Carbon::parse($lembur->mulai_lembur);
            $endDt = Carbon::parse($lembur->selesai_lembur);
            
            // Hitung durasi dalam menit
            $durasi = $startDt->diffInMinutes($endDt);

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $nip);
            $sheet->setCellValue('C' . $row, $lembur->karyawan->nama_lengkap ?? '-');
            $sheet->setCellValue('D' . $row, $lembur->karyawan->departemen->nama_departemen ?? '-');
            $sheet->setCellValue('E' . $row, $lembur->karyawan->outsourcing->nama_outsourcing ?? '-');
            $sheet->setCellValue('F' . $row, $startDt->translatedFormat('d M Y'));
            $sheet->setCellValue('G' . $row, $startDt->format('H:i'));
            $sheet->setCellValue('H' . $row, $endDt->translatedFormat('d M Y'));
            $sheet->setCellValue('I' . $row, $endDt->format('H:i'));
            $sheet->setCellValue('J' . $row, $durasi);
            $sheet->setCellValue('K' . $row, 'Disetujui'); // Hanya data lembur tervalidasi yang di-export

            // Style row cells
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Zebra striping
            if ($row % 2 === 0) {
                $sheet->getStyle('A' . $row . ':K' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F3F4F6');
            }

            $row++;
        }

        // Apply Borders and auto column width
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'E5E7EB'],
                ],
            ],
        ];
        $sheet->getStyle('A5:K' . ($row - 1))->applyFromArray($borderStyle);

        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Generate response stream
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Ecogreen_Lembur_' . str_replace(' ', '_', $deptNama) . '_' . sprintf('%02d', $month) . '_' . $year . '.xlsx';

        $downloadToken = $request->input('download_token');
        if ($downloadToken) {
            setcookie('download_token', $downloadToken, time() + 60, '/');
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export rekap absensi karyawan ke Excel.
     */
    public function exportAbsensi(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|integer',
            'bulan' => 'required|string|regex:/^\d{4}-\d{2}$/',
        ]);

        $vendorId = (int) $request->input('vendor_id');
        $bulanStr = $request->input('bulan');

        $carbonBulan = Carbon::createFromFormat('Y-m', $bulanStr);
        $awal = $carbonBulan->copy()->subMonth()->setDay(26);
        $akhir = $carbonBulan->copy()->setDay(25);
        $periodeAwal = $awal->format('Y-m-d');
        $periodeAkhir = $akhir->format('Y-m-d');
        $jumlahHariDalamBulan = (int) $awal->diffInDays($akhir) + 1;

        // Cari rekap yang diajukan oleh Admin Outsourcing dari vendorId ini
        $rekapRecord = $this->rekapService->getRekapRecord($vendorId, $periodeAwal, $periodeAkhir);

        // JIKA tidak ada atau belum dikirim, tidak boleh di-export
        if (!$rekapRecord || !$rekapRecord->tanggal_rekap) {
            return redirect()->back()->with('error', 'Ekspor gagal: Rekapan absensi belum diajukan oleh Admin Outsourcing.');
        }

        $statusRekap = 'Belum Diajukan';
        if ($rekapRecord) {
            $statusVal = $rekapRecord->status_validasi;
            if ($statusVal === \App\Enums\Validasi::Valid->value) {
                $statusRekap = 'Disetujui';
            } elseif ($statusVal === \App\Enums\Validasi::Invalid->value) {
                $statusRekap = 'Ditolak';
            } else {
                $statusRekap = 'Menunggu Persetujuan';
            }
        }

        $vendor = $this->outsourcingService->getOutsourcingById($vendorId);
        $vendorNama = $vendor ? $vendor->nama_outsourcing : 'Vendor';

        $karyawans = $this->userService->getKaryawanByOutsourcingWithDepartemen($vendorId);

        if ($karyawans->isEmpty()) {
            return redirect()->back()->with('error', 'Ekspor gagal: Tidak ada data karyawan ditemukan.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Heading
        $sheet->setCellValue('A1', 'ECOGREEN REKAPITULASI ABSENSI');
        $lastColIndex = 4 + $jumlahHariDalamBulan + 4;
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIndex);
        $sheet->mergeCells('A1:' . $lastColLetter . '1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('065F46'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Vendor: ' . $vendorNama);
        $sheet->mergeCells('A2:' . $lastColLetter . '2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $periodeStr = $awal->translatedFormat('d M Y') . ' s/d ' . $akhir->translatedFormat('d M Y');
        $sheet->setCellValue('A3', 'Periode: ' . $periodeStr);
        $sheet->mergeCells('A3:' . $lastColLetter . '3');
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A4', 'Status Rekap: ' . $statusRekap);
        $sheet->mergeCells('A4:' . $lastColLetter . '4');
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(
            $statusRekap === 'Disetujui' ? '047857' : ($statusRekap === 'Ditolak' ? 'DC2626' : 'D97706')
        ));
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row = 6;
        $headers = ['No', 'NIP', 'Nama Karyawan', 'Departemen'];
        
        $colIdx = 1;
        foreach ($headers as $h) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheet->setCellValue($colLetter . $row, $h);
            $sheet->getStyle($colLetter . $row)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
            $sheet->getStyle($colLetter . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('047857');
            $sheet->getStyle($colLetter . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $colIdx++;
        }

        $awalCarbon = Carbon::parse($periodeAwal);
        for ($i = 1; $i <= $jumlahHariDalamBulan; $i++) {
            $tgl = $awalCarbon->copy()->addDays($i - 1);
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheet->setCellValue($colLetter . $row, $tgl->day);
            $sheet->getStyle($colLetter . $row)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
            $sheet->getStyle($colLetter . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('047857');
            $sheet->getStyle($colLetter . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $colIdx++;
        }

        $summaries = ['H', 'A', 'S/I', 'L'];
        foreach ($summaries as $s) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheet->setCellValue($colLetter . $row, $s);
            $sheet->getStyle($colLetter . $row)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
            $sheet->getStyle($colLetter . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('047857');
            $sheet->getStyle($colLetter . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $colIdx++;
        }

        $borderHeaderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '047857'],
                ],
            ],
        ];
        $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->applyFromArray($borderHeaderStyle);
        $sheet->getRowDimension($row)->setRowHeight(22);

        $startDataRow = $row + 1;
        $row++;

        $grandH = $grandA = $grandSI = $grandL = 0;

        foreach ($karyawans as $index => $user) {
            $nip = ($user->nip ?? null) && (int) $user->nip !== 0 ? 'NIP-' . $user->nip : '-';
            
            $sheet->setCellValueByColumnAndRow(1, $row, $index + 1);
            $sheet->setCellValueByColumnAndRow(2, $row, $nip);
            $sheet->setCellValueByColumnAndRow(3, $row, $user->nama_lengkap);
            $sheet->setCellValueByColumnAndRow(4, $row, $user->departemen->nama_departemen ?? '-');

            $kehadiranData = $this->kehadiranService->getKehadiranStatusForKaryawan($user->id_user, $periodeAwal, $periodeAkhir);

            $mappingKode = [
                'hadir'     => 'H',
                'sakit'     => 'S',
                'izin'      => 'I',
                'mankir'    => 'A',
                'cuti'      => 'L',
                'terlambat' => 'H',
            ];

            $kehadiranMap = [];
            foreach ($kehadiranData as $kehadiran) {
                $tgl  = Carbon::parse($kehadiran->tanggal);
                $urut = (int) $awalCarbon->diffInDays($tgl) + 1;
                $kehadiranMap[$urut] = $mappingKode[$kehadiran->status_kehadiran] ?? '-';
            }

            $hadir     = collect($kehadiranMap)->filter(fn($v) => $v === 'H')->count();
            $mangkir   = collect($kehadiranMap)->filter(fn($v) => $v === 'A')->count();
            $sakitIzin = collect($kehadiranMap)->filter(fn($v) => in_array($v, ['S', 'I']))->count();
            $cuti      = collect($kehadiranMap)->filter(fn($v) => $v === 'L')->count();

            $grandH  += $hadir;
            $grandA  += $mangkir;
            $grandSI += $sakitIzin;
            $grandL  += $cuti;

            $colIdx = 5;
            for ($i = 1; $i <= $jumlahHariDalamBulan; $i++) {
                $val = $kehadiranMap[$i] ?? '-';
                $sheet->setCellValueByColumnAndRow($colIdx, $row, $val);
                $sheet->getStyleByColumnAndRow($colIdx, $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $cell = $sheet->getCellByColumnAndRow($colIdx, $row);
                if ($val === 'H') {
                    $cell->getStyle()->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('047857'))->setBold(true);
                } elseif ($val === 'A') {
                    $cell->getStyle()->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('DC2626'))->setBold(true);
                } elseif (in_array($val, ['S', 'I'])) {
                    $cell->getStyle()->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('D97706'))->setBold(true);
                } elseif ($val === 'L') {
                    $cell->getStyle()->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('7C3AED'))->setBold(true);
                }
                
                $colIdx++;
            }

            $sheet->setCellValueByColumnAndRow($colIdx, $row, $hadir);
            $sheet->getStyleByColumnAndRow($colIdx, $row)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('047857'));
            $sheet->getStyleByColumnAndRow($colIdx, $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $colIdx++;

            $sheet->setCellValueByColumnAndRow($colIdx, $row, $mangkir);
            $sheet->getStyleByColumnAndRow($colIdx, $row)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('DC2626'));
            $sheet->getStyleByColumnAndRow($colIdx, $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $colIdx++;

            $sheet->setCellValueByColumnAndRow($colIdx, $row, $sakitIzin);
            $sheet->getStyleByColumnAndRow($colIdx, $row)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('D97706'));
            $sheet->getStyleByColumnAndRow($colIdx, $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $colIdx++;

            $sheet->setCellValueByColumnAndRow($colIdx, $row, $cuti);
            $sheet->getStyleByColumnAndRow($colIdx, $row)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('7C3AED'));
            $sheet->getStyleByColumnAndRow($colIdx, $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $colIdx++;

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            if ($row % 2 === 0) {
                $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F9FAFB');
            }

            $row++;
        }

        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, '');
        $sheet->setCellValue('C' . $row, 'TOTAL REKAP');
        $sheet->getStyle('C' . $row)->getFont()->setBold(true);
        $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('D' . $row, '');

        $colIdx = 5 + $jumlahHariDalamBulan;
        $sheet->setCellValueByColumnAndRow($colIdx, $row, $grandH);
        $sheet->getStyleByColumnAndRow($colIdx, $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $colIdx++;

        $sheet->setCellValueByColumnAndRow($colIdx, $row, $grandA);
        $sheet->getStyleByColumnAndRow($colIdx, $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $colIdx++;

        $sheet->setCellValueByColumnAndRow($colIdx, $row, $grandSI);
        $sheet->getStyleByColumnAndRow($colIdx, $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $colIdx++;

        $sheet->setCellValueByColumnAndRow($colIdx, $row, $grandL);
        $sheet->getStyleByColumnAndRow($colIdx, $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $colIdx++;

        $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('E2F0D9');

        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'D1D5DB'],
                ],
            ],
        ];
        $sheet->getStyle('A' . $startDataRow . ':' . $lastColLetter . $row)->applyFromArray($borderStyle);

        for ($i = 1; $i <= $lastColIndex; $i++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Ecogreen_Absensi_' . str_replace(' ', '_', $vendorNama) . '_' . $bulanStr . '.xlsx';

        $downloadToken = $request->input('download_token');
        if ($downloadToken) {
            setcookie('download_token', $downloadToken, time() + 60, '/');
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
