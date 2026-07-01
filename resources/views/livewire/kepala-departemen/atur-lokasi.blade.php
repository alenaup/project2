@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #map { height: 400px; width: 100%; z-index: 10; border-radius: 0.75rem; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mapLokasi', () => ({
            map: null,
            marker: null,
            circle: null,
            lat: @entangle('latitude'),
            lng: @entangle('longitude'),
            radius: @entangle('radius'),

            searchQuery: '',
            searchResults: [],
            isSearching: false,
            searchError: '',

            initMap() {
                // Initialize map
                this.map = L.map('map').setView([this.lat, this.lng], 16);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(this.map);

                // Add draggable marker
                this.marker = L.marker([this.lat, this.lng], {
                    draggable: true
                }).addTo(this.map);

                // Add circle for radius
                this.circle = L.circle([this.lat, this.lng], {
                    color: '#3C8B5E',
                    fillColor: '#3C8B5E',
                    fillOpacity: 0.2,
                    radius: this.radius
                }).addTo(this.map);

                // Event listener when marker is dragged
                this.marker.on('dragend', (e) => {
                    const position = this.marker.getLatLng();
                    this.lat = position.lat;
                    this.lng = position.lng;
                    this.circle.setLatLng(position);
                    this.map.panTo(position);
                });

                // Event listener when map is clicked
                this.map.on('click', (e) => {
                    this.lat = e.latlng.lat;
                    this.lng = e.latlng.lng;
                    this.marker.setLatLng(e.latlng);
                    this.circle.setLatLng(e.latlng);
                    this.map.panTo(e.latlng);
                });

                // Watch radius changes to update circle
                this.$watch('radius', (value) => {
                    if (this.circle) {
                        this.circle.setRadius(parseInt(value) || 0);
                    }
                });

                // Watch lat/lng changes from Livewire to update map
                this.$watch('lat', (value) => {
                    this.updateMapPosition();
                });
                this.$watch('lng', (value) => {
                    this.updateMapPosition();
                });
            },

            updateMapPosition() {
                if (this.marker && this.circle && this.map) {
                    const newLatLng = new L.LatLng(this.lat, this.lng);
                    this.marker.setLatLng(newLatLng);
                    this.circle.setLatLng(newLatLng);
                    this.map.panTo(newLatLng);
                }
            },

            async searchLocation() {
                if (!this.searchQuery.trim()) return;
                this.isSearching = true;
                this.searchError = '';
                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.searchQuery)}`);
                    const data = await response.json();
                    if (data && data.length > 0) {
                        this.searchResults = data;
                    } else {
                        this.searchResults = [];
                        this.searchError = 'Lokasi tidak ditemukan.';
                    }
                } catch (error) {
                    this.searchError = 'Gagal mencari lokasi. Coba lagi.';
                    this.searchResults = [];
                } finally {
                    this.isSearching = false;
                }
            },

            selectLocation(result) {
                this.lat = parseFloat(result.lat);
                this.lng = parseFloat(result.lon);
                this.searchQuery = result.display_name;
                this.searchResults = [];
                this.updateMapPosition();
            }
        }))
    });
</script>
@endpush

<div >
    {{-- Success Message --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.duration.500ms
             class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-semibold mb-5 shadow-sm">
            <i class="fa-solid fa-circle-check text-lg"></i>
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition.duration.500ms
             class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3 text-sm font-semibold mb-5 shadow-sm">
            <i class="fa-solid fa-triangle-exclamation text-lg"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="animate-bitem bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden fade-in-up" style="animation-delay:.05s">
        <div style="background: linear-gradient(to right, #2d6e4a, #3C8B5E);" class="px-5 py-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-white font-bold text-base">
                        <i class="fa-solid fa-map-location-dot mr-2 opacity-75"></i>Konfigurasi Lokasi
                    </h2>
                    <p class="text-green-200 text-xs mt-0.5">Departemen: <strong>{{ $nama_departemen ?? '-' }}</strong></p>
                </div>
            </div>
        </div>

        <div class="animate-bitem p-6 grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="mapLokasi()" x-init="initMap()">
            
            <!-- Map Area -->
            <div class="lg:col-span-2 space-y-3">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-semibold text-gray-700">Peta Lokasi</label>
                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-md"><i class="fa-solid fa-mouse-pointer mr-1"></i>Klik pada peta untuk memindah titik</span>
                </div>
                
                <!-- Search Bar -->
                <div class="relative mb-3 z-50">
                    <div class="flex gap-2 animate-bitem">
                        <input type="text" x-model="searchQuery" @keydown.enter.prevent="searchLocation()" placeholder="Cari alamat atau nama tempat..." class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition shadow-sm">
                        <button type="button" @click="searchLocation()" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-sm transition flex-shrink-0 flex items-center justify-center min-w-[50px]">
                            <i class="fa-solid fa-magnifying-glass" x-show="!isSearching"></i>
                            <i class="fa-solid fa-circle-notch fa-spin" x-show="isSearching" style="display: none;"></i>
                        </button>
                    </div>
                    <!-- Search Results Dropdown -->
                    <ul x-show="searchResults.length > 0" @click.away="searchResults = []" style="animate-bitem display: none;" class="absolute w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-xl max-h-60 overflow-y-auto z-[9999]">
                        <template x-for="result in searchResults" :key="result.place_id">
                            <li @click="selectLocation(result)" class="px-4 py-2.5 hover:bg-emerald-50 cursor-pointer text-sm border-b border-gray-100 last:border-0">
                                <i class="fa-solid fa-location-dot text-gray-400 mr-2"></i>
                                <span x-text="result.display_name" class="text-gray-700"></span>
                            </li>
                        </template>
                    </ul>
                    <div x-show="searchError" x-text="searchError" style="display: none;" class="text-xs text-red-500 mt-1.5 font-medium ml-1"></div>
                </div>

                <div id="map" wire:ignore></div>
            </div>

            <!-- Form Area -->
            <form wire:submit.prevent="simpan" class="space-y-5">
                <div class="animate-bitem">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lokasi <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="nama_lokasi" placeholder="Misal: Kantor Batam" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition shadow-sm">
                    @error('nama_lokasi') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="animate-bitem grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Latitude</label>
                        <input type="text" wire:model="latitude" readonly class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Longitude</label>
                        <input type="text" wire:model="longitude" readonly class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-500 cursor-not-allowed">
                    </div>
                </div>

                <div class="animate-bitem">
                    <div class="flex justify-between mb-1">
                        <label class="block text-sm font-semibold text-gray-700">Radius Absensi <span class="text-red-500">*</span></label>
                        <span class="text-sm font-bold text-emerald-600" x-text="radius + ' Meter'"></span>
                    </div>
                    <input type="range" x-model="radius" min="10" max="1000" step="5" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-emerald-600">
                    <div class="flex justify-between text-xs text-gray-400 mt-1">
                        <span>10m</span>
                        <span>1000m</span>
                    </div>
                    @error('radius') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4 border-t border-gray-100 animate-bitem">
                    <button type="submit" wire:loading.attr="disabled" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl transition shadow-md flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="simpan"><i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan Lokasi</span>
                        <span wire:loading wire:target="simpan"><i class="fa-solid fa-circle-notch fa-spin"></i> Menyimpan...</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
