
<!DOCTYPE html>
<html lang="en" class="animate-item">

<head >
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login ecoGreen E-outsourcing' }}</title>

    {{-- icon untuk logo Perusahaan --}}
    <link rel="icon" type="image/x-icon" href="/images/logo.png">
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

<body class="min-h-screen">
    <div class="flex">
        <x-sidebar :menus="[
        ['title' => 'Absensi', 'icon' => 'fas fa-calendar-check', 'ref' => '/karyawan-outsourcing/dashboard'],
        ['title' => 'Jadwalku', 'icon' => 'fas fa-calendar-days', 'ref' => '/karyawan-outsourcing/jadwal-karyawan'],
        ['title' => 'Pengajuan Lembur', 'icon' => 'fas fa-business-time', 'ref' => '/karyawan-outsourcing/pengajuanKaryawan',],
        ['title' => 'Perizinan Sakit', 'icon' => 'fas fa-briefcase-medical', 'ref' => '/karyawan-outsourcing/perizinan-karyawan'],
    ]">Karyawan Outsourcing</x-sidebar>

        {{-- mengisi untuk letak isi --}}

        <div x-show="open" @click="open = false" class="fixed inset-0 bg-black/40 backdrop-blur-sm md:hidden z-40">
        </div>

        <div class="flex-1 p-4 md:p-6 overflow-x-hidden">
            <!-- HEADER -->
            <x-header>Karyawan Outsourcing</x-header>

            <!-- CONTENT akan diletakkan disini -->
            
            
        </div><!-- /main -->
    </div>

    {{-- js bawaan untuk livewire --}}
    @livewireScripts

    {{-- letak untuk script push --}}
    @stack('scripts')
</body>

</html>
