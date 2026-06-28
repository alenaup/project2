<?php

namespace App\Models;

use App\Models\Lokasi;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    protected $table = 'departemen';
    protected $primaryKey = 'id_departemen';

    protected $fillable =  [
        'nama_departemen',
        'status',
        'lokasi_id',
    ];

    public function lokasi() 
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id', 'id_lokasi');
    }
    
    public function user()
    {
        return $this->hasMany(User::class, 'departemen_id', 'id_departemen');
    }
}
