<div class="flex items-center justify-between mb-6">
    <button @click="open = !open"
        class="top-4 left-4 md:hidden bg-white p-2 rounded-lg shadow
               transition hover:scale-110 active:scale-95 ">
        ☰
    </button>
    <div>
        <h1 class="text-xl font-bold text-gray-800 md:text-2xl">
            Selamat Datang, {{ $slot }}
        </h1>
        <p class="text-gray-500 text-sm">
            Semoga harimu produktif dan menyenangkan
        </p>
    </div>
    <!-- PROFILE DROPDOWN -->
    <div x-data="{ openProfile: false }" class="relative">
        <div @click="openProfile = !openProfile"
            class="flex items-center gap-1 bg-white px-2 py-1 rounded-xl shadow
                    cursor-pointer hover:shadow-lg transition md:px-4 md:py-2 md:gap-3">

            <img src="/images/profile.jpg" class="w-10 h-10 rounded-full object-cover">

            <div class="hidden md:block">
                <p class="text-sm font-semibold text-gray-800">{{ $slot }}</p>
                <p class="text-xs text-gray-500">Admin</p>
            </div>

            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition md:text-sm"
                :class="openProfile ? 'rotate-180' : ''"></i>
        </div>


        <!-- DROPDOWN -->
        <div x-show="openProfile" @click.outside="openProfile = false" x-transition
            class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg overflow-hidden z-50">

            <a href="#" class="block px-4 py-2 text-sm text-gray-800 hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-5 h-5 text-gray-800" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 1 1-7.5 0
        3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a8.25 8.25 0 0 1 15 0" />
                </svg>  Profile
            </a>

            <hr>

            <livewire:auth.logout />
        </div>

    </div>
</div>
