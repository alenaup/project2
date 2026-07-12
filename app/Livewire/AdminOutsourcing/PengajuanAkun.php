<?php

namespace App\Livewire\AdminOutsourcing;

use App\Services\UserService;
use App\Services\DepartemenService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class PengajuanAkun extends Component
{
    use WithPagination;
    use WithFileUploads;

    // Excel import property
    public $fileExcel;

    // Search & Pagination
    public string $search = '';
    public int $perPage = 10;

    // Modal Control properties
    public ?int $selectedId = null;

    // Form fields for submitting a new employee
    public string $nip = '';
    public string $nama_lengkap = '';
    public string $email = '';
    public string $nomor_tlp = '';
    public string $alamat = '';
    public ?int $departemen_id = null;

    // Form fields for editing/resubmitting
    public string $editNip = '';
    public string $editNama = '';
    public string $editEmail = '';
    public string $editTelepon = '';
    public string $editAlamat = '';
    public ?int $editDepartemenId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // melakukan submission data karyawan yang diajukan, melakuka vaalidasi
    // output event message hasil dari pengajuan
    public function submit(UserService $userService): void
    {
        $validated = $this->validate([
            'nip' => 'required|string|max:50',
            'nama_lengkap' => 'required|string|min:3|max:100',
            'email' => 'required|email|unique:user,email',
            'nomor_tlp' => 'required|string|max:20',
            'alamat' => 'required|string|max:255',
            'departemen_id' => 'required|exists:departemen,id_departemen',
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.min' => 'Nama lengkap minimal 3 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'nomor_tlp.required' => 'Nomor telepon wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'departemen_id.required' => 'Departemen wajib dipilih.',
        ]);

        $userService->createKaryawanSubmission([
            'nama_lengkap' => $this->nama_lengkap,
            'email' => $this->email,
            'nomor_tlp' => $this->nomor_tlp,
            'alamat' => $this->alamat,
            'nip' => $this->nip,
            'departemen_id' => $this->departemen_id,
        ], auth()->id(), auth()->user()->outsourcing_id);

        session()->flash('success', '✅ Pengajuan data karyawan berhasil dikirim.');
        $this->dispatch('flash-success', message: 'Pengajuan data karyawan berhasil dikirim.');

        $this->resetForm();
        $this->dispatch('close-add-modal');
    }

    private function resetForm(): void
    {
        $this->nip = '';
        $this->nama_lengkap = '';
        $this->email = '';
        $this->nomor_tlp = '';
        $this->alamat = '';
        $this->departemen_id = null;
    }

    public function openCancel(int $userId): void
    {
        $this->selectedId = $userId;
    }

    public function cancelSubmission(UserService $userService): void
    {
        $success = $userService->cancelKaryawanSubmission($this->selectedId);

        if ($success) {
            session()->flash('success', '🗑️ Pengajuan data karyawan berhasil dibatalkan.');
            $this->dispatch('flash-success', message: 'Pengajuan data karyawan berhasil dibatalkan.');
        } else {
            session()->flash('error', 'Gagal membatalkan pengajuan.');
            $this->dispatch('flash-error', message: 'Gagal membatalkan pengajuan.');
        }

        $this->selectedId = null;
        $this->dispatch('close-cancel-modal');
    }

    public function openEdit(int $userId, UserService $userService): void
    {
        $user = $userService->getInactiveSubmission($userId);

        if ($user) {
            $this->selectedId = $userId;
            $this->editNip = str_replace('NIP-', '', $user->nip);
            $this->editNama = $user->nama_lengkap;
            $this->editEmail = $user->email;
            $this->editTelepon = $user->nomor_tlp ?? '';
            $this->editAlamat = $user->alamat ?? '';
            $this->editDepartemenId = $user->departemen_id;
        }
    }

    public function closeEdit(): void
    {
        $this->selectedId = null;
        $this->editNip = '';
        $this->editNama = '';
        $this->editEmail = '';
        $this->editTelepon = '';
        $this->editAlamat = '';
        $this->editDepartemenId = null;
    }

    public function resubmit(UserService $userService): void
    {
        $validated = $this->validate([
            'editNip' => 'required|string|max:50',
            'editNama' => 'required|string|min:3|max:100',
            'editEmail' => 'required|email|unique:user,email,' . $this->selectedId . ',id_user',
            'editTelepon' => 'required|string|max:20',
            'editAlamat' => 'required|string|max:255',
            'editDepartemenId' => 'required|exists:departemen,id_departemen',
        ], [
            'editNip.required' => 'NIP wajib diisi.',
            'editNama.required' => 'Nama lengkap wajib diisi.',
            'editNama.min' => 'Nama lengkap minimal 3 karakter.',
            'editEmail.required' => 'Email wajib diisi.',
            'editEmail.email' => 'Format email tidak valid.',
            'editEmail.unique' => 'Email sudah digunakan.',
            'editTelepon.required' => 'Nomor telepon wajib diisi.',
            'editAlamat.required' => 'Alamat wajib diisi.',
            'editDepartemenId.required' => 'Departemen wajib dipilih.',
        ]);

        $success = $userService->resubmitKaryawan($this->selectedId, [
            'nip' => $this->editNip,
            'nama_lengkap' => $this->editNama,
            'email' => $this->editEmail,
            'nomor_tlp' => $this->editTelepon,
            'alamat' => $this->editAlamat,
            'departemen_id' => $this->editDepartemenId,
        ]);

        if ($success) {
            session()->flash('success', '✅ Pengajuan data karyawan berhasil diperbarui dan dikirim kembali.');
            $this->dispatch('flash-success', message: 'Pengajuan data karyawan berhasil diperbarui dan dikirim kembali.');
        } else {
            session()->flash('error', 'Gagal mengirim kembali pengajuan.');
            $this->dispatch('flash-error', message: 'Gagal mengirim kembali pengajuan.');
        }

        $this->closeEdit();
        $this->dispatch('close-edit-modal');
    }



    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        
        // --- Sheet 1: Petunjuk ---
        $sheetInstructions = $spreadsheet->getActiveSheet();
        $sheetInstructions->setTitle('Petunjuk Pengisian');
        
        // Title
        $sheetInstructions->setCellValue('A1', 'ECOGREEN OUTSOURCING');
        $sheetInstructions->mergeCells('A1:F1');
        $sheetInstructions->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF3C8B5E'));
        
        // Subtitle
        $sheetInstructions->setCellValue('A2', 'Aturan Pengisian Template Pengajuan Karyawan');
        $sheetInstructions->mergeCells('A2:F2');
        $sheetInstructions->getStyle('A2')->getFont()->setItalic(true)->setSize(11);

        // Rules
        $sheetInstructions->setCellValue('A4', 'Aturan Umum:');
        $sheetInstructions->getStyle('A4')->getFont()->setBold(true);

        $rules = [
            '1. Isi data karyawan pada Sheet kedua ("Form Data Karyawan").',
            '2. Kolom NIP, Nama Lengkap, Email, Nomor Telepon, Alamat, dan ID Departemen wajib diisi.',
            '3. Jangan menggabungkan kolom (merge cells) pada Sheet data.',
            '4. NIP harus unik dan tidak boleh mengandung karakter khusus selain strip (-).',
            '5. Email harus valid dan belum terdaftar di sistem.',
            '6. Nomor Telepon harus diawali dengan angka 0 atau kode negara (contoh: 081234567890).',
            '7. Alamat harus diisi lengkap (alamat domisili).',
            '8. ID Departemen harus berupa angka bulat yang valid.'
        ];
        foreach ($rules as $index => $rule) {
            $rowNum = 5 + $index;
            $sheetInstructions->setCellValue('A' . $rowNum, $rule);
            $sheetInstructions->mergeCells('A' . $rowNum . ':F' . $rowNum);
        }

        // Departments Table
        $startRow = 15;
        $sheetInstructions->setCellValue('A' . $startRow, 'Daftar ID Departemen yang Valid:');
        $sheetInstructions->getStyle('A' . $startRow)->getFont()->setBold(true);
        
        $sheetInstructions->setCellValue('A' . ($startRow + 1), 'ID');
        $sheetInstructions->setCellValue('B' . ($startRow + 1), 'Nama Departemen');
        $sheetInstructions->getStyle('A' . ($startRow + 1) . ':B' . ($startRow + 1))->getFont()->setBold(true);
        
        $depts = app(DepartemenService::class)->getAllDepartemen();
        $currRow = $startRow + 2;
        foreach ($depts as $dept) {
            $sheetInstructions->setCellValue('A' . $currRow, $dept->id_departemen);
            $sheetInstructions->setCellValue('B' . $currRow, $dept->nama_departemen);
            $currRow++;
        }
        
        $sheetInstructions->getColumnDimension('A')->setAutoSize(true);
        $sheetInstructions->getColumnDimension('B')->setAutoSize(true);

        // --- Sheet 2: Form Data ---
        $sheetData = $spreadsheet->createSheet();
        $sheetData->setTitle('Form Data Karyawan');
        
        // Headers
        $sheetData->setCellValue('A1', 'NIP *');
        $sheetData->setCellValue('B1', 'Nama Lengkap *');
        $sheetData->setCellValue('C1', 'Email *');
        $sheetData->setCellValue('D1', 'Nomor Telepon *');
        $sheetData->setCellValue('E1', 'Alamat *');
        $sheetData->setCellValue('F1', 'ID Departemen *');
        
        $sheetData->getStyle('A1:F1')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
        $sheetData->getStyle('A1:F1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF3C8B5E');
        
        // Autofit columns
        foreach (range('A', 'F') as $col) {
            $sheetData->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="template_pengajuan_karyawan.xlsx"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }

    // melakukan validasi tipe, ukuran file excel, dan menyesuaikan format dengan tamplate
    public function importExcel(): void
    {
        $this->validate([
            'fileExcel' => 'required|mimes:xlsx,xls|max:10240',
        ], [
            'fileExcel.required' => 'Berkas Excel wajib dipilih.',
            'fileExcel.mimes' => 'Berkas harus berformat .xlsx atau .xls',
            'fileExcel.max' => 'Ukuran berkas maksimal 10 MB.',
        ]);

        try {
            $import = new \App\Imports\KaryawanSubmissionsImport();
            \Maatwebsite\Excel\Facades\Excel::import($import, $this->fileExcel->getRealPath());

            $this->reset('fileExcel');
            session()->flash('success', ' Pengajuan data karyawan dari Excel berhasil diimpor.');
            $this->dispatch('flash-success', message: 'Pengajuan data karyawan dari Excel berhasil diimpor.');
            $this->dispatch('close-import-modal');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $row = $failure->row();
                $attribute = $failure->attribute();
                $errs = implode(', ', $failure->errors());
                $errors[] = "Baris {$row} (Kolom {$attribute}): {$errs}";
            }
            $errorMsg = 'Gagal Impor: ' . ($errors[0] ?? 'Data tidak valid');
            session()->flash('error', $errorMsg);
            $this->dispatch('flash-error', message: $errorMsg);
            $this->dispatch('close-import-modal');
        } catch (\Exception $e) {
            $errorMsg = 'Gagal mengimpor file. Pastikan format kolom sesuai dengan template.';
            session()->flash('error', $errorMsg);
            $this->dispatch('flash-error', message: $errorMsg);
        }
    }

    public function render(UserService $userService, DepartemenService $departemenService)
    {
        return view('livewire.admin-outsourcing.pengajuan-akun', [
            'submissions' => $userService->getSubmissionsPaginated(auth()->id(), auth()->user()->outsourcing_id, $this->search, $this->perPage),
            'departments' => $departemenService->getAllDepartemen(),
        ]);
    }
}
