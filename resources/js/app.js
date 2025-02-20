import './bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min';
import Alpine from 'alpinejs';
window.Alpine = Alpine;
document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
});

// hero section auto slide


// stats
    // document.addEventListener("alpine:init", () => {
    //     Alpine.data("statistikCounter", () => ({
    //         duration: 4000, // Durasi animasi dalam milidetik (4 detik)
    //         startCounters() {
    //             document.querySelectorAll(".counter").forEach((el) => {
    //                 const target = parseInt(el.dataset.target, 10) || 0;
    //                 let startTime = null;

    //                 const updateCounter = (timestamp) => {
    //                     if (!startTime) startTime = timestamp;
    //                     const progress = Math.min((timestamp - startTime) / this.duration, 1);
    //                     el.textContent = Math.ceil(progress * target);

    //                     if (progress < 1) {
    //                         requestAnimationFrame(updateCounter);
    //                     }
    //                 };
    //                 requestAnimationFrame(updateCounter);
    //             });
    //         }
    //     }));
    // });

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
