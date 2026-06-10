

<x-Head-html>


    <x-sidebar :menus="[
        ['title' => 'Absensi', 'icon' => 'fas fa-calendar-check', 'ref' => '/karyawan-outsourcing/dashboard'],
        ['title' => 'Jadwalku', 'icon' => 'fas fa-calendar-days', 'ref' => '/karyawan-outsourcing/jadwal-karyawan'],
        ['title' => 'Pengajuan Lembur', 'icon' => 'fas fa-business-time', 'ref' => '/karyawan-outsourcing/pengajuanKaryawan',],
        ['title' => 'Perizinan Sakit', 'icon' => 'fas fa-briefcase-medical', 'ref' => '/karyawan-outsourcing/perizinan-karyawan'],
    ]">Karyawan Outsourcing</x-sidebar>

    <div x-show="open" @click="open = false" class="fixed inset-0 bg-black/40 backdrop-blur-sm md:hidden z-40"></div>

        <div class="flex-1 p-4 md:p-6 overflow-x-hidden">
            <!-- HEADER -->
            <x-header>Karyawan Outsourcing</x-header>
            
            <!-- CONTENT -->
            @yield('content')

        </div><!-- /main -->

</x-Head-html>
