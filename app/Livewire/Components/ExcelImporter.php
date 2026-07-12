<?php

namespace App\Livewire\Components;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\Departemen;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelImporter extends Component
{
    use WithFileUploads;

    public string $templatePath = '';
    public string $importClass = '';
    public string $buttonLabel = 'Impor Berkas';
    public string $onSuccessEvent = '';
    public bool $needsPeriodSelection = false;
    public ?int $selectedMonth = null;
    public ?int $selectedYear = null;
    
    public bool $needsRoleSelection = false;
    public string $selectedRole = '';
    public ?int $selectedDepartemen = null;
    public array $departemensList = [];

    public function mount()
    {
        $this->selectedMonth = \Carbon\Carbon::now()->month;
        $this->selectedYear = \Carbon\Carbon::now()->year;
        
        if ($this->needsRoleSelection) {
            $this->departemensList = Departemen::where('status', Status::Active->value)->get()->toArray();
        }
    }

    public $fileExcel;

    public function downloadTemplate($role = null, $departemenId = null)
    {
        if (!$this->templatePath) {
            return;
        }

        $path = public_path($this->templatePath);
        if (!file_exists($path) || is_dir($path)) {
            // Check if it is a route name
            if (\Route::has($this->templatePath)) {
                $params = [];
                if ($this->needsPeriodSelection && $this->selectedMonth && $this->selectedYear) {
                    $params['month'] = $this->selectedMonth;
                    $params['year'] = $this->selectedYear;
                }
                return redirect()->route($this->templatePath, $params);
            }

            // Check if it is a URL path
            if (str_contains($this->templatePath, '/') || url()->isValidUrl($this->templatePath)) {
                $url = url($this->templatePath);
                if ($this->needsPeriodSelection && $this->selectedMonth && $this->selectedYear) {
                    $url = $url . (str_contains($url, '?') ? '&' : '?') . http_build_query([
                        'month' => $this->selectedMonth,
                        'year' => $this->selectedYear
                    ]);
                }
                return redirect($url);
            }

            $this->dispatch('flash-error', message: 'Template tidak ditemukan.');
            return;
        }

        // Modifikasi sel B7 jika role Kepala Departemen dan departemenId terisi
        if ($this->needsRoleSelection && $role === 'kepala_departemen' && $departemenId) {
            try {
                $departemen = Departemen::find($departemenId);
                if ($departemen) {
                    $spreadsheet = IOFactory::load($path);
                    $sheet = $spreadsheet->getActiveSheet();
                    
                    // Isi sel B7 dengan nama departemen
                    $sheet->setCellValue('B7', $departemen->nama_departemen);
                    
                    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                    
                    // Buat file temporary
                    $tempFile = tempnam(sys_get_temp_dir(), 'template_') . '.xlsx';
                    $writer->save($tempFile);
                    
                    return response()->download($tempFile, 'template.xlsx')->deleteFileAfterSend(true);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Gagal memodifikasi template: ' . $e->getMessage());
                // Fallback ke unduh biasa jika gagal modifikasi
            }
        }

        // Jika bukan kepala departemen atau proses modifikasi gagal, unduh aslinya
        return response()->download($path, 'template.xlsx');
    }

    // melakukan validasi file excel yang diupload, memastikan format dan ukuran file sesuai
    // input file excel, memberikan output validasi berhasil atau gagal
    public function import()
    {
        $rules = [
            'fileExcel' => 'required|mimes:xlsx,xls,csv,txt|max:10240', // support csv/txt as well
        ];
        
        $messages = [
            'fileExcel.required' => 'File tidak boleh kosong.',
            'fileExcel.mimes' => 'Format file harus berupa .xlsx, .xls, atau .csv',
            'fileExcel.max' => 'Ukuran file maksimal 10 MB.',
        ];

        if ($this->needsRoleSelection) {
            $rules['selectedRole'] = 'required';
            $messages['selectedRole.required'] = 'Role harus dipilih.';
            
            if ($this->selectedRole === 'kepala_departemen') {
                $rules['selectedDepartemen'] = 'required';
                $messages['selectedDepartemen.required'] = 'Departemen harus dipilih untuk Kepala Departemen.';
            }
        }

        $this->validate($rules, $messages);

        try {
            // Validasi keamanan: Pastikan importClass diizinkan (Anti Arbitrary Class Instantiation)
            $allowedImports = [
                'App\Imports\UsersImport',
                'App\Imports\JadwalsImport',
                'App\Imports\KehadiranImport',
            ];

            if (!in_array($this->importClass, $allowedImports)) {
                $this->dispatch('flash-error', message: 'Class impor tidak valid atau tidak diizinkan.');
                return;
            }

            // Instansiasi class Import
            if ($this->importClass === 'App\Imports\UsersImport') {
                $roleEnum = match($this->selectedRole) {
                    'admin_outsourcing' => UserRole::AdminVendor,
                    'hr'                => UserRole::Hr,
                    'kepala_departemen' => UserRole::KepalaDepartemen,
                    default             => UserRole::AdminVendor,
                };
                $importInstance = new $this->importClass($roleEnum, $this->selectedDepartemen);
            } else {
                $importInstance = new $this->importClass;
            }

            // Jalankan import
            Excel::import($importInstance, $this->fileExcel);

            // Reset file upload dan state form
            $this->reset('fileExcel');
            
            if ($this->needsRoleSelection) {
                $this->reset(['selectedRole', 'selectedDepartemen']);
            }

            // Dispatch flash message sukses
            $this->dispatch('flash-success', message: 'Data berhasil di-import ke sistem!');

            // Beri tahu parent component untuk refresh
            if ($this->onSuccessEvent) {
                $this->dispatch($this->onSuccessEvent);
            }
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $row = $failure->row();
                $attribute = $failure->attribute();
                $err = implode(', ', $failure->errors());
                $errors[] = "Baris {$row} (Kolom {$attribute}): {$err}";
            }
            // Tampilkan error pertama agar user tahu baris mana yang salah
            $this->dispatch('flash-error', message: 'Gagal Impor: ' . ($errors[0] ?? 'Data tidak valid'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Import Error: ' . $e->getMessage());
            $this->dispatch('flash-error', message: 'Gagal mengimpor file. Periksa format data Anda.');
        }
    }

    public function render()
    {
        return view('livewire.components.excel-importer');
    }
}
