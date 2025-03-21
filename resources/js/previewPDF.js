import './bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min';
import Alpine from 'alpinejs';
window.Alpine = Alpine;
document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
});

document.addEventListener("alpine:init", () => {
    Alpine.data("pdfViewer", () => ({
        pdfUrl: window.pdfUrl, // Ambil URL dari Blade
        pdfDoc: null,
        page: 1,
        totalPages: 1,
        scale: 1.5,

        async loadPdf() {
            if (!this.pdfUrl) {
                console.error("PDF URL tidak ditemukan!");
                return;
            }

            try {
                const loadingTask = pdfjsLib.getDocument(this.pdfUrl);
                this.pdfDoc = await loadingTask.promise;
                this.totalPages = this.pdfDoc.numPages;
                this.renderPage();
            } catch (error) {
                console.error("Gagal memuat PDF:", error);
            }
        },
        
        console.log("PDF URL:", window.pdfUrl);

        async renderPage() {
            const canvas = document.getElementById("pdf-canvas");
            const ctx = canvas.getContext("2d");

            const page = await this.pdfDoc.getPage(this.page);
            const viewport = page.getViewport({ scale: this.scale });

            canvas.width = viewport.width;
            canvas.height = viewport.height;

            const renderContext = { canvasContext: ctx, viewport: viewport };
            await page.render(renderContext);
        },

        nextPage() {
            if (this.page < this.totalPages) {
                this.page++;
                this.renderPage();
            }
        },

        prevPage() {
            if (this.page > 1) {
                this.page--;
                this.renderPage();
            }
        }
    }));
});
