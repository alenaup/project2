<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- title untuk judul halaman --}}
    <title>Admin Outsourcing</title>

    {{-- link favicon untuk logo --}}
    <link rel="icon" type="image/x-icon" href="/images/logo.png">

    {{-- mengambil css dan js styling tailwind --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- kode CDN untuk alpine js (Dihapus karena Livewire 3 sudah membawa Alpine bawaan) --}}

    {{-- kode CDN untuk font awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- kode CDN untuk chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js"></script>

    {{-- kode CDN untuk flatpickr --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    {{-- kode CDN untuk chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Livewire styles — wajib ada agar komponen Livewire dapat bekerja --}}
    @livewireStyles

</head>

<body x-data="{ open: false }" class="bg-slate-100 font-sans text-slate-800 antialiased">
    <div class="flex">
        {{ $slot }}

        
    </div><!-- /app -->

    {{-- Livewire scripts — wajib ada di akhir body agar reaktivitas berjalan --}}
    @livewireScripts

</body>

</html>
