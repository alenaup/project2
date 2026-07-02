<?php

namespace App\Imports;

use App\Models\User;
use App\Enums\Status;
use App\Enums\UserRole;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class KaryawanDataSheetImport implements ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Trim inputs
            $nip = isset($row['nip']) ? trim((string)$row['nip']) : '';
            $nama = isset($row['nama_lengkap']) ? trim((string)$row['nama_lengkap']) : '';
            $email = isset($row['email']) ? trim((string)$row['email']) : '';
            $telepon = isset($row['nomor_telepon']) ? trim((string)$row['nomor_telepon']) : '';
            $alamat = isset($row['alamat']) ? trim((string)$row['alamat']) : '';
            $deptId = isset($row['id_departemen']) ? intval($row['id_departemen']) : null;

            if (empty($nip) && empty($nama) && empty($email)) {
                continue; // skip empty rows
            }

            User::create([
                'nama_lengkap' => $nama,
                'email' => $email,
                'password' => bcrypt('admin123'),
                'nomor_tlp' => $telepon,
                'alamat' => $alamat,
                'nip' => 'NIP-' . ltrim(str_replace('NIP-', '', $nip)),
                'departemen_id' => $deptId,
                'tanggal_keluar' => null,
                'tanggal_masuk' => null,
                'role' => UserRole::Karyawan->value,
                'status' => Status::Pending->value,
                'user_id' => auth()->id(),
                'outsourcing_id' => auth()->user()->outsourcing_id,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nip' => 'required|string|max:50',
            'nama_lengkap' => 'required|string|min:3|max:100',
            'email' => 'required|email|unique:user,email',
            'nomor_telepon' => 'required|string|max:20',
            'alamat' => 'required|string|max:255',
            'id_departemen' => 'required|exists:departemen,id_departemen',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nip.required' => 'NIP wajib diisi.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.min' => 'Nama lengkap minimal 3 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'nomor_telepon.required' => 'Nomor telepon wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'id_departemen.required' => 'ID Departemen wajib diisi.',
            'id_departemen.exists' => 'ID Departemen tidak valid atau tidak ditemukan.',
        ];
    }

    public function prepareForValidation($data, $index): array
    {
        // Clean up keys and cast values to string
        if (isset($data['nip'])) {
            $data['nip'] = (string)$data['nip'];
        }
        if (isset($data['nama_lengkap'])) {
            $data['nama_lengkap'] = (string)$data['nama_lengkap'];
        }
        if (isset($data['email'])) {
            $data['email'] = (string)$data['email'];
        }
        if (isset($data['alamat'])) {
            $data['alamat'] = (string)$data['alamat'];
        }
        if (isset($data['id_departemen'])) {
            $data['id_departemen'] = (string)$data['id_departemen'];
        }
        if (isset($data['nomor_telepon'])) {
            $data['nomor_telepon'] = (string)$data['nomor_telepon'];
            // Normalize telephone number leading zero
            if (preg_match('/^[1-9][0-9]{8,14}$/', $data['nomor_telepon'])) {
                $data['nomor_telepon'] = '0' . $data['nomor_telepon'];
            }
        }
        return $data;
    }
}
