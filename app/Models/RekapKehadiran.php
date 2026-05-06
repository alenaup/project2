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
        'total_lembur',
        'total_jam_kerja',
        'total_terlambat',
        'tanggal_validasi',
        'status_validasi',
        'status',
        'pemvalidasi_id',
        'tanggal',
    ];

    protected $casts = [
        'tanggal_validasi' => 'date',
        'tanggal' => 'date',
        'status' => Status::class,

    ];


    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class, 'rekapan_kehadiran_id');
    }
}
