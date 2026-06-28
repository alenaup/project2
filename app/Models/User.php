<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\Departemen;
use App\Models\Kehadiran;
use App\Models\Lembur;
use App\Models\Outsourcing;
use App\Models\RekapKehadiran;
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
    protected $table = 'user';

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
        'nip',
        'nama_departemen',
        'departemen_id',
        'tanggal_keluar',
        'tanggal_masuk',
        'role',
        'status',
        'user_id',
        'outsourcing_id',

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
    public function departemen() 
    {
        return $this->belongsTo(Departemen::class,'departemen_id', 'id_departemen');
    }

    public function lembur()
    {
        return $this->hasMany(Lembur::class, 'karyawan_id', 'id_user');
    }

    public function outsourcing() 
    {
        return $this->belongsTo(Outsourcing::class, 'outsourcing_id', 'id_outsourcing');
    }

    public function rekap() 
    {
        return $this->hasMany(RekapKehadiran::class, 'user_id', 'id_user');
    }

    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class, 'user_id', 'id_user');
    }

    public function jadwal()
    {
        return $this->belongsToMany(Jadwal::class, 'karyawan_jadwal', 'user_id', 'jadwal_id');
    }

    public function perizinanSakit()
    {
        return $this->hasMany(\App\Models\PerizinanSakit::class, 'karyawan_id', 'id_user');
    }
}