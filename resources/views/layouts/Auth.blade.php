<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login ecoGreen E-outsourcing' }}</title>

    {{-- icon untuk logo Perusahaan --}}
    <link rel="icon" type="image/x-icon" href="/images/logo.png">
    {{-- untuk mengambil data dari css js vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- bawaan style livewire --}}
    @livewireStyles

    {{-- letak untuk style push --}}
    @stack('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body class="min-h-screen">
    {{-- mengisi untuk letak isi --}}
    @yield('content')

    {{-- js bawaan untuk livewire --}}
    @livewireScripts
    {{-- letak untuk script push --}}
    @stack('scripts')
</body>

</html>
