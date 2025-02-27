import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';

Alpine.plugin(intersect);
window.Alpine = Alpine;

document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
});

document.addEventListener('alpine:init', () => {
    Alpine.data('timeline', () => ({
        steps: [
            { number: "1", text: "Pemohon mengajukan keberatan melalui formulir yang tersedia", position: "right" },
            { number: "2", text: "Petugas PPID Seksi Aduan Masyarakat menerima dan mencatat permohonan keberatan", position: "left" },
            { number: "3", text: "Seksi Aduan Masyarakat meneruskan permohonan keberatan kepada PPID Pusat untuk ditindaklanjuti", position: "right" },
            { number: "4", text: "PPID Pusat menyampaikan permohonan keberatan kepada atasan", position: "left" },
            { number: "5", text: "Atasan PPID pusat menentukan untuk menggugurkan atau menyetujui keputusan PPID", position: "right" },
            { number: "6", text: "Keputusan akhir mengenai keberatan disampaikan secara tertulis kepada pemohon", position: "left" }
        ]
    }));
});
