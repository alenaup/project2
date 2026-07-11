<?php 

namespace App\Services;

use App\Models\Outsourcing;

class OutsourcingService
{
    public function ambilSemuaOutsourcing()
    {
        return Outsourcing::all();
    }

    public function getOutsourcingPaginated(int $perPage = 5)
    {
        return Outsourcing::select('id_outsourcing', 'nama_outsourcing')->paginate($perPage);
    }

    public function getOutsourcingById(int $id): ?Outsourcing
    {
        return Outsourcing::find($id);
    }
}