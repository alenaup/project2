<!-- 🔥 SIDEBAR -->
@props(['menus' => []])

<aside :class="open ? 'translate-x-0' : '-translate-x-full'"
    class="fixed md:sticky md:top-0 top-0 left-0 h-screen
w-[72%] sm:w-[55%] md:w-[220px] lg:w-[240px]
bg-gradient-to-b from-[#2d7a52] to-[#1e5c3a] text-white
flex flex-col justify-between
transition-transform duration-300 ease-in-out
md:translate-x-0 z-50 shadow-2xl shrink-0">

    <!-- HEADER -->
    <div class="overflow-y-auto flex-1 flex flex-col">
        <div class="text-center px-4 py-5">
            <div class="flex items-center justify-center gap-2">
                <img src="/images/logo (2).webp" alt="EcoGreen Logo" class="w-8 h-8">
                <h3 class="text-white font-bold text-xl tracking-wide">EcoGreen</h3>
            </div>
            <p class="text-white/50 text-xs mt-1 tracking-widest uppercase">Outsourcing</p>
        </div>
        <hr class="border-white/20 mx-4">

        <!-- MENU -->
        <ul id="sidebar-menu" class="mt-4 px-3 space-y-1">
            @foreach ($menus as $menu)
                @php
                    $isActive = request()->is(ltrim($menu['ref'], '/'));
                @endphp

                <li>
                    <a href="{{ $menu['ref'] }}"
                        class="flex items-center gap-3 text-sm font-medium px-3 py-2.5 rounded-xl transition-all duration-200
            {{ $isActive
                ? 'bg-white text-[#2d7a52] shadow-md font-semibold'
                : 'text-white/85 hover:bg-white/15 hover:text-white hover:translate-x-1' }}">

                        <div class="w-5 h-5 flex-shrink-0 {{ $isActive ? 'text-[#2d7a52]' : 'text-white/80' }}">
                            {!! $menu['icon'] !!}
                        </div>
                        {{ $menu['title'] }}

                        @if($isActive)
                            <div class="ml-auto w-1.5 h-1.5 rounded-full bg-[#2d7a52]"></div>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- FOOTER PROFILE -->
    <div class="px-3 pb-5">
        <hr class="border-white/20 mb-4">
        <div class="flex items-center gap-3 bg-white/10 hover:bg-white/20 transition p-3 rounded-xl backdrop-blur-md cursor-default">
            <div class="w-9 h-9 rounded-full bg-white/30 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a8.25 8.25 0 0 1 15 0" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold truncate">{{ $slot }}</p>
                <p class="text-xs text-white/60 truncate">e-outsourcing</p>
            </div>
        </div>
    </div>
</aside>

<!-- 🌫️ OVERLAY (mobile only) -->
<div x-show="open"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="open = false"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm md:hidden z-40">
</div>
