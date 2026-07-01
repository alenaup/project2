<!DOCTYPE html>
<html lang="id" class="is-loading">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'HR - EcoGreen' }}</title>

    {{-- icon untuk logo Perusahaan --}}
    <link rel="preload" as="image" href="/images/logo (2).webp">
    <link rel="icon" type="image/x-icon" href="/images/logo (2).webp">

    {{-- untuk mengambil data dari css js vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- kode CDN untuk font awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{-- bawaan style livewire --}}
    @livewireStyles
    {{-- letak untuk style push --}}
    @stack('styles')
</head>

<body x-data="{ open: false }" class="min-h-screen bg-gray-50">
    <div class="flex min-h-screen" x-data="{ open: false }">

        {{-- SIDEBAR --}}
        <x-sidebar :menus="[
            [
                'title' => 'Dashboard',
                'icon' =>
                    '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M3 12l2-2m0 0 7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11-7-7m7 7v10a1 1 0 0 1-1 1h-3m-6 0h6\' /></svg>',
                'ref' => '/hr/dashboard',
            ],
        
            [
                'title' => 'Rekapan Detail',
                'icon' =>
                    '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25A2.25 2.25 0 0 1 5.25 3h13.5A2.25 2.25 0 0 1 21 5.25Z\' /></svg>',
                'ref' => '/hr/rekapan-detail',
            ],
        
            [
                'title' => 'Ajuan Data Karyawan',
                'icon' =>
                    '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M15 19.128a9.38 9.38 0 0 0 2.625.372A9.337 9.337 0 0 0 21 18.24c0-1.883-.707-3.64-1.95-4.96M15 19.128v-3.043m0 3.043a9.384 9.384 0 0 1-3 .492c-1.052 0-2.062-.173-3-.492m6 0-3-3m-6.75-8.25a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM3 20.25a7.5 7.5 0 0 1 15 0\' /></svg>',
                'ref' => '/hr/ajuan-data-karyawan',
            ],
        
            [
                'title' => 'Karyawan',
                'icon' =>
                    '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z\' /></svg>',
                'ref' => '/hr/data-karyawan',
            ],
        ]">
            HR
        </x-sidebar>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">
            {{-- HEADER --}}
            <div class="sticky top-0 z-30 bg-gray-50/80 backdrop-blur-sm px-4 pt-4 md:px-6 md:pt-6">
                <x-header>HR Outsourcing</x-header>
            </div>

            {{-- PAGE CONTENT --}}
            <main class="flex-1 px-4 pb-6 md:px-6">
                @yield('content')
            </main>
        </div>

    </div>

    {{-- ✅ Loading Modal diletakkan di root body agar fixed inset-0 bisa menutupi seluruh halaman --}}
    <x-loading-modal target="logout" message="Sedang keluar dari sistem..." keepAlive="true" />

    {{-- js bawaan untuk livewire --}}
    @livewireScripts

    {{-- letak untuk script push --}}
    @stack('scripts')
</body>

</html>
