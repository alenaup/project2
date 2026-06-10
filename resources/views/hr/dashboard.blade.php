<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard HR</title>

    <link rel="icon" type="image/x-icon" href="/images/logo.png">
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body x-data="{ open: false }" class="bg-gray-100">

    <div class="flex">
        <x-sidebar :menus="[
            ['title' => 'Dashboard', 'icon' => 'fas fa-book', 'ref' => '/hr/dashboard'],
            ['title' => 'Rekapan Detail', 'icon' => 'fas fa-user-group', 'ref' => '/hr/rekapan-detail'],
            ['title' => 'Ajuan Data Karyawan', 'icon' => 'fas fa-address-book', 'ref' => '/hr/ajuan-data-karyawan'],
            ['title' => 'Karyawan', 'icon' => 'fas fa-user-tie', 'ref' => '/hr/data-karyawan'],
        ]">HR</x-sidebar>

        <div class="flex-1 p-4 md:p-6 ml-0 min-w-0 overflow-hidden">
            <x-header>HR</x-header>

            {{-- // BUAT ISI CONTENT DIBAWAH SINIIIIIII --}}
            @livewire(\App\Livewire\HR\DashboardHR::class)
            {{-- SELESAI CONTENT --}}
        </div>
    </div>

    @livewireScripts
    
</body>
</html>