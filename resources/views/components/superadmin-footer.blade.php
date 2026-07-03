<div class="fixed bottom-0 left-0 right-0 z-50 w-full bg-white/55 backdrop-blur-md border-t border-emerald-400/40 shadow-2xl px-6 py-1.5 flex justify-around items-center">
    @php
        $isDashboard = request()->is('super-admin/dashboard');
        $isDepartemen = request()->is('super-admin/departemen');
    @endphp

    <!-- Kelola Pengguna -->
    <a href="/super-admin/dashboard" class="flex flex-col items-center group">
        <div class="relative flex items-center justify-center transition-all duration-300 rounded-full
            {{ $isDashboard 
                ? 'w-9 h-9 bg-green-900 text-white shadow-md shadow-green-950/20' 
                : 'w-8 h-8 bg-emerald-200/80 text-emerald-800 hover:bg-emerald-100 hover:text-emerald-950' }}">
            <i class="fa-solid fa-users-gear text-xs"></i>
        </div>
        <span class="text-[9px] font-bold mt-1 tracking-wider transition-colors duration-200 uppercase
            {{ $isDashboard ? 'text-green-950 font-black' : 'text-emerald-850/80 group-hover:text-emerald-955' }}">
            Akun Pengguna
        </span>
    </a>

    <!-- Kelola Departemen -->
    <a href="/super-admin/departemen" class="flex flex-col items-center group">
        <div class="relative flex items-center justify-center transition-all duration-300 rounded-full
            {{ $isDepartemen 
                ? 'w-9 h-9 bg-green-900 text-white shadow-md shadow-green-950/20' 
                : 'w-8 h-8 bg-emerald-200/80 text-emerald-800 hover:bg-emerald-100 hover:text-emerald-950' }}">
            <i class="fa-solid fa-building-user text-xs"></i>
        </div>
        <span class="text-[9px] font-bold mt-1 tracking-wider transition-colors duration-200 uppercase
            {{ $isDepartemen ? 'text-green-950 font-black' : 'text-emerald-850/80 group-hover:text-emerald-955' }}">
            Departemen
        </span>
    </a>
</div>
