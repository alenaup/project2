var map;
var userMarker;
var officeMarker;
var officeCircle;

document.addEventListener('DOMContentLoaded', function () {
    if (typeof L !== 'undefined' && document.getElementById('map')) {
        // Inisialisasi map dengan pusat di lokasi kantor
        map = L.map('map').setView([kantor.lat, kantor.lng], 16);

        // Tile layer dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Tambah marker kantor
        officeMarker = L.marker([kantor.lat, kantor.lng])
            .addTo(map)
            .bindPopup(kantor.nama)
            .openPopup();

        // Tambah circle radius kantor (misal 100m)
        officeCircle = L.circle([kantor.lat, kantor.lng], {
            color: '#10b981', // warna emerald
            fillColor: '#10b981',
            fillOpacity: 0.15,
            radius: radiusKantor
        }).addTo(map);
    }
});

function updateMapUser() {
    if (!map) return;

    // Hapus marker user lama jika ada
    if (userMarker) {
        map.removeLayer(userMarker);
    }

    // Tambah marker user baru dengan warna biru
    userMarker = L.marker([lokasi.lat, lokasi.lng], {
        icon: L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        })
    }).addTo(map)
        .bindPopup("Lokasi Anda")
        .openPopup();

    // Sesuaikan zoom map agar menampilkan kantor dan user sekaligus
    const bounds = L.latLngBounds([
        [kantor.lat, kantor.lng],
        [lokasi.lat, lokasi.lng]
    ]);
    map.fitBounds(bounds, { padding: [50, 50] });

    // Update info teks koordinat di UI
    const infoLokasi = document.getElementById('infoLokasi');
    if (infoLokasi) {
        infoLokasi.innerHTML = `Lokasi diambil: ${lokasi.lat.toFixed(6)}, ${lokasi.lng.toFixed(6)}`;
        infoLokasi.classList.remove('text-gray-400');
        infoLokasi.classList.add('text-emerald-600', 'font-medium');
    }
}

function ambilLokasi() {

    if (!navigator.geolocation) {
        window.dispatchEvent(new CustomEvent('flash-error', { detail: { message: "Browser tidak mendukung GPS." } }));
        return;
    }

    // ✅ Cek status izin GPS terlebih dulu sebelum menampilkan loading modal
    // Ini mencegah modal berkedip saat izin sudah ditolak sebelumnya
    const doGetPosition = () => {
        // Tampilkan loading modal SETELAH kita tahu izin tidak ditolak
        window.dispatchEvent(new CustomEvent('show-loading', { detail: { message: 'Mengambil lokasi GPS...' } }));

        navigator.geolocation.getCurrentPosition(

            function (pos) {

                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;

                // Hitung jarak ke kantor (meter)
                const jarak = map.distance(
                    [lat, lng],
                    [kantor.lat, kantor.lng]
                );

                // Update marker pada map
                lokasi = {
                    lat: lat,
                    lng: lng
                };

                updateMapUser();

                console.log("Jarak ke kantor:", jarak);

                // Cari komponen Livewire
                const componentEl = document.getElementById('map').closest('[wire\\:id]');
                let component = null;
                if (componentEl && typeof Livewire !== 'undefined') {
                    component = Livewire.find(componentEl.getAttribute('wire:id'));
                }

                if (component) {
                    component.$set('latitude', String(lat));
                    component.$set('longitude', String(lng));
                    component.$set('jarak', Math.round(jarak));

                    const now = new Date();
                    const waktu = now.getFullYear() + '-'
                        + String(now.getMonth() + 1).padStart(2, '0') + '-'
                        + String(now.getDate()).padStart(2, '0') + ' '
                        + String(now.getHours()).padStart(2, '0') + ':'
                        + String(now.getMinutes()).padStart(2, '0') + ':'
                        + String(now.getSeconds()).padStart(2, '0');

                    component.$set('waktu', waktu);

                    console.log("Data lokasi & waktu dikirim ke Livewire:", { lat, lng, jarak: Math.round(jarak), waktu });
                } else {
                    console.error("Komponen Livewire tidak ditemukan!");
                }

                // Cek radius
                if (jarak > radiusKantor) {
                    window.dispatchEvent(new CustomEvent('flash-error', {
                        detail: { message: `Anda berada di luar area kantor. Jarak: ${Math.round(jarak)} meter` }
                    }));
                    // Sembunyikan modal — tidak ada Livewire request yang akan terjadi
                    window.dispatchEvent(new CustomEvent('hide-loading'));
                    return;
                }

                // Jika dalam radius — kirim ke Livewire (hook Livewire akan menutup modal otomatis)
                window.dispatchEvent(new CustomEvent('flash-success', {
                    detail: { message: `Lokasi valid. Jarak ke kantor: ${Math.round(jarak)} meter` }
                }));

            },

            function (err) {
                // Sembunyikan loading modal saat GPS gagal
                window.dispatchEvent(new CustomEvent('hide-loading'));

                switch (err.code) {
                    case err.PERMISSION_DENIED:
                        window.dispatchEvent(new CustomEvent('flash-error', { detail: { message: "Izin lokasi ditolak. Silakan izinkan akses lokasi pada browser Anda." } }));
                        break;
                    case err.POSITION_UNAVAILABLE:
                        window.dispatchEvent(new CustomEvent('flash-error', { detail: { message: "Lokasi tidak tersedia. Pastikan GPS Anda aktif." } }));
                        break;
                    case err.TIMEOUT:
                        window.dispatchEvent(new CustomEvent('flash-error', { detail: { message: "Pengambilan lokasi terlalu lama (timeout)." } }));
                        break;
                    default:
                        window.dispatchEvent(new CustomEvent('flash-error', { detail: { message: "Gagal mengambil lokasi." } }));
                }
            },

            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    };

    // Gunakan Permissions API untuk cek status izin SEBELUM membuka loading modal
    if (navigator.permissions) {
        navigator.permissions.query({ name: 'geolocation' }).then(function (result) {
            if (result.state === 'denied') {
                // Izin sudah ditolak — langsung tampilkan pesan error TANPA membuka modal
                window.dispatchEvent(new CustomEvent('flash-error', {
                    detail: { message: "Izin lokasi ditolak. Silakan izinkan akses lokasi di pengaturan browser Anda." }
                }));
            } else {
                // Izin 'granted' atau 'prompt' — aman untuk menampilkan loading dan meminta lokasi
                doGetPosition();
            }
        });
    } else {
        // Browser tidak mendukung Permissions API — langsung coba (fallback)
        doGetPosition();
    }
}
