<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')

    <link rel="icon" type="image/x-icon" href="/images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/lucide@latest"></script>

    <title>Document</title>
    @livewireStyles
</head>

<body class="bg-gray-100" style="font-family: 'Poppins', sans-serif;">

    <!-- TOPBAR -->
    <div
        class="bg-linear-to-r from-green-900 to-green-700 px-10 h-16 flex items-center justify-between text-white shadow">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9  rounded-lg flex items-center justify-center overflow-hidden">
                <img src="/images/logo.png" alt="Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <h1 class="text-sm font-extrabold">Ecogreen e-Outsourcing</h1>
                <p class="text-[11px] text-green-200">Sistem Manajemen Karyawan Outsourcing</p>
            </div>
        </div>
        <div class="relative">
            <div onclick="toggleDropdown()"
                class="bg-white/10 px-4 py-1.5 rounded-lg flex items-center gap-2 border border-white/20 cursor-pointer">
                <div
                    class="w-7 h-7 bg-green-400 rounded-full flex items-center justify-center text-green-900 font-bold text-xs">
                    SA
                </div>
                <span class="text-sm font-semibold flex items-center gap-2">

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


    <div class="p-4 md:p-8 lg:p-10 ">
        <h1 class="text-2xl font-semibold animate-item">Kelola Akun Pengguna</h1>
        <p class="text-sm text-gray-500 mb-4 animate-item">Tambah, ubah, dan hapus akun Admin Outsourcing, HR, dan Kepala
            Departemen
        </p>

        {{-- melakukan request kepada bagian service folder app/livewire/superAdmin/dashboardAdmin --}}
        @livewire('super-admin.dashboard-stats')

        {{-- melakukan request kepada bagian service folder app/livewire/superAdmin/user-management --}}
        @livewire('super-admin.user-management')
    </div>

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
