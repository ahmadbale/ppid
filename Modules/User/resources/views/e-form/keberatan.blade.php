<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Form | Pernyataan Keberatan</title>
    @vite(['resources/css/app.css', 'resources/js/upload-ktp.js'])
</head>

<body class="eform-bg" style="background: url('{{ asset('img/bgwavy.webp') }}') repeat; background-size: contain;">
    @include('user::layouts.header')
    <section>
        <div class="title-page text-black text-center">
            <h2 class="fw-bold"> Formulir Pernyataan Keberatan</h2>
        </div>
        <div class="form-wrap">
            <div class="container d-flex justify-content-center align-items-center ">
                <div class="e-form p-4 rounded bg-white shadow-lg" style="max-width: 600px; width: 100%;">
                    <div x-data="{ step: 1, kategori: '' }" x-cloak>
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

                        <!-- Stepper Indicator -->
                        <div class="d-flex align-items-center justify-content-center mb-4">
                            <div class="step-indicator" :class="step === 1 ? 'active' : ''">1</div>
                            <div class="step-line" :class="step === 2 ? 'active-line' : ''"></div>
                            <div class="step-indicator" :class="step === 2 ? 'active' : ''">2</div>
                        </div>

                        <!-- FORM UTAMA -->
                        <form action="{{ route('form-pernyataan-keberatan.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- STEP 1: Identitas Pemohon -->
                            <div x-show="step === 1">
                                <div class="form-group mb-3">
                                    <label class="label-form" for="pk_kategori_pemohon">Pengajuan Keberatan
                                        Dilakukan Atas</label>
                                    <select x-model="kategori" class="form-select" id="pk_kategori_pemohon"
                                        name="t_pernyataan_keberatan[pk_kategori_pemohon]" required>
                                        <option value="">- Pilih Kategori Pemohon -</option>
                                        @foreach ($kategori as $item)
                                            <option value="{{ $item['nama'] }}">{{ $item['nama'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Form Orang Lain -->
                                <div id="formOrangLain" x-show="kategori === 'Orang Lain'" x-cloak>
                                    <div class="form-group mb-3">
                                        <label class="label-form">Nama Kuasa Pemohon <span class="text-danger">*</span>
                                        </label>
                                        <br>
                                        <label class="text-muted">Nama lengkap sesuai KTP</label>
                                        <input type="text" class="form-control"
                                            name="t_form_pk_orang_lain[pk_nama_kuasa_pemohon]"
                                            :required="kategori === 'Orang Lain'">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="label-form">Alamat Kuasa Pemohon <span class="text-danger">*</span>
                                        </label>
                                        <br>
                                        <label class="text-muted">Alamat lengkap sesuai KTP</label>
                                        <input type="text" class="form-control"
                                            name="t_form_pk_orang_lain[pk_alamat_kuasa_pemohon]" 
                                            :required="kategori === 'Orang Lain'">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="label-form">No HP / Telepon Kuasa Pemohon <span
                                                        class="text-danger">*</span> </label>
                                                <input type="text" class="form-control"
                                                    name="t_form_pk_orang_lain[pk_no_hp_kuasa_pemohon]"
                                                    :required="kategori === 'Orang Lain'">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="label-form">Email Kuasa Pemohon <span
                                                        class="text-danger">*</span>
                                                </label>
                                                <input type="email" class="form-control"
                                                    name="t_form_pk_orang_lain[pk_email_kuasa_pemohon]"
                                                    :required="kategori === 'Orang Lain'">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="label-form">Upload Foto Kartu Identitas Kuasa Pemohon <span
                                                class="text-danger">*</span> </label>
                                        <br>
                                        <label class="text-muted">Silakan scan / Foto kartu identitas
                                            (KTP/SIM/Paspor)
                                            kuasa pemohon. Semua data pada kartu identitas harus tampak jelas dan
                                            terang.</label>

                                        <div x-data="uploadHandler('pk_upload_nik_kuasa_pemohon')" class="upload-box">
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
                                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 2MB
                                                    </p>
                                                    <button type="button" @click="$refs.fileInput.click()"
                                                        class="btn upload-btn  px-4 py-2 shadow-sm">
                                                        Pilih File
                                                    </button>
                                                </div>
                                            </div>

                                            <input type="file" x-ref="fileInput"
                                                name="pk_upload_nik_kuasa_pemohon"
                                                class="absolute invisible w-0 h-0" accept="image/*"
                                                @change="handleFileSelect" :required="kategori === 'Orang Lain'">

                                            <div class="text-red-500 text-sm mt-2" x-text="errorMessage"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Diri Sendiri -->
                                <div id="formDiriSendiri" x-show="kategori === 'Diri Sendiri'" x-cloak>
                                    <div class="alert alert-info">
                                        <p class="mb-0">Anda mengajukan pernyataan keberatan untuk diri sendiri.</p>
                                    </div>
                                </div>

                                <!-- Tombol Lanjut -->
                                <div>
                                    <button type="button" class="lanjut-btn btn-primary w-100 mt-3"
                                        :disabled="!kategori" @click="step = 2">
                                        Lanjut
                                    </button>
                                </div>
                            </div>

                            <!-- STEP 2: Pertanyaan & Data Keberatan -->
                            <div x-show="step === 2" x-cloak>
                                <div class="form-group mb-3">
                                    <label class="label-form mb-3" for="pk_alasan_pengajuan_keberatan">
                                        Alasan Pengajuan Keberatan <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="pk_alasan_pengajuan_keberatan"
                                        name="t_pernyataan_keberatan[pk_alasan_pengajuan_keberatan]" required>
                                        <option value="">- Pilih Alasan Pengajuan -</option>
                                        @foreach ($pertanyaanForm[0]['options'] as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="label-form">Kasus Posisi <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" name="t_pernyataan_keberatan[pk_kasus_posisi]" required rows="4"></textarea>
                                </div>

                                <!-- Tombol Back & Submit -->
                                <div class="mt-4 d-flex justify-content-between gap-2">
                                    <button type="button" class="btn btn-secondary w-auto px-4"
                                        @click="step = 1">Kembali</button>
                                    <button type="submit" class="btn btn-success w-auto px-4">Kirim Keberatan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('user::layouts.footer')
</body>

</html>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form validation utility functions
        const FormValidator = {
            // Validate file upload
            validateFile: function(fileInput, allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'],
                maxSize = 2 * 1024 * 1024) {
                const file = fileInput.files[0];
                const errors = [];

                if (!file) {
                    return {
                        valid: false,
                        errors: ['File harus dipilih']
                    };
                }

                // Check file type
                if (!allowedTypes.includes(file.type)) {
                    errors.push('File harus berupa gambar (JPG, PNG)');
                }

                // Check file size
                if (file.size > maxSize) {
                    errors.push(`Ukuran file maksimal ${maxSize / (1024 * 1024)}MB`);
                }

                return {
                    valid: errors.length === 0,
                    errors: errors
                };
            },

            // Show error message
            showError: function(input, message) {
                this.clearError(input);

                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = message;

                input.classList.add('is-invalid');
                input.parentNode.appendChild(errorDiv);
            },

            // Clear error message
            clearError: function(input) {
                input.classList.remove('is-invalid');
                const errorMsg = input.parentNode.querySelector('.invalid-feedback');
                if (errorMsg) {
                    errorMsg.remove();
                }
            },

            // Validate email format
            validateEmail: function(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            },

            // Validate phone number (Indonesian format)
            validatePhone: function(phone) {
                const phoneRegex = /^(\+62|62|0)[0-9]{9,13}$/;
                return phoneRegex.test(phone.replace(/[\s-]/g, ''));
            }
        };

        // Real-time validation for file inputs
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', function() {
                FormValidator.clearError(this);

                if (this.files.length > 0) {
                    const validation = FormValidator.validateFile(this);

                    if (!validation.valid) {
                        FormValidator.showError(this, validation.errors.join(', '));
                        this.value = ''; // Clear the invalid file
                    }
                }
            });
        });

        // Real-time validation for email inputs
        const emailInputs = document.querySelectorAll('input[type="email"]');
        emailInputs.forEach(input => {
            input.addEventListener('blur', function() {
                FormValidator.clearError(this);

                if (this.value && !FormValidator.validateEmail(this.value)) {
                    FormValidator.showError(this, 'Format email tidak valid');
                }
            });
        });

        // Real-time validation for phone inputs
        const phoneInputs = document.querySelectorAll('input[name*="no_hp"], input[name*="no_telp"]');
        phoneInputs.forEach(input => {
            input.addEventListener('blur', function() {
                FormValidator.clearError(this);

                if (this.value && !FormValidator.validatePhone(this.value)) {
                    FormValidator.showError(this, 'Format nomor telepon tidak valid');
                }
            });
        });

        // Form submission validation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                const currentStep = document.querySelector('[x-data]').__x.$data.step;
                const kategori = document.querySelector('[x-data]').__x.$data.kategori;

                // Clear all previous errors
                const invalidInputs = form.querySelectorAll('.is-invalid');
                invalidInputs.forEach(input => FormValidator.clearError(input));

                // Validate based on current step and kategori
                if (currentStep === 1) {
                    isValid = validateStep1(kategori) && isValid;
                } else if (currentStep === 2) {
                    isValid = validateStep2() && isValid;
                }

                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first error
                    const firstError = form.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }
            });
        }

        // Step 1 validation
        function validateStep1(kategori) {
            let isValid = true;

            if (!kategori) {
                const select = document.querySelector(
                    'select[name="t_pernyataan_keberatan[pk_kategori_pemohon]"]');
                FormValidator.showError(select, 'Kategori pemohon harus dipilih');
                isValid = false;
            }

            if (kategori === 'Orang Lain') {
                isValid = validateOrangLainForm() && isValid;
            }

            return isValid;
        }

        // Step 2 validation
        function validateStep2() {
            let isValid = true;
            const requiredInputs = form.querySelectorAll('[x-show="step === 2"] [required]');

            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    FormValidator.showError(input, 'Field ini wajib diisi');
                    isValid = false;
                }
            });

            return isValid;
        }

        // Validate Orang Lain form
        function validateOrangLainForm() {
            let isValid = true;

            // Validate penginput data
            const fields = [
                't_form_pk_orang_lain[pk_nama_kuasa_pemohon]',
                't_form_pk_orang_lain[pk_alamat_kuasa_pemohon]',
                't_form_pk_orang_lain[pk_no_hp_kuasa_pemohon]',
                't_form_pk_orang_lain[pk_email_kuasa_pemohon]'
            ];

            fields.forEach(fieldName => {
                const input = form.querySelector(`[name="${fieldName}"]`);
                if (input && !input.value.trim()) {
                    FormValidator.showError(input, 'Field ini wajib diisi');
                    isValid = false;
                }
            });

            // Validate required file uploads
            const nikFile = form.querySelector('[name="pk_upload_nik_kuasa_pemohon"]');
            if (nikFile && !nikFile.files.length) {
                FormValidator.showError(nikFile, 'File NIK kuasa pemohon harus diupload');
                isValid = false;
            }

            return isValid;
        }
    });
</script>