import './bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min';
import Alpine from 'alpinejs';
window.Alpine = Alpine;
document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
});

// hero section auto slide
window.heroSlider = function () {
    return {
        currentSlide: 0,
        slides: [],
        startSlider() {
            this.slides = Array.from(document.querySelectorAll('.custom-slide'));

            if (this.slides.length > 0) {
                setInterval(() => {
                    this.slides[this.currentSlide].classList.remove('active');
                    this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                    this.slides[this.currentSlide].classList.add('active');
                }, 3000);
            }
        }
    };
};

// statistic
    document.addEventListener("alpine:init", () => {
        Alpine.data("statistikCounter", () => ({
            targets: [25, 24, 1, 9, 4, 7, 1, 7],
            counts: [0, 0, 0, 0, 0, 0, 0, 0],
            duration: 2000,
            observer: null,

            startCounters() {
                let startTime = null;
                const animate = (timestamp) => {
                    if (!startTime) startTime = timestamp;
                    let progress = Math.min((timestamp - startTime) / this.duration, 1);

                    this.counts = this.targets.map(target => Math.ceil(progress * target));

                    if (progress < 1) requestAnimationFrame(animate);
                };
                requestAnimationFrame(animate);
            },

            init() {
                this.observer = new IntersectionObserver(entries => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            this.startCounters();
                            this.observer.disconnect();
                        }
                    });
                });
                this.observer.observe(document.querySelector(".statistik-section"));
            }
        }));
    });

    
