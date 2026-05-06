<x-Head-html>

        <x-sidebar :menus="[
            ['title' => 'Penjadwalan', 'icon' => 'fas fa-home', 'ref' => '/kepala-departemen/dashboard'],
            ['title' => 'Karyawan', 'icon' => 'fas fa-users', 'ref' => '/kepala-departemen/karyawan'],
            ['title' => 'Pengajuan', 'icon' => 'fas fa-users', 'ref' => '/kepala-departemen/pengajuan'],
            ['title' => 'Laporan', 'icon' => 'fas fa-cog', 'ref' => '/kepala-departemen/laporan'],
            ['title' => 'Shift', 'icon' => 'fas fa-cog', 'ref' => '/kepala-departemen/shift'],
        ]">kepala-departemen</x-sidebar>

        <div x-show="open" @click="open = false" class="fixed inset-0 bg-black/40 backdrop-blur-sm md:hidden z-40"></div>

        <div class="flex-1 p-4 md:p-6 overflow-x-hidden">
            <!-- HEADER -->
            <x-header>Kepala Departemen</x-header>
            <x-alert></x-alert>
            <!-- CONTENT -->



        </div><!-- /main -->

</x-Head-html>
