<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerizinanSakit extends Model
{
    use HasFactory;

    protected $table = 'perizinan_sakit';
    protected $primaryKey = 'id_perizinan';

    protected $fillable = [
        'karyawan_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'file_surat',
        'status',
        'tanggal_pengajuan'
    ];

    public function karyawan()
    {
        return $this->belongsTo(User::class, 'karyawan_id', 'id_user');
    }
}
