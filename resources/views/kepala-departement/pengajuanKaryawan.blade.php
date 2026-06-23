<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Pengajuan Lembur - Kepala Departemen</title>

    <link rel="icon" type="image/x-icon" href="/images/logo.png">
    @vite('resources/css/app.css')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @livewireStyles
</head>

<body x-data="{ open: false }" class="bg-gray-100">

    <div class="flex">
        {{-- SIDEBAR --}}
        <x-sidebar :menus="[
            ['title' => 'Penjadwalan', 'icon' => 'fa-solid fa-calendar', 'ref' => '/kepala-departement/dashboard'],
            ['title' => 'Karyawan', 'icon' => 'fas fa-users', 'ref' => '/kepala-departement/karyawan'],
            ['title' => 'Pengajuan', 'icon' => 'fa-solid fa-file-arrow-up', 'ref' => '/kepala-departement/pengajuan'],
            ['title' => 'Laporan', 'icon' => 'fa-solid fa-file-lines', 'ref' => '/kepala-departement/laporan'],
            ['title' => 'Shift', 'icon' => 'fa-solid fa-user-clock', 'ref' => '/kepala-departement/shift'],
        ]">kepala-departement</x-sidebar>

        <div class="flex-1 p-6 ml-0">
            <x-header>Kepala Departemen</x-header>

            <div class="max-w-6xl mx-auto p-6 bg-white/70 rounded-2xl shadow">
                @livewire('kepala-departement.persetujuan-pengajuan')
            </div>
        </div>

    </div>

    @livewireScripts
</body>

</html>

