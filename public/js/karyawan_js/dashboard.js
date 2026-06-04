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
        alert("Browser tidak mendukung GPS");
        return;
    }

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

            // Cek radius
            if (jarak > radiusKantor) {

                alert(
                    `Anda berada di luar area kantor.\nJarak: ${Math.round(jarak)} meter`
                );

                return;
            }

            // Jika dalam radius
            alert(
                `Lokasi valid.\nJarak ke kantor: ${Math.round(jarak)} meter`
            );

            // Kirim ke Livewire component dashboardAbsensi
            const componentEl = document.getElementById('map').closest('[wire\\:id]');
            let component = null;
            if (componentEl) {
                component = componentEl.__livewire || (typeof Livewire !== 'undefined' ? Livewire.find(componentEl.getAttribute('wire:id')) : null);
            }

            if (component) {
                component.set('latitude', lat);
                component.set('longitude', lng);
                component.set('jarak', Math.round(jarak));
            } else {
                const fallbackComponent = typeof Livewire !== 'undefined' ? Livewire.first() : null;
                if (fallbackComponent) {
                    fallbackComponent.set('latitude', lat);
                    fallbackComponent.set('longitude', lng);
                    fallbackComponent.set('jarak', Math.round(jarak));
                }
            }

        },

        function (err) {

            switch (err.code) {

                case err.PERMISSION_DENIED:
                    alert("Izin lokasi ditolak. Silakan izinkan akses lokasi pada browser Anda.");
                    break;

                case err.POSITION_UNAVAILABLE:
                    alert("Lokasi tidak tersedia. Pastikan GPS Anda aktif.");
                    break;

                case err.TIMEOUT:
                    alert("Pengambilan lokasi terlalu lama (timeout).");
                    break;

                default:
                    alert("Gagal mengambil lokasi.");
                    }

        },

        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }

    );
}