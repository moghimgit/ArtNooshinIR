document.addEventListener('DOMContentLoaded', () => {
    const navToggle = document.getElementById('navToggle');
    const mainNav = document.getElementById('mainNav');
    if (navToggle && mainNav) {
        navToggle.addEventListener('click', () => mainNav.classList.toggle('open'));
    }

    const slider = document.getElementById('heroSlider');
    if (slider) {
        const slides = [...slider.querySelectorAll('.slide')];
        let index = 0;
        const show = (n) => {
            slides.forEach((s, i) => s.classList.toggle('active', i === n));
        };
        slider.querySelector('.prev')?.addEventListener('click', () => {
            index = (index - 1 + slides.length) % slides.length;
            show(index);
        });
        slider.querySelector('.next')?.addEventListener('click', () => {
            index = (index + 1) % slides.length;
            show(index);
        });
        if (slides.length > 1) {
            setInterval(() => {
                index = (index + 1) % slides.length;
                show(index);
            }, 5500);
        }
    }
});