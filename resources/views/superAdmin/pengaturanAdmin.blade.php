<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Lokasi Absensi</title>
    <!-- Leaflet CSS -->
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>

<body class="min-h-screen  items-center justify-center">

    <div class="bg-gradient-to-r from-green-900 to-green-700 px-10 h-16 flex items-center justify-between text-white shadow">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">🌿</div>
            <div>
                <h1 class="text-sm font-extrabold">OutsourceMS</h1>
                <p class="text-[11px] text-green-200">Sistem Manajemen Tenaga Outsourcing</p>
            </div>
        </div>
        <div class="bg-white/10 px-4 py-1.5 rounded-lg flex items-center gap-2 border border-white/20">
            <div
                class="w-7 h-7 bg-green-400 rounded-full flex items-center justify-center text-green-900 font-bold text-xs">
                SA</div>
            <span class="text-sm font-semibold">Super Admin</span>
        </div>
    </div>

    <!-- Header -->
    <div class="max-w-6xl mx-auto my-10 space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">📍 Pengaturan Lokasi Absensi</h1>
            <p class="text-gray-500 text-sm">Atur titik lokasi dan radius agar absensi hanya bisa dilakukan di
                area
                tertentu.</p>
        </div>

        <!-- Form -->
        <div class="p-6 bg-white rounded-2xl shadow-lg">
            <h2 class="text-xl font-semibold mb-4">Pilih Lokasi Absensi 📍</h2>

            <!-- Map -->
            <div id="map" class="w-full h-[400px] rounded-xl"></div>

            <!-- Input Latitude & Longitude -->
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="text-sm text-gray-600">Latitude</label>
                    <input type="text" id="latitude" name="latitude"
                        class="w-full mt-1 p-2 border rounded-lg focus:ring focus:ring-green-300">
                </div>

                <div>
                    <label class="text-sm text-gray-600">Longitude</label>
                    <input type="text" id="longitude" name="longitude"
                        class="w-full mt-1 p-2 border rounded-lg focus:ring focus:ring-green-300">
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-semibold text-gray-600">Radius Absensi (meter)</label>
                    <input type="range" min="10" max="500" value="100"
                        class="w-full mt-2 accent-green-500">
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>10m</span>
                        <span>500m</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-700">
            💡 Tips: Klik pada peta untuk menentukan lokasi secara otomatis. Radius menentukan seberapa jauh
            karyawan
            bisa melakukan absensi dari titik ini.
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3">
            <button class="px-5 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 transition">Reset</button>
            <button class="px-6 py-2 rounded-xl bg-green-500 text-white hover:bg-green-600 transition shadow-lg">Simpan
                Lokasi</button>
        </div>



    </div>
    <script>
        // Default lokasi (Batam biar relevan sama kamu 🌴)
        var map = L.map('map').setView([1.0456, 104.0305], 13);

        // Tile dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var marker;

        // Event klik di map
        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;

            // Isi input
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            // Hapus marker lama
            if (marker) {
                map.removeLayer(marker);
            }

            // Tambah marker baru
            marker = L.marker([lat, lng]).addTo(map);
        });
    </script>
    <script>
        function updateMarkerFromInput() {
            var lat = document.getElementById('latitude').value;
            var lng = document.getElementById('longitude').value;

            if (lat && lng) {
                if (marker) {
                    map.removeLayer(marker);
                }

                marker = L.marker([lat, lng]).addTo(map);
                map.setView([lat, lng], 15);
            }
        }

        document.getElementById('latitude').addEventListener('change', updateMarkerFromInput);
        document.getElementById('longitude').addEventListener('change', updateMarkerFromInput);
    </script>
    <script>
        navigator.geolocation.getCurrentPosition(function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;

            map.setView([lat, lng], 15);

            marker = L.marker([lat, lng]).addTo(map);

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        });
    </script>
