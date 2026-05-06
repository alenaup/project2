<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    /* pada models ini tidak mengunakan factory */
    protected $table = 'shift';

    protected $primaryKey = 'id_shift';

    protected $fillable = [
        'jam_masuk',
        'jam_keluar',
        'tipe_shift',
    ];

    protected $casts = [
        'jam_masuk' => 'datetime:H:i:s',
        'jam_keluar' => 'datetime:H:i:s',
    ];

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'shift_id');
    }
}
