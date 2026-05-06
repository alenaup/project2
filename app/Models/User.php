<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Status;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

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
 *
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 */

/* Class user yang berfungsi untuk merepresentasikan pengguna */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    /* nama table yang digunakan */
    protected $table = 'users';

    /* primary key */
    protected $primaryKey = 'id_user';

    /* auto increment */
    public $incrementing = true;

    /* tipe primary key */
    protected $keyType = 'int';

    /* data yang diperbolehkan untuk diambil dari database */
    protected $fillable = [
        'nama_lengkap',
        'email',
        'password',
        'nomor_tlp',
        'alamat',
        'NIP',
        'nama_departemen',
        'tanggal_keluar',
        'tanggal_masuk',
        'role',
        'status',
        'created_by',
    ];

    /* mengisi nilai default untuk atribut */
    protected $attributes = [
        'status' => Status::Active->value,
        'role' => UserRole::Karyawan->value,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */

    /* data yang tidak diperbolehkan untuk serialisasi */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /* ================================================================= */

    /* melakukan pengecekan role */
    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function isAdminVendor(): bool
    {
        return $this->role === UserRole::AdminVendor;
    }

    public function isKaryawan(): bool
    {
        return $this->role === UserRole::Karyawan;
    }

    public function isHr(): bool
    {
        return $this->role === UserRole::Hr;
    }

    public function isKepalaDepartemen(): bool
    {
        return $this->role === UserRole::KepalaDepartemen;
    }

    /* ================================================================ */

    /* mengambil data berdasarkan role */
    public function roleData(): ?Model
    {
        return match ($this->role) {
            UserRole::Karyawan => $this->karyawan,
            default => null,
        };
    }


    /* =============================================================== */
    /* relasi one to many dengan model vendor */
    /* relasi hanya untuk user dengan role admin_vendor */
    /* bagian ini hanya memberikan sebuah jalur jadi tidak memerlukan pengecekan role */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id_vendor');
    }

    /* relasi ke tabel relasi dengan nama tabel KaryawanJadwal */
    public function jadwal()
    {
        return $this->belongsToMany(Jadwal::class, 'karyawan_jadwal', 'user_id', 'jadwal_id');
    }

    /* relasi ke tabel lembur dengan kardinalittas satu ke banyak */

    public function lembur()
    {
        return $this->hasMany(Lembur::class, 'user_id');
    }
}
