<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outsourcing extends Model
{
    /* melakukan faktory untuk membuat dummy data */
    use HasFactory;

    /* menentukan nama table dan melakukan inisiasi primary key */
    protected $table = 'outsourcing';
    protected $primaryKey = 'id_outsourcing';

    /* menentukan field yang dapat diisi */
    protected $fillable = [
        'nama_outsourcing',
        'status',
        'nomor_tlp',
        'email',
        'alamat',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'outsourcing_id', 'id_outsourcing');
    }
}
