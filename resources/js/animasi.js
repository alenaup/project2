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
});
