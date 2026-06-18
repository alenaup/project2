<?php

namespace App\Services;
use App\Models\RekapKehadiran;

class RekapService
{
    public function ambilRekapDetail()
    {
        return RekapKehadiran::where('status', 'active')->first()
               ?? RekapKehadiran::first();
    }
}