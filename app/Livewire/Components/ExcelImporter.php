<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ExcelImporter extends Component
{
    use WithFileUploads;

    public string $templatePath = '';
    public string $importClass = '';
    public string $buttonLabel = 'Impor Berkas';
    public string $onSuccessEvent = '';

    public $fileExcel;

    // melakukan validasi file excel yang diupload, memastikan format dan ukuran file sesuai
    // input file excel, memberikan output validasi berhasil atau gagal
    public function import()
    {
        $this->validate([
            'fileExcel' => 'required|mimes:xlsx,xls,csv,txt|max:10240', // support csv/txt as well
        ], [
            'fileExcel.required' => 'File tidak boleh kosong.',
            'fileExcel.mimes' => 'Format file harus berupa .xlsx, .xls, atau .csv',
            'fileExcel.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        try {
            // Instansiasi class Import secara dinamis
            $importInstance = new $this->importClass;

            // Jalankan import
            Excel::import($importInstance, $this->fileExcel->getRealPath());

            // Reset file upload
            $this->reset('fileExcel');

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
            $this->dispatch('flash-error', message: 'Gagal mengimpor file. Periksa format kolom data Anda.');
        }
    }

    public function render()
    {
        return view('livewire.components.excel-importer');
    }
}
