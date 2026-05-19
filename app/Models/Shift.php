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
}
