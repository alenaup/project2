<!DOCTYPE html>
<html lang="en" class="is-loading">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')

    {{-- icon untuk logo Perusahaan --}}
    <link rel="preload" as="image" href="/images/logo (2).webp">
    <link rel="icon" type="image/x-icon" href="/images/logo (2).webp">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/lucide@latest"></script>

    <title>Kelola Departemen - Super Admin</title>
    @livewireStyles
</head>

<body class="bg-gray-100" style="font-family: 'Poppins', sans-serif;">

    <!-- TOPBAR -->
    <div
        class="bg-linear-to-r from-green-900 to-green-700 px-4 md:px-10 h-16 flex items-center justify-between text-white shadow">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center overflow-hidden">
                <img src="/images/logo (2).webp" alt="Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <h1 class="text-sm font-extrabold hidden md:block">Ecogreen e-Outsourcing</h1>
                <h1 class="text-sm font-extrabold md:hidden">Ecogreen</h1>
                <h1 class="text-sm font-extrabold md:hidden">e-Outsourcing</h1>
                <p class="text-[11px] text-green-200 md:block hidden">Sistem Manajemen Karyawan Outsourcing</p>
            </div>
        </div>
        <div class="relative">
            <div onclick="toggleDropdown()"
                class="bg-white/10 px-4 py-1.5 rounded-lg flex items-center gap-2 border border-white/20 cursor-pointer">
                <div
                    class="w-7 h-7 hidden md:flex bg-green-400 rounded-full items-center justify-center text-green-900 font-bold text-xs">
                    SA
                </div>
                <span class="md:text-sm text-xs font-semibold flex items-center gap-2">
                    Super Admin
                </span>
                <i class="fas fa-chevron-down text-xs"></i>
            </div>

            <div id="dropdownProfile"
                class="hidden absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg text-gray-700 text-sm overflow-hidden z-50">
                <button class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                    <i class="fas fa-user"></i> Profile
                </button>

                <livewire:auth.logout />
            </div>
        </div>
    </div>


    <div class="p-4 md:p-8 lg:p-10 pb-28">
        <div class="mb-6 animate-item rounded-2xl border border-gray-200 bg-white px-5 py-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800">
                        Kelola Departemen Perusahaan
                    </h1>

                    <p class="mt-1 text-sm text-gray-500">
                        Tambah, ubah, nonaktifkan, dan hapus departemen perusahaan serta atur relasi lokasi absensinya.
                    </p>
                </div>

                <div
                    class="hidden sm:flex h-10 w-10 items-center justify-center rounded-full bg-brand-light text-brand">
                    🏢
                </div>
            </div>
        </div>

        <livewire:super-admin.department-management />
    </div>

    {{-- ✅ Loading Modal diletakkan di root body agar fixed inset-0 bisa menutupi seluruh halaman --}}
    <x-loading-modal target="logout" message="Sedang keluar dari sistem..." keepAlive="true" />

    {{-- bagian untuk komponen error dan succes pesan di pojok kanan atas --}}
    <x-flash-message type="success" sessionKey="success" on="flash-success" />
    <x-flash-message type="error" sessionKey="error" on="flash-error" />

    <!-- Reusable Bottom Footer Navigation Component -->
    <x-superadmin-footer />

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdownProfile');
            dropdown.classList.toggle('hidden');
        }

        // klik di luar nutup dropdown
        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById('dropdownProfile');
            const trigger = e.target.closest('[onclick="toggleDropdown()"]');

            if (!trigger && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
    @livewireScripts
</body>

</html>
