<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Enums\Validasi;
use App\Models\Departemen;
use App\Models\Lembur;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class HRController extends Controller
{
    /**
     * Export rekap lembur karyawan ke Excel.
     */
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
            $dept = Departemen::find($departemenId);
            if ($dept) {
                $deptNama = $dept->nama_departemen;
            }
        }

        $query = Lembur::with(['karyawan.departemen', 'karyawan.outsourcing'])
            ->where('status', Status::Active->value)
            ->where('status_validasi', Validasi::Valid->value)
            ->whereDate('mulai_lembur', '>=', $startDate)
            ->whereDate('selesai_lembur', '<=', $endDate);

        if (!empty($departemenId)) {
            $query->whereHas('karyawan', function ($q) use ($departemenId) {
                $q->where('departemen_id', $departemenId);
            });
        }

        $lemburs = $query->orderBy('mulai_lembur', 'asc')->get();

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
}
