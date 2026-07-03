document.addEventListener('DOMContentLoaded', () => {
    const elements = document.querySelectorAll('.animate-item');
    const staggerDelay = 40; // Lebih cepat dan responsif (dari 150ms)
    const maxStagger = 8; // Batasi total antrean stagger agar halaman cepat tampil

    elements.forEach((element, index) => {
        const delay = Math.min(index, maxStagger) * staggerDelay;
        setTimeout(() => {
            element.classList.add('show');
        }, delay);
    });

    const b_elements = document.querySelectorAll('.animate-bitem');

    b_elements.forEach((b_element, index) => {
        const delay = Math.min(index, maxStagger) * staggerDelay;
        setTimeout(() => {
            b_element.classList.add('show');
        }, delay);
    });

    // Hapus class 'is-loading' dari tag html setelah animasi utama selesai
    const maxItems = Math.max(elements.length, b_elements.length);
    const duration = Math.min(maxItems, maxStagger) * staggerDelay + 800; // Capped max duration

    setTimeout(() => {
        document.documentElement.classList.remove('is-loading');
    }, duration);
});

