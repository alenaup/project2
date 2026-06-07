<div
    class="relative bg-linear-to-br from-white to-gray-50 rounded-3xl border border-gray-100 shadow-md p-6 space-y-5 overflow-hidden">

    <!-- subtle glow -->
    <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-100 blur-3xl opacity-30"></div>

    <!-- HEADER -->
    <div class="relative z-10 flex items-center justify-between">
        <h2 class="font-semibold text-gray-800 text-lg">Form Absensi</h2>
        <span class="text-xs bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full font-medium">
            {{ now()->translatedFormat('l, d M Y') }}
        </span>
    </div>

    <!-- ── FLASH MESSAGES ──────────────────────────────────────────────── -->
    @if (session()->has('success'))
        <div
            class="relative z-10 flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl px-4 py-3">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div
            class="relative z-10 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- ── INFO JADWAL ─────────────────────────────────────────────────── -->
    @if ($adaJadwal && $jamMasuk)
        <div class="relative z-10 flex items-center gap-3 bg-blue-50 border border-blue-100 rounded-2xl px-4 py-3">
            <i class="fas fa-calendar-check text-blue-500"></i>
            <div class="text-xs text-blue-700 leading-relaxed">
                <span class="font-semibold">Jadwal Hari Ini</span><br>
                Jam Masuk: <strong>{{ \Carbon\Carbon::parse($jamMasuk)->format('H:i') }}</strong>
                &nbsp;|&nbsp;
                Jam Keluar: <strong>{{ \Carbon\Carbon::parse($jamKeluar)->format('H:i') }}</strong>
            </div>
        </div>
    @endif

    <!-- ── TIDAK ADA JADWAL ────────────────────────────────────────────── -->
    @if (!$adaJadwal)
        <div class="relative z-10 flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-2xl px-4 py-3">
            <i class="fas fa-calendar-times text-amber-500 text-lg"></i>
            <div class="text-sm text-amber-700">
                <span class="font-semibold">Tidak Ada Jadwal</span><br>
                <span class="text-xs">{{ $pesanJadwal ?? 'Hubungi admin untuk informasi lebih lanjut.' }}</span>
            </div>
        </div>

        <!-- Form dinonaktifkan -->
        <div class="relative z-10 opacity-40 pointer-events-none select-none space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div class="py-2.5 rounded-xl bg-gray-100 text-center text-gray-400 text-sm font-medium">Absen Masuk
                </div>
                <div class="py-2.5 rounded-xl bg-gray-100 text-center text-gray-400 text-sm font-medium">Absen Keluar
                </div>
            </div>
            <div class="w-full bg-gray-200 text-gray-400 py-3 rounded-2xl text-center text-sm font-semibold">
                <i class="fas fa-lock mr-1"></i> Form Tidak Tersedia
            </div>
        </div>
    @else
        <!-- ── FORM ABSENSI (jadwal tersedia) ─────────────────────────────── -->
        <form wire:submit.prevent="simpanAbsensi">
            <div x-data="{ jenisAbsensi: @entangle('jenisAbsensi') }" class="space-y-4">

                <!-- STATUS ABSENSI HARI INI -->
                <div class="relative z-10 flex gap-2 text-xs">
                    <span
                        class="flex items-center gap-1 px-2.5 py-1 rounded-full font-medium
                    {{ $sudahAbsenMasuk ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        <i class="fas fa-{{ $sudahAbsenMasuk ? 'check' : 'times' }}-circle text-[10px]"></i>
                        Masuk
                    </span>
                    <span
                        class="flex items-center gap-1 px-2.5 py-1 rounded-full font-medium
                    {{ $sudahAbsenKeluar ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                        <i class="fas fa-{{ $sudahAbsenKeluar ? 'check' : 'times' }}-circle text-[10px]"></i>
                        Keluar
                    </span>
                </div>

                <!-- BUTTON MASUK / KELUAR -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 relative z-10">

                    {{-- Absen Masuk — dinonaktifkan jika sudah absen masuk --}}
                    <button id="btnMasuk" @click="jenisAbsensi = 'masuk'" type="button" @disabled($sudahAbsenMasuk)
                        :class="jenisAbsensi === 'masuk' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700'"
                        class="py-2.5 rounded-xl font-medium transition flex items-center justify-center gap-2
                           shadow-sm hover:shadow-md hover:-translate-y-0.5
                           {{ $sudahAbsenMasuk ? 'opacity-50 cursor-not-allowed' : '' }}">
                        <i class="fas fa-sign-in-alt text-sm"></i>
                        Absen Masuk
                        @if ($sudahAbsenMasuk)
                            <i class="fas fa-check text-xs"></i>
                        @endif
                    </button>

                    {{-- Absen Keluar — hanya aktif jika sudah absen masuk --}}
                    <button id="btnKeluar" @click="jenisAbsensi = 'keluar'" type="button" @disabled(!$sudahAbsenMasuk || $sudahAbsenKeluar)
                        :class="jenisAbsensi === 'keluar' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700'"
                        class="py-2.5 rounded-xl font-medium transition flex items-center justify-center gap-2
                           shadow-sm hover:shadow-md hover:-translate-y-0.5
                           {{ !$sudahAbsenMasuk || $sudahAbsenKeluar ? 'opacity-50 cursor-not-allowed' : '' }}">
                        <i class="fas fa-sign-out-alt text-sm"></i>
                        Absen Keluar
                        @if ($sudahAbsenKeluar)
                            <i class="fas fa-check text-xs"></i>
                        @endif
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

                <!-- LOKASI GPS -->
                <div class="relative z-10 space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Lokasi GPS</label>
                        <button onclick="ambilLokasi()" type="button"
                            class="text-xs bg-blue-100 text-blue-600 px-3 py-1 rounded-full font-medium hover:bg-blue-200 transition flex items-center gap-1">
                            <i class="fas fa-location-arrow text-[10px]"></i>
                            Ambil Lokasi
                        </button>
                    </div>

                    <div id="map" wire:ignore
                        class="w-full h-100 rounded-2xl border border-gray-100 overflow-hidden shadow-inner"></div>

                    <p id="infoLokasi" class="text-xs text-gray-400">
                        Lokasi belum diambil
                    </p>
                </div>

                <!-- SUBMIT -->
                <div
                    x-data="{ buttonDisabled: @entangle('sudahAbsenMasuk') && @entangle('sudahAbsenKeluar') }"    
                >
                    <template x-if="buttonDisabled" >
                        <button type="submit" :disabled="buttonDisabled"
                        class="relative z-10 w-full bg-emerald-600 text-white py-3 rounded-2xl font-semibold shadow-sm
                       hover:shadow-lg hover:-translate-y-0.5 transition flex items-center justify-center gap-2
                       disabled:opacity-50 disabled:cursor-not-allowed"
                        >Anda Sudah absen hari ini <i class="fas fa-check"></i> </button>
                    </template>
                    <template x-if="!buttonDisabled" >
                        <button type="submit" 
                            class="relative z-10 w-full bg-emerald-600 text-white py-3 rounded-2xl font-semibold shadow-sm
                        hover:shadow-lg hover:-translate-y-0.5 transition flex items-center justify-center gap-2
                        disabled:opacity-50 disabled:cursor-not-allowed"
                        >Absen <i class="fas fa-paper-plane"></i> </button>
                    </template>
                </div>
            </div>
        </form>
    @endif

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
    var lokasi = null;
</script>
