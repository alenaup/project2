<div
    class="relative bg-linear-to-br from-white to-gray-50 rounded-3xl border border-gray-100 shadow-md p-6 space-y-5 overflow-hidden">

    <!-- subtle glow -->
    <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-100 blur-3xl opacity-30"></div>

    <!-- HEADER -->
    <div class="relative z-10 flex items-center justify-between">
        <h2 class="font-semibold text-gray-800 text-lg">Form Absensi</h2>
        <span class="text-xs bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full font-medium">
            Hari Ini
        </span>
    </div>

    <!-- BUTTON MASUK / KELUAR -->
    <form wire:submit.prevent="simpanAbsensi" >
        <div  x-data="{ jenisAbsensi: @entangle('jenisAbsensi') }"
         class="grid grid-cols-1 md:grid-cols-2 gap-3 relative z-10">

            {{-- button dengan aksi absen masuk untuk melakukan absensi masuk kerja --}}
            {{-- mengirim data ke folder public js karyawan_js dashboard dengan fungtion absenMasuk --}}
            <button id="btnMasuk" @click="jenisAbsensi = 'masuk'" type="button"
            :class="jenisAbsensi === 'masuk' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700'"
            class="py-2.5 rounded-xl font-medium transition"
                class="group flex items-center justify-center gap-2 shadow-sm hover:shadow-md hover:-translate-y-0.5 ">
                <i class="fas fa-sign-in-alt text-sm group-hover:scale-110 transition"></i>
                Absen Masuk
            </button>

            {{-- button dengan aksi absen keluar untuk melakukan absensi keluar kerja --}}
            {{-- mengirim data ke folder public js karyawan_js dashboard dengan fungtion absenKeluar --}}
            <button id="btnKeluar" @click="jenisAbsensi = 'keluar'" type="button"
            :class="jenisAbsensi === 'keluar' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700'"
            class="py-2.5 rounded-xl font-medium transition"
                class="group flex items-center justify-center gap-2 shadow-sm hover:shadow-md hover:-translate-y-0.5 ">
                <i class="fas fa-sign-out-alt text-sm group-hover:scale-110 transition"></i>
                Absen Keluar
            </button>
        </div>

        <!-- WAKTU -->
        <div class="relative z-10">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Waktu</label>
            <div class="relative mt-1"> 
                <input id="waktu" type="text" name="waktu" 
                    class="w-full border border-gray-200 rounded-xl p-3 pl-10 bg-white focus:ring-2 focus:ring-emerald-500 outline-none text-sm"
                    readonly wire:model="waktu">
                <i class="fas fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            </div>
        </div>

        <!-- LOKASI -->
        <div class="relative z-10 space-y-2">
            <div class="flex items-center justify-between">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Lokasi GPS</label>

                <!-- BUTTON AMBIL LOKASI -->
                {{-- mengirim data ke folder public js karyawan_js dashboard dengan fungtion ambilLokasi --}}
                <button onclick="ambilLokasi()" type="button"
                    class="text-xs bg-blue-100 text-blue-600 px-3 py-1 rounded-full font-medium hover:bg-blue-200 transition flex items-center gap-1">
                    <i class="fas fa-location-arrow text-[10px]"></i>
                    Ambil Lokasi
                </button>
            </div>

            <div id="map" class="w-full h-52 rounded-2xl border border-gray-100 overflow-hidden shadow-inner">
            </div>

            <!-- info lokasi -->
            {{-- mendapatkan data info lokasi --}}
            <p id="infoLokasi" class="text-xs text-gray-400">
                Lokasi belum diambil
            </p>
        </div>

        <!-- SUBMIT -->
        {{-- mengirim data ke folder public js karyawan_js dashboard dengan fungtion simpanAbsensi --}}
        <button type="submit" 
            class="relative z-10 w-full bg-emerald-600 text-white py-3 rounded-2xl font-semibold shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition flex items-center justify-center gap-2">
            <i class="fas fa-save text-sm"></i>
            Simpan Absensi
        </button>
    </form>

</div>

<script>
    // Mengambil lokasi kantor dari departemen user yang login
    @php
        $user = auth()->user();
        $lokasi = $user && $user->departemen ? $user->departemen->lokasi : null;
        $lat = $lokasi ? $lokasi->latitude : 1.083481;
        $lng = $lokasi ? $lokasi->longitude : 104.030512;
        $radius = $lokasi ? $lokasi->radius : 100;
        $namaLokasi = $lokasi ? $lokasi->nama_lokasi : 'Kantor Pusat';
    @endphp

    var kantor = {
        lat: {{ $lat }},
        lng: {{ $lng }},
        nama: "{{ $namaLokasi }}"
    };
    var radiusKantor = {{ $radius }};
    var lokasi = null; // Lokasi pengguna
</script>
