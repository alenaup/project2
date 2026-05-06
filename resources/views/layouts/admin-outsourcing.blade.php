<x-Head-html>


    <x-sidebar :menus="[
        ['title' => 'Dashboard', 'icon' => 'fas fa-home', 'ref' => '/admin-outsourcing/dashboard'],
        ['title' => 'Perizinan Karyawan', 'icon' => 'fas fa-users', 'ref' => '/admin-outsourcing/pengajuan-karyawan'],
        ['title' => 'Kelola Karyawan', 'icon' => 'fas fa-user-cog', 'ref' => '/admin-outsourcing/kelola-karyawan'],
    ]">Admin Outsourcing</x-sidebar>

    <div x-show="open" @click="open = false" class="fixed inset-0 bg-black/40 backdrop-blur-sm md:hidden z-40"></div>

        <div class="flex-1 p-4 md:p-6 overflow-x-hidden">
            <!-- HEADER -->
            <x-header>Admin Outsourcing</x-header>
            <x-alert></x-alert>
            <!-- CONTENT -->



        </div><!-- /main -->

</x-Head-html>
