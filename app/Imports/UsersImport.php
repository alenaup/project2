<?php

namespace App\Imports;

use App\Models\User;
use App\Enums\Status;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    protected $role;
    protected $departemenId;

    public function __construct(UserRole $role, ?int $departemenId = null)
    {
        $this->role = $role;
        $this->departemenId = $departemenId;
    }

    /**
     * Map each row of Excel into the User model.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new User([
            'nama_lengkap'  => $row['nama_lengkap'],
            'email'         => $row['email'],
            'nomor_tlp'     => $row['nomor_tlp'] ?? null,
            'role'          => $this->role->value,
            'departemen_id' => $this->departemenId,
            'password'      => Hash::make($row['password']),
            'status'        => Status::Active->value,
            'user_id'       => Auth::id(),
        ]);
    }

    /**
     * Define the validation rules for the incoming excel rows.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required|string|max:255',
            'email'        => 'required|email|unique:user,email',
            'nomor_tlp'    => 'nullable|string|max:20',
            'password'     => 'required|string|min:8',
        ];
    }

    /**
     * Tentukan bahwa heading kolom berada pada baris ke-8.
     * Data sesungguhnya akan diimpor mulai dari baris ke-9.
     *
     * @return int
     */
    public function headingRow(): int
    {
        return 8;
    }

    /**
     * Mempersiapkan data sebelum divalidasi.
     * Mengonversi seluruh data input kolom yang seharusnya bertipe teks menjadi string,
     * serta mengembalikan digit '0' di depan jika terpotong pada nomor telepon.
     *
     * @param mixed $data
     * @param mixed $index
     * @return array
     */
    public function prepareForValidation($data, $index): array
    {
        // 1. Cast nama_lengkap ke string
        if (isset($data['nama_lengkap']) && $data['nama_lengkap'] !== null) {
            $data['nama_lengkap'] = (string) $data['nama_lengkap'];
        }

        // 2. Cast email ke string
        if (isset($data['email']) && $data['email'] !== null) {
            $data['email'] = (string) $data['email'];
        }

        // 3. Cast password ke string (mengatasi password angka murni)
        if (isset($data['password']) && $data['password'] !== null) {
            $data['password'] = (string) $data['password'];
        }

        // 4. Cast nomor_tlp ke string dan kembalikan angka 0 jika terpotong
        if (isset($data['nomor_tlp']) && $data['nomor_tlp'] !== null) {
            $data['nomor_tlp'] = (string) $data['nomor_tlp'];

            if (preg_match('/^[1-9][0-9]{8,14}$/', $data['nomor_tlp'])) {
                $data['nomor_tlp'] = '0' . $data['nomor_tlp'];
            }
        }

        return $data;
    }
}
