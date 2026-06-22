
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Karyawan Outsourcing - EcoGreen' }}</title>

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

<body class="min-h-screen bg-gray-50">
    <div class="flex min-h-screen" x-data="{ open: false }">

        {{-- SIDEBAR --}}
        <x-sidebar :menus="[
            [
                'title' => 'Absensi',
                'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M6.75 3v2.25M17.25 3v2.25M3.75 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h12a2.25 2.25 0 0 1 2.25 2.25v11.25m-16.5 0A2.25 2.25 0 0 0 6 21h12a2.25 2.25 0 0 0 2.25-2.25m-16.5 0h16.5\' /></svg>',
                'ref' => '/karyawan-outsourcing/dashboard'
            ],
            [
                'title' => 'Jadwalku',
                'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M6.75 3v2.25M17.25 3v2.25M3 9h18M3.75 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h12A2.25 2.25 0 0 1 20.25 7.5v11.25A2.25 2.25 0 0 1 18 21H6a2.25 2.25 0 0 1-2.25-2.25Z\' /></svg>',
                'ref' => '/karyawan-outsourcing/jadwal-karyawan'
            ],
            [
                'title' => 'Pengajuan Lembur',
                'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M12 6v6h4.5M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z\' /></svg>',
                'ref' => '/karyawan-outsourcing/pengajuanKaryawan'
            ],
            [
                'title' => 'Perizinan Sakit',
                'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z\' /></svg>',
                'ref' => '/karyawan-outsourcing/perizinan-karyawan'
            ],
            [
                'title' => 'Data Diri',
                'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a8.25 8.25 0 0 1 15 0\' /></svg>',
                'ref' => '/karyawan-outsourcing/data-diri'
            ],
        ]">
            Karyawan Outsourcing
        </x-sidebar>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">
            {{-- HEADER --}}
            <div class="sticky top-0 z-30 bg-gray-50/80 backdrop-blur-sm px-4 pt-4 md:px-6 md:pt-6">
                <x-header>Karyawan Outsourcing</x-header>
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