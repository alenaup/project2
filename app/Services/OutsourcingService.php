<?php 

namespace App\Services;

use App\Models\Outsourcing;

class OutsourcingService
{
    public function ambilSemuaOutsourcing()
    {
        return Outsourcing::all();
    }
}