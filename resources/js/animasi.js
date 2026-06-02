document.addEventListener('DOMContentLoaded', () => {
    const elements = document.querySelectorAll('.animate-item');

    elements.forEach((element, index) => {
        setTimeout(() => {
            element.classList.add('show');
        }, index * 150);
    });
});
