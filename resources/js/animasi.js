document.addEventListener('DOMContentLoaded', () => {
    const elements = document.querySelectorAll('.animate-item');

    elements.forEach((element, index) => {
        setTimeout(() => {
            element.classList.add('show');
        }, index * 150);
    });

    const b_elements = document.querySelectorAll('.animate-bitem');

    b_elements.forEach((b_element, index) => {
        setTimeout(() => {
            b_element.classList.add('show');
        }, index * 150);
    });

    // Remove 'is-loading' class from html tag once initial animations have run/finished
    const maxItems = Math.max(elements.length, b_elements.length);
    const duration = maxItems * 150 + 800; // Calculate duration based on number of items + animation length
    setTimeout(() => {
        document.documentElement.classList.remove('is-loading');
    }, duration);
});
