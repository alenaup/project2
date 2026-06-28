<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Login ecoGreen E-outsourcing' }}</title>

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body class="min-h-screen h-64 overflow-y-scroll [&::-webkit-scrollbar]:w-3 [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-emerald-700 [&::-webkit-scrollbar-thumb]:rounded-md">
    <div class="flex" x-data="{ open: false }">
        <x-sidebar :menus="[
            [
                'title' => 'Dashboard',
                'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke=\'currentColor\' class=\'w-5 h-5\'>
                    <path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M3 12l9-9 9 9M5 10v10a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V10\' />
                </svg>',
                'ref' => '/admin-outsourcing/dashboard',
            ],
        
            [
                'title' => 'Perizinan Karyawan',
                'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke=\'currentColor\' class=\'w-5 h-5\'>
                    <path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M17 20h5v-2a4 4 0 0 0-4-4h-1M9 20H4v-2a4 4 0 0 1 4-4h1m8-4a4 4 0 1 0-8 0 4 4 0 0 0 8 0zm6 2a3 3 0 1 0-6 0\' />
                </svg>',
                'ref' => '/admin-outsourcing/pengajuan-karyawan',
            ],
        
            [
                'title' => 'Kelola Karyawan',
                'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke=\'currentColor\' class=\'w-5 h-5\'>
                    <path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM4 21a8 8 0 0 1 16 0M19.4 15a2 2 0 0 1 0 3.6l-1 .4-.3 1.1-1.5.9-1-.6-1 .6-1.5-.9-.3-1.1-1-.4a2 2 0 0 1 0-3.6l1-.4.3-1.1 1.5-.9 1 .6 1-.6 1.5.9.3 1.1 1 .4Z\' />
                </svg>',
                'ref' => '/admin-outsourcing/kelola-karyawan',
            ],
        
            [
                'title' => 'Pengajuan Akun',
                'icon' => '<svg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke-width=\'2\' stroke=\'currentColor\' class=\'w-5 h-5\'>
                    <path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0zM3 19.235v-.111c0-1.748 1.154-3.238 2.825-3.66a9.98 9.98 0 0 1 6.35 0c1.671.422 2.825 1.912 2.825 3.66v.111M12 18.75V21m-4.5-2.25H16.5\' />
                </svg>',
                'ref' => '/admin-outsourcing/pengajuan-akun',
            ],
        ]">
            Admin Outsourcing
        </x-sidebar>

        {{-- mengisi untuk letak isi --}}

        <div x-show="open" @click="open = false" class="fixed inset-0 bg-black/40 backdrop-blur-sm md:hidden z-40">
        </div>

        <div class="flex-1 p-4 md:p-6 overflow-x-hidden">
            <!-- HEADER -->
            <x-header>Admin Outsourcing</x-header>

            <!-- CONTENT akan diletakkan disini -->
            @yield('content')
        </div><!-- /main -->
    </div>

    {{-- ✅ Loading Modal diletakkan di root body agar fixed inset-0 bisa menutupi seluruh halaman --}}
    <x-loading-modal target="logout" message="Sedang keluar dari sistem..." keepAlive="true" />
    

    {{-- js bawaan untuk livewire --}}
    @livewireScripts

    {{-- letak untuk script push --}}
    @stack('scripts')
</body>

</html>
