<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Jadwal;
use App\Models\Shift;
use App\Models\PerizinanSakit;
use App\Enums\Status;
use App\Enums\UserRole;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class JadwalSheetImport implements ToCollection
{
    /**
     * Parse the Excel sheet collection.
     *
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        // 1. Ambil Baris Ke-9 (indeks 8) untuk mengambil daftar tanggal secara dinamis mulai dari Kolom C (indeks 2)
        $tanggalHeaders = [];
        $headerRow = $rows[8] ?? null;

        if ($headerRow) {
            foreach ($headerRow as $col => $rawDate) {
                // Kolom C dimulai dari indeks 2. Lewati kolom A (0) dan B (1)
                if ($col < 2) {
                    continue;
                }

                if ($rawDate === null || trim((string) $rawDate) === '') {
                    continue;
                }

                try {
                    // Konversi format tanggal excel atau teks secara dinamis
                    if (is_numeric($rawDate)) {
                        $tanggalHeaders[$col] = Carbon::instance(
                            \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawDate)
                        )->format('Y-m-d');
                    } else {
                        $tanggalHeaders[$col] = Carbon::parse(trim((string) $rawDate))->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    // Skip kolom jika parsing tanggal gagal
                }
            }
        }

        if (empty($tanggalHeaders)) {
            throw new \Exception("Tidak ditemukan header tanggal yang valid pada baris 9.");
        }

        // 2. Filter Karyawan agar hanya mencakup departemen dari Kepala Departemen yang sedang Login
        $deptId = Auth::check() ? Auth::user()->departemen_id : null;
        $userQuery = User::where('role', UserRole::Karyawan->value)->where('status', Status::Active->value);
        
        if ($deptId) {
            $userQuery->where('departemen_id', $deptId);
        }

        // Caching list karyawan ke memori
        $karyawansMap = $userQuery->get()->keyBy('nama_lengkap');

        // Caching list shift ke memori
        $shifts = Shift::all();

        // 3. Mulai membaca data Karyawan dari Baris Ke-10 (indeks 9) ke bawah
        for ($rowIndex = 9; $rowIndex < $rows->count(); $rowIndex++) {
            $row = $rows[$rowIndex];

            $namaKaryawan = isset($row[1]) ? trim((string) $row[1]) : '';

            if (empty($namaKaryawan)) {
                continue;
            }

            // Cari User dari memori berdasarkan nama_lengkap (karena nama di-generate otomatis dari DB saat download template)
            $user = $karyawansMap->get($namaKaryawan);

            if (!$user) {
                continue; // Karyawan tidak terdaftar/tidak di departemen yang sama, skip
            }

            // 4. Perulangan untuk setiap kolom tanggal dinamis (Kolom C ke kanan)
            foreach ($tanggalHeaders as $col => $tanggal) {
                $statusInput = isset($row[$col]) ? strtolower(trim((string) $row[$col])) : '';

                if ($statusInput === '') {
                    continue; // Sel kosong, skip
                }

                // A. Tangani Shift Kerja (p = Pagi, s = Sore, m = Malam)
                if (in_array($statusInput, ['p', 's', 'm'])) {
                    $namaShift = match ($statusInput) {
                        'p' => 'Pagi',
                        's' => 'Sore',
                        'm' => 'Malam',
                    };

                    // Cari ID shift dari memori
                    $shift = $shifts->first(function ($s) use ($namaShift) {
                        return str_contains(strtolower($s->nama_shift), strtolower($namaShift));
                    });
                    $shiftId = $shift ? $shift->id_shift : 1; // Default ke shift ke-1 jika gagal

                    // Selesaikan jadwal yang tumpang tindih untuk tanggal tunggal ini sebelum mengimpor
                    (new \App\Services\JadwalService)->resolveOverlappingJadwal($user->id_user, $tanggal, $tanggal);

                    // Cek apakah karyawan sudah memiliki jadwal pada tanggal ini
                    $existingJadwal = $user->jadwal()
                        ->whereDate('tanggal_mulai', $tanggal)
                        ->whereDate('tanggal_akhir', $tanggal)
                        ->first();

                    if ($existingJadwal) {
                        // Jika sudah ada, perbarui shift-nya saja
                        $existingJadwal->update([
                            'shift_id' => $shiftId,
                        ]);
                    } else {
                        // Jika belum ada, buat jadwal baru
                        $jadwal = Jadwal::create([
                            'status'          => Status::Active->value,
                            'tanggal_mulai'   => $tanggal,
                            'tanggal_akhir'   => $tanggal,
                            'shift_id'        => $shiftId,
                            'dibuat_oleh'     => Auth::id() ?? 1,
                            'nama_periode'    => 'Periode ' . Carbon::parse($tanggal)->format('M Y'),
                            'toleransi_telat' => '00:15:00',
                        ]);

                        $user->jadwal()->attach($jadwal->id_jadwal);
                    }
                }
                // B. Tangani Cuti dan Izin (c = Cuti, i = Izin)
                elseif (in_array($statusInput, ['c', 'i'])) {
                    $keterangan = $statusInput === 'c' ? 'Cuti (Impor Excel)' : 'Izin (Impor Excel)';

                    // Cek apakah sudah terdaftar perizinan di tanggal tersebut
                    $existingIzin = PerizinanSakit::where('karyawan_id', $user->id_user)
                        ->whereDate('tanggal_mulai', $tanggal)
                        ->first();

                    if (!$existingIzin) {
                        PerizinanSakit::create([
                            'karyawan_id'       => $user->id_user,
                            'tanggal_mulai'     => $tanggal,
                            'tanggal_selesai'   => $tanggal,
                            'keterangan'        => $keterangan,
                            'status'            => 'disetujui', // Langsung otomatis disetujui karena di-import oleh Kepala Departemen
                            'tanggal_pengajuan' => Carbon::now()->format('Y-m-d'),
                        ]);
                    }
                }
            }
        }
    }
}
