<?php

namespace App\View\Components\hr;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class filterDataKaryawan extends Component
{
    public function __construct()
    {
    }

    public function render(): View|Closure|string
    {
        return view('components.hr.filter-data-karyawan');
    }
}
