document.addEventListener('login-success', function (event) {
    // Tampilkan layar overlay animasi
    const overlay = document.getElementById('animationOverlay');
    overlay.style.display = 'flex';

    // Tambahkan loading bar progres
    const loader = document.createElement('div');
    loader.classList.add('anim-loader');
    overlay.appendChild(loader);

    // Tunggu sampai animasi selesai (sekitar 3.5 detik) sebelum memindahkan halaman
    setTimeout(function () {
        window.location.href = event.detail.url;
    }, 3500);
});
