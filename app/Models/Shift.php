<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $table = 'shift';
    protected $primaryKey = 'id_shift';

    protected $fillable = [
        'jam_masuk',
        'jam_keluar',
        'nama_shift',
    ];


    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'shift_id', 'id_shift');
    }
}
