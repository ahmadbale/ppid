import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
});

document.addEventListener('alpine:init', () => {
    Alpine.data('multiUploadHandler', () => ({
        dragging: false,
        fileList: [],
        errorMessage: '',
        uploading: false,
        uploadProgress: 0,
        totalSize: 0,
        maxFileCount: 5,
        maxTotalSize: 100 * 1024 * 1024, // 100MB in bytes

        handleDrop(event) {
            this.dragging = false;
            const files = event.dataTransfer.files;
            this.processFileSelection(files);
        },

        handleFileSelect(event) {
            const files = event.target.files;
            this.processFileSelection(files);
            // Reset the input to allow selecting the same file again
            event.target.value = '';
        },

        processFileSelection(files) {
            if (files.length === 0) return;

            this.errorMessage = '';

            if (this.fileList.length + files.length > this.maxFileCount) {
                this.errorMessage = `Maksimum ${this.maxFileCount} file diperbolehkan. Anda mencoba menambahkan ${files.length} file lagi.`;
                return;
            }

            let newTotalSize = this.totalSize;
            for (let i = 0; i < files.length; i++) {
                newTotalSize += files[i].size;
            }

            if (newTotalSize > this.maxTotalSize) {
                this.errorMessage = `Total ukuran file melebihi batas 100MB. Saat ini: ${this.formatSize(newTotalSize)}`;
                return;
            }

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                // Add file to the list
                this.fileList.push(file);
                this.totalSize += file.size;
            }
        },

        removeFile(index) {
            this.totalSize -= this.fileList[index].size;
            // Remove file from array
            this.fileList.splice(index, 1);
        },

        clearAllFiles() {
            this.fileList = [];
            this.totalSize = 0;
        },

        formatSize(bytes) {
            if (bytes === 0) return '0 Bytes';

            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));

            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        uploadFiles() {
            if (this.fileList.length === 0) {
                this.errorMessage = 'Tidak ada file yang dipilih untuk diupload.';
                return;
            }

            this.uploading = true;
            this.uploadProgress = 0;

            const formData = new FormData();
            this.fileList.forEach((file, index) => {
                formData.append(`file${index}`, file);
            });

            let interval = setInterval(() => {
                this.uploadProgress += 5;
                if (this.uploadProgress >= 100) {
                    clearInterval(interval);
                    setTimeout(() => {
                        this.uploading = false;
                        alert('Semua file berhasil diupload!');
                        this.clearAllFiles();
                    }, 1000);
                }
            }, 200);
        }
    }));
});
