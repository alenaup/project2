<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Kepala Departemen - EcoGreen' }}</title>

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

<body x-data="{ ...dashboard(), open: false }" class="bg-gray-100">
    <div class="flex min-h-screen" x-data="{ open: false }">

        {{-- SIDEBAR --}}
        <x-sidebar :menus="[
            [
                'title' => 'Penjadwalan',
                'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M6.75 3v2.25M17.25 3v2.25M3.75 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h12a2.25 2.25 0 0 1 2.25 2.25v11.25m-16.5 0A2.25 2.25 0 0 0 6 21h12a2.25 2.25 0 0 0 2.25-2.25m-16.5 0h16.5\' /></svg>',
                'ref' => '/kepala-departement/dashboard'
            ],
            [
                'title' => 'Karyawan',
                'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v-.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Z\' /></svg>',
                'ref' => '/kepala-departement/karyawan'
            ],
            [
                'title' => 'Pengajuan',
                'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z\' /></svg>',
                'ref' => '/kepala-departement/pengajuan'
            ],
            [
                'title' => 'Laporan',
                'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M3 13.5h3m3 0h3m3 0h3M3 6.75h18M3 20.25h18\' /></svg>',
                'ref' => '/kepala-departement/laporan'
            ],
            [
                'title' => 'Shift',
                'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z\' /></svg>',
                'ref' => '/kepala-departement/shift'
            ],
            [
                'title' => 'Atur Lokasi Absensi',
                'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z\' /><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z\' /></svg>',
                'ref' => '/kepala-departement/atur-lokasi'
            ],
        ]">
            Kepala Departemen
        </x-sidebar>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">
            {{-- HEADER --}}
            <div class="sticky top-0 z-30 bg-gray-50/80 px-4 pt-4 md:px-6 md:pt-6 bg-gray-100 ">
                <x-header>Kepala Departemen</x-header>
            </div>

            {{-- PAGE CONTENT --}}
            <main class="flex-1 px-4 pb-6 md:px-6">
                @yield('content')
            </main>
        </div>

    </div>

    {{-- ✅ Loading Modal diletakkan di root body agar fixed inset-0 bisa menutupi seluruh halaman --}}
    <x-loading-modal target="logout" message="Sedang keluar dari sistem..." keepAlive="true" />

    {{-- ✅ Flash Message global — mendengarkan event dari JS maupun Livewire di seluruh halaman --}}
    <x-flash-message type="success" on="flash-success" sessionKey="success" />
    <x-flash-message type="error" on="flash-error" sessionKey="error" />

    {{-- js bawaan untuk livewire --}}
    @livewireScripts

    {{-- letak untuk script push --}}
    @stack('scripts')
</body>

</html>
