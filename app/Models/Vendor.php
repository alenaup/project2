<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    /* melakukan faktory untuk membuat dummy data */
    use HasFactory;

    /* menentukan nama table dan melakukan inisiasi primary key */
    protected $table = 'vendor';
    protected $primaryKey = 'id_vendor';

    /* menentukan field yang dapat diisi */
    protected $fillable = [
        'nama_vendor',
        'status',
        'nomor_tlp',
        'email',
        'alamat',
    ];

    /* menentukan tipe data field */
    protected $casts = [
        'status' => 'string'
    ];

    /* melakukan relasi ke model User dengan role admin_vendor */
    /* jenis relasi 1 vendor memiliki banyak user admin_vendor */
    public function users()
    {
        return $this->hasMany(User::class, 'vendor_id');
    }
}
