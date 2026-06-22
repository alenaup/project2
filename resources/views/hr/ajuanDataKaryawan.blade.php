<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Data Karyawan</title>

    <link rel="icon" type="image/x-icon" href="/images/logo.png">
    @vite('resources/css/app.css')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @livewireStyles
</head>

<body x-data="{ open: false }" class="bg-gray-100">

    <div class="flex">
        {{-- SIDEBAR --}}
        <x-sidebar :menus="[
            ['title' => 'Dashboard', 'icon' => 'fas fa-book', 'ref' => '/hr/dashboard'],
            ['title' => 'Rekapan Detail', 'icon' => 'fas fa-user-group','ref' => '/hr/rekapan-detail'],
            ['title' => 'Pengajuan Data Karyawan', 'icon' => 'fas fa-address-book','ref' => '/hr/ajuan-data-karyawan'],
            ['title' => 'Karyawan', 'icon' => 'fas fa-user-tie','ref' => '/hr/data-karyawan'],
        ]" >HR</x-sidebar>
        <div class="flex-1 p-4 md:p-6 ml-0 min-w-0 overflow-hidden">

            <!-- HEADER CONTENT -->
            <x-header>HR</x-header>
            {{-- // BUAT ISI CONTENT DIBAWAH SINIIIIIII --}}

            <div class="bg-white p-4 md:p-8 rounded-lg shadow-lg mt-6">
                <div class="flex flex-col md:flex-row md:justify-between gap-3">
                    <div class="flex items-center gap-3 mb-6">
                        <i class="fa-solid fa-file-signature text-xl md:text-2xl text-gray-900"></i>
                        <h2 class="text-lg md:text-xl font-bold text-gray-900">Pengajuan Data Karyawan Outsourcing</h2>
                    </div>

                </div>

                <div class="w-full overflow-x-auto pb-4">
                    @livewire(\App\Livewire\HR\AjuanDataKaryawan::class)
                </div>
            </div>


            {{-- SELESAI CONTENT --}}
        </div>

    </div>

    @livewireScripts
</body>

</html>
