<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id_departemen
 * @property string $nama_departemen
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $lokasi_id
 * @property-read \App\Models\Lokasi|null $lokasi
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $user
 * @property-read int|null $user_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemen newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemen newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemen query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemen whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemen whereIdDepartemen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemen whereLokasiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemen whereNamaDepartemen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemen whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemen whereUpdatedAt($value)
 */
	class Departemen extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_jadwal
 * @property string $status
 * @property string|null $toleransi_telat
 * @property string $tanggal_mulai
 * @property string $tanggal_akhir
 * @property string $nama_periode
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shift_id
 * @property int $dibuat_oleh
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Kehadiran> $kehadirans
 * @property-read int|null $kehadirans_count
 * @property-read \App\Models\Shift $shift
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $user
 * @property-read int|null $user_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jadwal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jadwal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jadwal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jadwal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jadwal whereDibuatOleh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jadwal whereIdJadwal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jadwal whereNamaPeriode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jadwal whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jadwal whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jadwal whereTanggalAkhir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jadwal whereTanggalMulai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jadwal whereToleransiTelat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jadwal whereUpdatedAt($value)
 */
	class Jadwal extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_kehadiran
 * @property string|null $waktu_masuk
 * @property string|null $waktu_keluar
 * @property string $tanggal
 * @property string $lokasi_masuk
 * @property string|null $lokasi_keluar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $jadwal_id
 * @property int $tipe_kehadiran_id
 * @property int $rekapan_kehadiran_id
 * @property int $karyawan_id
 * @property-read \App\Models\Jadwal $jadwal
 * @property-read \App\Models\RekapKehadiran $rekapanKehadiran
 * @property-read \App\Models\TipeKehadiran $tipeKehadiran
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kehadiran newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kehadiran newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kehadiran query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kehadiran whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kehadiran whereIdKehadiran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kehadiran whereJadwalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kehadiran whereKaryawanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kehadiran whereLokasiKeluar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kehadiran whereLokasiMasuk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kehadiran whereRekapanKehadiranId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kehadiran whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kehadiran whereTipeKehadiranId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kehadiran whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kehadiran whereWaktuKeluar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kehadiran whereWaktuMasuk($value)
 */
	class Kehadiran extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_lembur
 * @property string|null $mulai_lembur
 * @property string|null $selesai_lembur
 * @property string|null $tanggal_divalidasi
 * @property string $status
 * @property string|null $status_validasi
 * @property string $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $karyawan_id
 * @property int|null $pemvalidasi_id
 * @property-read \App\Models\User $karyawan
 * @property-read \App\Models\User|null $pemvalidasi
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereIdLembur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereKaryawanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereMulaiLembur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur wherePemvalidasiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereSelesaiLembur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereStatusValidasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereTanggalDivalidasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereUpdatedAt($value)
 */
	class Lembur extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_lokasi
 * @property numeric $longitude
 * @property numeric $latitude
 * @property string $nama_lokasi
 * @property int $radius
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Departemen|null $departemen
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi whereIdLokasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi whereNamaLokasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi whereRadius($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lokasi whereUpdatedAt($value)
 */
	class Lokasi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_outsourcing
 * @property string $nama_outsourcing
 * @property string $status
 * @property string $nomor_tlp
 * @property string $email
 * @property string $alamat
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\OutsourcingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outsourcing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outsourcing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outsourcing query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outsourcing whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outsourcing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outsourcing whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outsourcing whereIdOutsourcing($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outsourcing whereNamaOutsourcing($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outsourcing whereNomorTlp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outsourcing whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Outsourcing whereUpdatedAt($value)
 */
	class Outsourcing extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_rekapan
 * @property int $total_mankir
 * @property int $total_cuti
 * @property int $total_lembur
 * @property int $total_izin
 * @property int $total_sakit
 * @property int $total_hadir
 * @property int $total_terlambat
 * @property int $total_jam_kerja
 * @property \Illuminate\Support\Carbon|null $tanggal_validasi
 * @property string|null $status_validasi
 * @property \App\Enums\Status $status
 * @property int $pengaju
 * @property int|null $pevalidasi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Kehadiran> $kehadiran
 * @property-read int|null $kehadiran_count
 * @property-read \App\Models\User $pengajuUser
 * @property-read \App\Models\User|null $pevalidasiUser
 * @method static \Database\Factories\RekapKehadiranFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RekapKehadiran newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RekapKehadiran newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RekapKehadiran query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RekapKehadiran whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RekapKehadiran whereIdRekapan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RekapKehadiran wherePengaju($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RekapKehadiran wherePevalidasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RekapKehadiran whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RekapKehadiran whereStatusValidasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RekapKehadiran whereTanggalValidasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RekapKehadiran whereTotalCuti($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RekapKehadiran whereTotalHadir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RekapKehadiran whereTotalIzin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RekapKehadiran whereTotalJamKerja($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RekapKehadiran whereTotalLembur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RekapKehadiran whereTotalMankir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RekapKehadiran whereTotalSakit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RekapKehadiran whereTotalTerlambat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RekapKehadiran whereUpdatedAt($value)
 */
	class RekapKehadiran extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_shift
 * @property string $jam_masuk
 * @property string $jam_keluar
 * @property string $nama_shift
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Jadwal> $jadwal
 * @property-read int|null $jadwal_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereIdShift($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereJamKeluar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereJamMasuk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereNamaShift($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereUpdatedAt($value)
 */
	class Shift extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_tipe_kehadiran
 * @property string $status_kehadiran
 * @property string|null $bukti
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Kehadiran> $kehadiran
 * @property-read int|null $kehadiran_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipeKehadiran newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipeKehadiran newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipeKehadiran query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipeKehadiran whereBukti($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipeKehadiran whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipeKehadiran whereIdTipeKehadiran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipeKehadiran whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipeKehadiran whereStatusKehadiran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipeKehadiran whereUpdatedAt($value)
 */
	class TipeKehadiran extends \Eloquent {}
}

namespace App\Models{
/**
 * Class User
 *
 * Model untuk merepresentasikan pengguna dalam sistem outsourcing.
 *
 * Digunakan untuk:
 * - Autentikasi login
 * - Manajemen role (super_admin, hr, karyawan, admin_vendor, kepala_departemen)
 * - Relasi ke model karyawan
 *
 * Catatan:
 * - Menggunakan primary key custom: id_user
 * - Default role: karyawan
 * - Default status: active
 *
 * @property int $id_user
 * @property string $nama_lengkap
 * @property string $email
 * @property string|null $nomor_tlp
 * @property UserRole $role
 * @property string $password
 * @property string|null $alamat
 * @property string|null $NIP
 * @property string|null $nama_departemen
 * @property string|null $tanggal_keluar
 * @property string|null $tanggal_masuk
 * @property string $status
 * @property Carbon|null $email_verified_at
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @property string $nip
 * @property int|null $outsourcing_id
 * @property int|null $departemen_id
 * @property int|null $user_id
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Departemen|null $departemen
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Jadwal> $jadwal
 * @property-read int|null $jadwal_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Kehadiran> $kehadiran
 * @property-read int|null $kehadiran_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Lembur> $lembur
 * @property-read int|null $lembur_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Outsourcing|null $outsourcing
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RekapKehadiran> $rekap
 * @property-read int|null $rekap_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDepartemenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIdUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNamaLengkap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNip($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNomorTlp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOutsourcingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTanggalKeluar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTanggalMasuk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUserId($value)
 */
	class User extends \Eloquent {}
}

