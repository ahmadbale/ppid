import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
});

document.addEventListener('alpine:init', () => {
    Alpine.data('uploadHandler', () => ({
        dragging: false,
        previewUrl: '',
        errorMessage: '',
        uploading: false,
        uploadProgress: 0,

        handleDrop(event) {
            this.dragging = false;
            const files = event.dataTransfer.files;
            console.log("File yang didrop:", files);
            this.handleFiles(files);
        },

        handleFileSelect(event) {
            const files = event.target.files;
            this.handleFiles(files);
        },

        handleFiles(files) {
            if (files.length > 0) {
                const file = files[0];
                if (this.validateFile(file)) {
                    this.previewFile(file);
                    this.uploadFile(file);
                }
            }
        },

        validateFile(file) {
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (!validTypes.includes(file.type)) {
                this.errorMessage = 'Harap unggah file gambar (PNG, JPG, atau GIF)';
                return false;
            }

            if (file.size > maxSize) {
                this.errorMessage = 'Ukuran file maksimal 2MB';
                return false;
            }

            this.errorMessage = ''; // Reset pesan error jika file valid
            return true;
        },

        previewFile(file) {
            const reader = new FileReader();
            reader.onload = () => {
                this.previewUrl = reader.result;
            };
            reader.readAsDataURL(file);
        },

        uploadFile(file) {
            this.uploading = true;
            this.uploadProgress = 0;

            let interval = setInterval(() => {
                this.uploadProgress += 10;
                if (this.uploadProgress >= 100) {
                    clearInterval(interval);
                    setTimeout(() => {
                        this.uploading = false;
                    }, 1000);
                }
            }, 200);
        }
    }));
});

// document.addEventListener('alpine:init', () => {
//     Alpine.data('formHandler', () => ({
//         step: 1,
//         kategori: '',
//         nama: '',
//         alamat: '',
//         ktp: '',

//         nextStep() {
//             if (this.nama && this.alamat && this.ktp && this.kategori) {
//                 this.step = 2;
//             }
//         },

//         isNextDisabled() {
//             return !(this.nama && this.alamat && this.ktp && this.kategori);
//         }
//     }));
// });

