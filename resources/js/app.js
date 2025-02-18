import Alpine from 'alpinejs';
import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min';
import 'bootstrap-icons/font/bootstrap-icons.css';

window.Alpine = Alpine;
Alpine.start();

// show password
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

// File upload functionality
document.addEventListener('DOMContentLoaded', function() {
    const uploadZone = document.querySelector('.upload-zone');
    const fileInput = document.getElementById('ktp-upload');
    const previewImage = docuSment.getElementById('preview-image');
    const uploadPlaceholder = document.querySelector('.upload-placeholder');
    const uploadProgress = document.querySelector('.upload-progress');
    const progressBar = uploadProgress.querySelector('.bg-orange-500');
    const progressText = uploadProgress.querySelector('.progress-text');

    // Handle drag and drop events
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        uploadZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        uploadZone.classList.add('border-orange-500');
    }

    function unhighlight() {
        uploadZone.classList.remove('border-orange-500');
    }

    // Handle file drop
    uploadZone.addEventListener('drop', handleDrop, false);
    fileInput.addEventListener('change', handleFileSelect, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    function handleFileSelect(e) {
        const files = e.target.files;
        handleFiles(files);
    }

    function handleFiles(files) {
        if (files.length > 0) {
            const file = files[0];
            if (validateFile(file)) {
                previewFile(file);
                uploadFile(file);
            }
        }
    }

    function validateFile(file) {
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (!validTypes.includes(file.type)) {
            alert('Please upload an image file (PNG, JPG, or GIF)');
            return false;
        }

        if (file.size > maxSize) {
            alert('File size must be less than 10MB');
            return false;
        }
        return true;
    }

    function previewFile(file) {
        const reader = new FileReader();
        reader.onloadend = function() {
            previewImage.src = reader.result;
            previewImage.classList.remove('hidden');
            uploadPlaceholder.classList.add('hidden');
        }
        reader.readAsDataURL(file);
    }

    function uploadFile(file) {
        // Show progress bar
        uploadProgress.classList.remove('hidden');
        
        // Simulate upload progress 
        let progress = 0;
        const interval = setInterval(() => {
            progress += 5;
            progressBar.style.width = `${progress}%`;
            progressText.textContent = `${progress}%`;

            if (progress >= 100) {
                clearInterval(interval);
                setTimeout(() => {
                    uploadProgress.classList.add('hidden');
                    uploadPlaceholder.classList.remove('hidden');
                }, 1000);
            }
        }, 100);
    }

    window.togglePassword = function(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.nextElementSibling.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
});


document.getElementById("upload-btn").addEventListener("click", function() {
    document.getElementById("ktp-upload").click();
});


document.getElementById("ktp-upload").addEventListener("change", function(event) {
    const fileName = event.target.files[0] ? event.target.files[0].name : "Tidak ada file yang dipilih";
    const fileNameElement = document.getElementById("file-name");
    
    fileNameElement.textContent = fileName;
    fileNameElement.classList.remove("d-none");
});

// stats
    document.addEventListener("alpine:init", () => {
        Alpine.data("statistikCounter", () => ({
            duration: 4000, // Durasi animasi dalam milidetik (4 detik)
            startCounters() {
                document.querySelectorAll(".counter").forEach((el) => {
                    const target = parseInt(el.dataset.target, 10) || 0;
                    let startTime = null;
                    
                    const updateCounter = (timestamp) => {
                        if (!startTime) startTime = timestamp;
                        const progress = Math.min((timestamp - startTime) / this.duration, 1);
                        el.textContent = Math.ceil(progress * target);
                        
                        if (progress < 1) {
                            requestAnimationFrame(updateCounter);
                        }
                    };
                    requestAnimationFrame(updateCounter);
                });
            }
        }));
    });
}); 
