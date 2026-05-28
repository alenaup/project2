<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipeKehadiran extends Model
{
    use HasFactory;

    protected $table = 'tipe_kehadiran';
    protected $primaryKey = 'id_tipe_kehadiran';

    protected $fillable = [
        'status_kehadiran',
        'keterangan',
        'bukti'

    ];

    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class, 'tipe_kehadiran_id', 'id_tipe_kehadiran');
    }
}
