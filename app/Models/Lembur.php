<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    use HasFactory;

    protected $table = 'lembur';
    protected $primaryKey = 'id_lembur';

    protected $fillable = [
        'mulai_lembur',
        'selesai_lembur',
        'tanggal_dibuat',
        'status',
        'status_validasi',
        'keterangan',
        'karyawan_id',
        'pemvalidasi_id',
    ];

    public function karyawan()
    {
        return $this->belongsTo(User::class, 'karyawan_id', 'id_user');
    }

    public function pemvalidasi()
    {
        return $this->belongsTo(User::class, 'pemvalidasi_id', 'id_user');
    }
}
