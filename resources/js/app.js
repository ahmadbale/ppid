import Alpine from 'alpinejs';
import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min';
import 'bootstrap-icons/font/bootstrap-icons.css';

document.addEventListener("alpine:init", () => {
    Alpine.data("passwordToggle", () => ({
        showPassword: false,
        toggle() {
            this.showPassword = !this.showPassword;
        }
    }));

    Alpine.data("fileUpload", () => ({
        fileName: "",
        updateFile(event) {
            this.fileName = event.target.files[0]?.name || "";
        }
    }));
});
