<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapKehadiran extends Model
{
    use HasFactory;

    /* menginisiasikan tabel dan primary key */
    protected $table = 'rekap_kehadiran';
    protected $primaryKey = 'id_rekapan';

    protected $fillable = [
        'pengaju',
        'pevalidasi',
        'status_validasi',
        'status',
        'tanggal_validasi',
        'total_jam_kerja',
        'total_terlambat',
        'total_hadir',
        'total_sakit',
        'total_izin',
        'total_lembur',
        'total_cuti',
        'total_mankir',
    ];

    protected $casts = [
        'tanggal_validasi' => 'date',
        'status' => Status::class,
    ];

    public function pengajuUser()
    {
        return $this->belongsTo(User::class, 'pengaju', 'id_user');
    }

    public function pevalidasiUser()
    {
        return $this->belongsTo(User::class, 'pevalidasi', 'id_user');
    }

    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class, 'rekapan_kehadiran_id', 'id_rekapan');
    }

}
