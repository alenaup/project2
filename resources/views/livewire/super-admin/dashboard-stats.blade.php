<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 animate-item">
    <div class="bg-white rounded-xl p-4 shadow flex items-center gap-4 border border-gray-200">
        <div class="w-12 h-12 bg-pink-100 flex items-center justify-center rounded-xl">
            <i class="fas fa-user text-pink-600 text-lg"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Admin Outsourcing</p>
            <h3 class="text-xl font-bold text-gray-800">{{ $totalAdminVendor }}</h3>
        </div>
    </div>

    <div class="bg-white rounded-xl p-4 shadow flex items-center gap-4 border border-gray-200">
        <div class="w-12 h-12 bg-yellow-100 flex items-center justify-center rounded-xl">
            <i class="fas fa-user-tie text-yellow-600 text-lg"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">HR</p>
            <h3 class="text-xl font-bold text-gray-800">{{ $totalHr }}</h3>
        </div>
    </div>

    <div class="bg-white rounded-xl p-4 shadow flex items-center gap-4 border border-gray-200">
        <div class="w-12 h-12 bg-gray-100 flex items-center justify-center rounded-xl">
            <i class="fas fa-chalkboard-teacher text-gray-600 text-lg"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Kepala Departemen</p>
            <h3 class="text-xl font-bold text-gray-800">{{ $totalKepalaDepartemen }}</h3>
        </div>
    </div>

    <div class="bg-green-50 rounded-xl p-4 shadow flex items-center gap-4 border border-green-200">
        <div class="w-12 h-12 bg-green-200 flex items-center justify-center rounded-xl">
            <i class="fas fa-users text-green-600 text-lg"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500">Total</p>
            <h3 class="text-xl font-bold text-green-700">{{ $totalPengguna }}</h3>
        </div>
    </div>
</div>
