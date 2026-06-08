<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwal';
    protected $primaryKey = 'id_jadwal';

    protected $fillable = [
        'status',
        'tanggal',
        'shift_id',
        'dibuat_oleh',
        'tanggal_mulai',
        'tanggal_akhir',
        'nama_periode',
        'toleransi_telat',
    ];

    public function kehadirans()
    {
        return $this->hasMany(Kehadiran::class, 'jadwal_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id', 'id_shift');
    }

    public function user()
    {
        return $this->belongsToMany(User::class, 'karyawan_jadwal', 'jadwal_id', 'user_id');
    }
}
