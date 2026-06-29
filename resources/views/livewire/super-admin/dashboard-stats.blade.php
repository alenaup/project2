{{--
    komponen card pada halaman superAdmin
    memiliki fungsi
    - menampilkan jumlah admin outsourcing, hr, kepala departemen dan total pengguna
    - dapat reload data secara otomatis apabila ada perubahan data pada tabel
--}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 ">

    <!-- Admin Outsourcing -->
    <div class="animate-bitem bg-white rounded-xl p-4 shadow flex items-center gap-4 border border-gray-200">
        <div class="w-12 h-12 bg-pink-100 flex items-center justify-center rounded-xl">
            <svg class="w-6 h-6 text-pink-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21a8 8 0 0 0-16 0" />
                <circle cx="12" cy="7" r="4" />
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500">Admin Outsourcing</p>
            <h3 class="text-xl font-bold text-gray-800">{{ $totalAdminVendor }}</h3>
        </div>
    </div>

    <!-- HR -->
    <div class="animate-bitem bg-white rounded-xl p-4 shadow flex items-center gap-4 border border-gray-200">
        <div class="w-12 h-12 bg-yellow-100 flex items-center justify-center rounded-xl">
            <svg class="w-6 h-6 text-yellow-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2">
                <path d="M16 11V7a4 4 0 0 0-8 0v4" />
                <rect x="6" y="11" width="12" height="10" rx="2" />
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500">HR</p>
            <h3 class="text-xl font-bold text-gray-800">{{ $totalHr }}</h3>
        </div>
    </div>

    <!-- Kepala Departemen -->
    <div class="animate-bitem bg-white rounded-xl p-4 shadow flex items-center gap-4 border border-gray-200">
        <div class="w-12 h-12 bg-gray-100 flex items-center justify-center rounded-xl">
            <svg class="w-6 h-6 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2">
                <path d="M4 19h16" />
                <path d="M6 17V7l6-3 6 3v10" />
                <path d="M9 17v-6h6v6" />
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500">Kepala Departemen</p>
            <h3 class="text-xl font-bold text-gray-800">{{ $totalKepalaDepartemen }}</h3>
        </div>
    </div>

    <!-- Total -->
    <div class="animate-bitem bg-green-50 rounded-xl p-4 shadow flex items-center gap-4 border border-green-200">
        <div class="w-12 h-12 bg-green-200 flex items-center justify-center rounded-xl p-2">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                <g id="SVGRepo_iconCarrier">
                    <path
                        d="M5.30769 7.88571C5.30769 5.73969 7.06411 4 9.23077 4C11.3974 4 13.1538 5.73969 13.1538 7.88571C13.1538 10.0317 11.3974 11.7714 9.23077 11.7714C7.06411 11.7714 5.30769 10.0317 5.30769 7.88571Z"
                        fill="#363853"></path>
                    <path
                        d="M6.75123 13.5261L6.91563 13.5001C8.44935 13.2577 10.0122 13.2577 11.5459 13.5001L11.7103 13.5261C13.8714 13.8677 15.4615 15.714 15.4615 17.8816C15.4615 19.0516 14.504 20 13.3228 20H5.13874C3.95755 20 3 19.0516 3 17.8816C3 15.714 4.59016 13.8677 6.75123 13.5261Z"
                        fill="#363853"></path>
                    <path
                        d="M14.7692 4C14.3869 4 14.0769 4.307 14.0769 4.68571C14.0769 5.06442 14.3869 5.37143 14.7692 5.37143C16.1712 5.37143 17.3077 6.49711 17.3077 7.88571C17.3077 9.27432 16.1712 10.4 14.7692 10.4C14.3869 10.4 14.0769 10.707 14.0769 11.0857C14.0769 11.4644 14.3869 11.7714 14.7692 11.7714C16.9359 11.7714 18.6923 10.0317 18.6923 7.88571C18.6923 5.73969 16.9359 4 14.7692 4Z"
                        fill="#363853"></path>
                    <path
                        d="M15.9149 13.4916C15.5326 13.4916 15.2226 13.7986 15.2226 14.1773C15.2226 14.556 15.5326 14.863 15.9149 14.863H16.8086C16.8829 14.863 16.9573 14.8688 17.0306 14.8804C18.5197 15.1158 19.6154 16.388 19.6154 17.8816C19.6154 18.2942 19.2778 18.6286 18.8613 18.6286H16.9753C16.5929 18.6286 16.283 18.9356 16.283 19.3143C16.283 19.693 16.5929 20 16.9753 20H18.8613C20.0425 20 21 19.0516 21 17.8816C21 15.714 19.4098 13.8677 17.2488 13.5261C17.1032 13.5031 16.9559 13.4916 16.8086 13.4916H15.9149Z"
                        fill="#363853"></path>
                </g>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500">Total</p>
            <h3 class="text-xl font-bold text-green-700">{{ $totalPengguna }}</h3>
        </div>
    </div>

</div>
