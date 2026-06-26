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

            @php
                $profilPath = 'profiles/profil_' . Auth::id() . '.jpg';
                $hasFoto = Storage::disk('public')->exists($profilPath);
                $fotoUrl = $hasFoto ? asset('storage/' . $profilPath) . '?v=' . Storage::disk('public')->lastModified($profilPath) : null;
            @endphp
            <img src="{{ $fotoUrl ?? '/images/profile.jpg' }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama_lengkap ?? 'User') }}&background=10b981&color=fff&size=128'" class="w-10 h-10 rounded-full object-cover">

            <div class="hidden md:block">
                <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->nama_lengkap ?? 'User' }}</p>
                <p class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', Auth::user()->role->value ?? 'karyawan') }}</p>
            </div>

            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition md:text-sm"
                :class="openProfile ? 'rotate-180' : ''"></i>
        </div>


        <!-- DROPDOWN -->
        <div x-show="openProfile" style="display: none;" @click.outside="openProfile = false" x-transition
            class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg overflow-hidden z-50">


            @if(!request()->routeIs('profil'))
                <a href="{{ route('profil') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">
                    👤 Profile
                </a>
                <hr>
            @endif


            <livewire:auth.logout />
        </div>

    </div>
</div>
