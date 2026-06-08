<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login ecoGreen E-outsourcing</title>
    <link rel="preload" as="image" href="/images/bg (2).webp">
    <link rel="preload" as="image" href="/images/logo (2).webp">
    <link rel="icon" type="image/x-icon" href="/images/logo (2).webp">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link rel="stylesheet" href={{ asset('css/login.css') }}>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>

<body class="min-h-screen">
    <main>
        <div class="min-h-screen bg-cover bg-center flex items-center justify-center md:justify-start w-full"
            style="background-image: url('/images/bg.webp');">

            <!-- OVERLAY ANIMASI -->
            <div id="animationOverlay">
                <div class="anim-logo-wrapper">
                    <img src="/images/logo (2).webp" class="anim-logo" alt="Logo">
                </div>
                <div class="anim-text lg:text-6xl">Eco Green</div>
            </div>

            <!-- DESKTOP LAYOUT -->
            <div class="flex w-full h-screen bg-black/60">

                <!-- LEFT -->
                <div class="hidden md:flex w-7/12 text-white items-center justify-center p-12">
                    <div class="max-w-md text-center">
                        <img src="/images/logo (2).webp"
                            class="w-32 mx-auto mb-6 hidden md:block ftransition duration-300 transform hover:scale-110 hover:-translate-y-1 hover:shadow-xl"
                            alt="Logo">

                        <h1 class="text-3xl font-bold mb-4">
                            Eco Green
                        </h1>

                        <p class="text-sm opacity-90 leading-relaxed">
                            Sistem manajemen outsourcing yang membantu mengelola data karyawan,
                            absensi, jadwal, dan aktivitas secara terpusat dan efisien.
                        </p>
                    </div>
                </div>

                <!-- RIGHT -->
                <div
                    class="w-full p-4 mx-[8%] my-[20%] md:w-5/12 md:my-0 md:mx-0 flex rounded-2xl md:rounded-0 items-center justify-center bg-white/10 backdrop-blur-md">
                    <div class="w-full max-w-md md:p-10 p-4">
                        <img src="/images/logo (2).webp"
                            class="block md:hidden w-16 mx-auto mb-6 transition duration-300 transform animate-[spin_16s_linear_infinite]"
                            alt="Logo">

                        <h1
                            class="flex items-center justify-center w-full md:hidden text-center text-emerald-700 text-3xl font-bold mb-4">
                            Eco Green
                        </h1>
                        <h2 class=" hidden md:block text-5xl font-bold md:p-16 text-center text-brand">
                            Masuk
                        </h2>

                        {{-- bagian dari form login dengan livewire --}}
                        {{-- bagian ini akan diisi oleh livewire --}}
                        <livewire:auth.login />
                    </div>
                </div>

            </div>
        </div>
    </main>
    @livewireScripts
    {{-- letak untuk script push --}}
    <script src="{{ asset('js/login.js') }}"></script>
</body>

</html>
