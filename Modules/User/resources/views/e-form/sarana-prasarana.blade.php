<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Form | Perawatan Sarana dan Prasarana</title>
    @vite(['resources/css/app.css', 'resources/js/upload-ktp.js'])
</head>

<body class="eform-bg" style="background: url('{{ asset('img/bgwavy.webp') }}') repeat; background-size: contain;">
    @include('user::layouts.header')
    <section>
        <div class="title-page text-black text-center">
            <h2 class="fw-bold">Formulir Perawatan Sarana dan Prasarana</h2>
        </div>
        <div class="form-wrap">
            <div class="container d-flex justify-content-center align-items-center ">
                <div class="e-form p-4 rounded bg-white shadow-lg" style="max-width: 600px; width: 100%;">
                    <!-- Alert Messages -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-4">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- FORM UTAMA -->
                    <form action="{{ route('form-sarana-prasarana.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group mb-3">
                            <label class="label-form" for="pp_unit_kerja">Unit Kerja <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('pp_unit_kerja') is-invalid @enderror" 
                                id="pp_unit_kerja" name="pp_unit_kerja" 
                                value="{{ old('pp_unit_kerja') }}" required>
                            @error('pp_unit_kerja')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="label-form" for="pp_lokasi_perawatan">Lokasi Perawatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('pp_lokasi_perawatan') is-invalid @enderror" 
                                id="pp_lokasi_perawatan" name="pp_lokasi_perawatan" 
                                value="{{ old('pp_lokasi_perawatan') }}" required>
                            @error('pp_lokasi_perawatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="label-form" for="pp_keluhan_kerusakan">Keluhan/Kerusakan <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('pp_keluhan_kerusakan') is-invalid @enderror" 
                                id="pp_keluhan_kerusakan" name="pp_keluhan_kerusakan" 
                                rows="3" required>{{ old('pp_keluhan_kerusakan') }}</textarea>
                            @error('pp_keluhan_kerusakan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="label-form" for="pp_perawatan_yang_diusulkan">Perawatan yang Diusulkan <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('pp_perawatan_yang_diusulkan') is-invalid @enderror" 
                                id="pp_perawatan_yang_diusulkan" name="pp_perawatan_yang_diusulkan" 
                                rows="3" required>{{ old('pp_perawatan_yang_diusulkan') }}</textarea>
                            @error('pp_perawatan_yang_diusulkan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label class="label-form">Foto Kondisi <span class="text-danger">*</span></label>
                            <div x-data="uploadHandler('pp_foto_kondisi')" class="upload-box">
                                <div class="upload-zone relative border-2 border-dashed border-gray-300 rounded-lg p-6 transition-all hover:border-orange-500 text-center"
                                    @dragover.prevent="dragging = true" 
                                    @dragleave="dragging = false"
                                    @drop.prevent="handleDrop($event)"
                                    :class="{ 'border-orange-500': dragging }">

                                    <template x-if="previewUrl">
                                        <img :src="previewUrl" class="upload-preview"
                                            alt="Preview">
                                    </template>

                                    <div x-show="!previewUrl" class="upload-placeholder">
                                        <i class="fas fa-upload text-4xl text-gray-400 mb-3"></i>
                                        <p class="text-sm text-gray-600">
                                            <strong>Drag and drop <span
                                                    class="text-orange-500 font-semibold">or choose
                                                    file</span> to
                                                upload </strong>
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG up to 5MB</p>
                                        <button type="button" @click="$refs.fileInput.click()"
                                            class="btn upload-btn px-4 py-2 shadow-sm">
                                            Pilih File
                                        </button>
                                    </div>
                                </div>

                                <input type="file" x-ref="fileInput"
                                    name="pp_foto_kondisi"
                                    class="absolute invisible w-0 h-0" accept="image/jpeg,image/jpg,image/png"
                                    @change="handleFileSelect" required>

                                <div class="text-red-500 text-sm mt-2" x-text="errorMessage"></div>
                                @error('pp_foto_kondisi')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Kirim Permohonan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    @include('user::layouts.footer')
</body>

</html>
<script>
    function uploadHandler(inputName) {
        return {
            dragging: false,
            previewUrl: null,
            errorMessage: '',
            
            handleDrop(event) {
                this.dragging = false;
                if (event.dataTransfer.files.length > 0) {
                    this.validateAndPreview(event.dataTransfer.files[0]);
                }
            },
            
            handleFileSelect() {
                const file = this.$refs.fileInput.files[0];
                this.validateAndPreview(file);
            },
            
            validateAndPreview(file) {
                // Reset error
                this.errorMessage = '';
                
                // Validasi tipe file
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    this.errorMessage = 'Format file tidak valid. Gunakan JPG, JPEG, atau PNG.';
                    this.$refs.fileInput.value = '';
                    return;
                }
                
                // Validasi ukuran file (maksimum 5MB)
                const maxSize = 5 * 1024 * 1024; // 5MB
                if (file.size > maxSize) {
                    this.errorMessage = 'Ukuran file terlalu besar. Maksimum 5MB.';
                    this.$refs.fileInput.value = '';
                    return;
                }
                
                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewUrl = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        };
    }

    // Script untuk validasi form
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (field.type === 'file') {
                    if (!field.files || field.files.length === 0) {
                        isValid = false;
                        showError(field, 'File harus diupload');
                    }
                } else if (field.value.trim() === '') {
                    isValid = false;
                    showError(field, 'Field ini wajib diisi');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = form.querySelector('.is-invalid, .invalid-feedback');
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }
        });
        
        function showError(field, message) {
            field.classList.add('is-invalid');
            
            // Cek apakah sudah ada pesan error
            let errorDiv = field.nextElementSibling;
            if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                field.parentNode.insertBefore(errorDiv, field.nextSibling);
            }
            
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }
    });
</script>