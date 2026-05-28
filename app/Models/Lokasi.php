<?php

namespace App\Models;

use App\Models\Departemen;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    protected $table = 'lokasi';
    protected $primaryKey = 'id_lokasi';

    protected $fillable = [
        'nama_lokasi',
        'latitude',
        'longitude',
        'radius',
        'status',
    ];

    public function departemen()
    {
        return $this->hasOne(Departemen::class, 'lokasi_id', 'id_lokasi');
    }
}