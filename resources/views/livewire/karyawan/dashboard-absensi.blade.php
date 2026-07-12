<div
    class="animate-bitem relative bg-linear-to-br from-white to-gray-50 rounded-3xl border border-gray-100 shadow-md p-6 space-y-5 overflow-hidden">

    <!-- subtle glow -->
    <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-100 blur-3xl opacity-30"></div>

    <!-- HEADER -->
    <div class="relative z-10 flex items-center justify-between">
        <h2 class="font-semibold text-gray-800 text-lg">Form Absensi</h2>
        <span class="text-xs bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full font-medium">
            {{ now()->translatedFormat('l, d M Y') }}
        </span>
    </div>

    {{-- Flash Message ditangani di layout global (karyawanOutsourcing.blade.php) --}}

    <!-- ── TIDAK ADA JADWAL ────────────────────────────────────────────── -->
    @if (!$adaJadwal)
        <div class="relative z-10 flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-2xl px-4 py-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">

                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3.75 7.5h16.5M5.25 5.25h13.5A1.5 1.5 0 0 1 20.25 6.75v12A1.5 1.5 0 0 1 18.75 20.25H5.25A1.5 1.5 0 0 1 3.75 18.75v-12A1.5 1.5 0 0 1 5.25 5.25Z
        M9 15l6-6M15 15l-6-6" />
            </svg>
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
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1 inline" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16.5 10.5V7a4.5 4.5 0 0 0-9 0v3.5M6.75 10.5h10.5A1.75 1.75 0 0 1 19 12.25v7A1.75 1.75 0 0 1 17.25 21h-10.5A1.75 1.75 0 0 1 5 19.25v-7a1.75 1.75 0 0 1 1.75-1.75Z" />
                </svg>

                Form Tidak Tersedia
            </div>
        </div>
    @elseif ($kehadiranSudahTerisi)
        {{-- ── STATUS KEHADIRAN SUDAH TERISI (bukan absensi kerja) ──────── --}}
        @php
            $tipeConfig = match($tipeKehadiranHariIni) {
                'Sakit'  => ['bg' => 'bg-red-50',    'border' => 'border-red-200',    'icon_bg' => 'bg-red-100',    'text' => 'text-red-600',    'badge_bg' => 'bg-red-100',    'badge_text' => 'text-red-700',    'icon' => 'fa-heart-pulse',    'desc' => 'Kamu tercatat sakit hari ini. Semoga lekas pulih!'],
                'Izin'   => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'icon_bg' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'badge_bg' => 'bg-yellow-100', 'badge_text' => 'text-yellow-700', 'icon' => 'fa-file-signature', 'desc' => 'Kamu memiliki izin yang telah disetujui hari ini.'],
                'Cuti'   => ['bg' => 'bg-blue-50',   'border' => 'border-blue-200',   'icon_bg' => 'bg-blue-100',   'text' => 'text-blue-600',   'badge_bg' => 'bg-blue-100',   'badge_text' => 'text-blue-700',   'icon' => 'fa-umbrella-beach',  'desc' => 'Kamu sedang dalam masa cuti hari ini. Nikmati hari liburmu!'],
                'Mankir' => ['bg' => 'bg-gray-50',   'border' => 'border-gray-300',   'icon_bg' => 'bg-gray-200',   'text' => 'text-gray-600',   'badge_bg' => 'bg-gray-200',   'badge_text' => 'text-gray-700',   'icon' => 'fa-ban',             'desc' => 'Kamu tercatat tidak hadir tanpa keterangan. Hubungi atasan.'],
                default  => ['bg' => 'bg-gray-50',   'border' => 'border-gray-200',   'icon_bg' => 'bg-gray-100',   'text' => 'text-gray-600',   'badge_bg' => 'bg-gray-100',   'badge_text' => 'text-gray-700',   'icon' => 'fa-circle-info',     'desc' => 'Status kehadiran hari ini sudah tercatat.'],
            };
        @endphp

        <div class="relative z-10 rounded-2xl border {{ $tipeConfig['border'] }} {{ $tipeConfig['bg'] }} p-5 space-y-4">
            {{-- Icon + Label --}}
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 {{ $tipeConfig['icon_bg'] }} {{ $tipeConfig['text'] }} flex items-center justify-center rounded-2xl shadow-inner">
                    <i class="fas {{ $tipeConfig['icon'] }} text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Status Kehadiran Hari Ini</p>
                    <span class="inline-block mt-0.5 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $tipeConfig['badge_bg'] }} {{ $tipeConfig['badge_text'] }}">
                        {{ $tipeKehadiranHariIni }}
                    </span>
                </div>
            </div>

            {{-- Deskripsi --}}
            <p class="text-sm text-gray-500 leading-relaxed">{{ $tipeConfig['desc'] }}</p>

            {{-- Divider --}}
            <div class="border-t border-dashed {{ $tipeConfig['border'] }}"></div>

            {{-- Notifikasi blokir absensi --}}
            <div class="flex items-start gap-3 rounded-xl bg-white/70 border {{ $tipeConfig['border'] }} px-4 py-3">
                <i class="fas fa-lock text-sm {{ $tipeConfig['text'] }} mt-0.5 flex-shrink-0"></i>
                <div>
                    <p class="text-sm font-semibold text-gray-700">Absensi Tidak Tersedia</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Form absensi dinonaktifkan karena status kehadiran kamu hari ini
                        sudah tercatat sebagai <strong class="{{ $tipeConfig['text'] }}">{{ $tipeKehadiranHariIni }}</strong>.
                        Jika ada kesalahan, hubungi HR atau Kepala Departemen.
                    </p>
                </div>
            </div>

            {{-- Form nonaktif (visual saja) --}}
            <div class="opacity-30 pointer-events-none select-none space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div class="py-2.5 rounded-xl bg-gray-100 text-center text-gray-400 text-sm font-medium">Absen Masuk</div>
                    <div class="py-2.5 rounded-xl bg-gray-100 text-center text-gray-400 text-sm font-medium">Absen Keluar</div>
                </div>
                <div class="w-full bg-gray-200 text-gray-400 py-3 rounded-2xl text-center text-sm font-semibold">
                    <i class="fas fa-lock mr-1"></i> Form Terkunci
                </div>
            </div>
        </div>
    @else
        {{-- ── FORM ABSENSI (jadwal tersedia, tidak ada status lain) ────── --}}
        <form wire:submit.prevent="simpanAbsensi" x-data
            @submit="$dispatch('show-loading', { message: 'Memproses absensi...' })">
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
                    <div id="map" wire:ignore
                        class="w-full h-100 rounded-2xl border border-gray-100 overflow-hidden shadow-inner"></div>

                    <p id="infoLokasi" class="text-xs text-gray-400">
                        Lokasi belum diambil
                    </p>
                </div>

                <!-- SUBMIT -->
                <div x-data="{ sudahMasuk: @entangle('sudahAbsenMasuk'), sudahKeluar: @entangle('sudahAbsenKeluar') }" x-bind:class="''">
                    <div class="flex items-end justify-end mb-2">
                        <button onclick="ambilLokasi()" type="button"
                            class="relative z-10 w-full bg-blue-100 text-blue-600 px-3 py-1 rounded-full font-medium hover:bg-blue-200 transition flex items-center justify-center gap-2">
                            <i class="fas fa-location-arrow text-[10px]"></i>
                            Ambil Lokasi
                        </button>
                    </div>
                    <template x-if="sudahMasuk && sudahKeluar">
                        <button type="submit" :disabled="true"
                            class="relative z-10 w-full bg-emerald-600 text-white py-3 rounded-2xl font-semibold shadow-sm
                       hover:shadow-lg hover:-translate-y-0.5 transition flex items-center justify-center gap-2
                       disabled:opacity-50 disabled:cursor-not-allowed">Anda
                            Sudah absen hari ini <i class="fas fa-check"></i> </button>
                    </template>
                    <template x-if="!(sudahMasuk && sudahKeluar)">
                        <button type="submit" @disabled(empty($latitude) || empty($longitude) || empty($waktu))
                            class="relative z-10 w-full bg-emerald-600 text-white py-3 rounded-2xl font-semibold shadow-sm
                        hover:shadow-lg hover:-translate-y-0.5 transition flex items-center justify-center gap-2
                        disabled:opacity-50 disabled:cursor-not-allowed">Absen
                            <i class="fas fa-paper-plane"></i> </button>
                    </template>
                </div>
            </div>
        </form>

    @endif

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
</div>
