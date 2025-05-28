<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Form | Permohonan Informasi</title>
    @vite(['resources/css/app.css', 'resources/js/upload-ktp.js'])
</head>

<body class="eform-bg" style="background: url('{{ asset('img/bgwavy.webp') }}') repeat; background-size: contain;">
    @include('user::layouts.header')
    <section>
        <div class="title-page text-black text-center">
            <h2 class="fw-bold"> Formulir Permohonan Informasi Publik</h2>
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
                            <div class="step-indicator" :class="step === 1 || step === 2 ? 'active' : ''">1</div>
                            <div class="step-line" :class="step === 2 ? 'active-line' : ''"></div>
                            <div class="step-indicator" :class="step === 2 ? 'active' : ''">2</div>
                        </div>

                        <!-- FORM UTAMA - SATU FORM UNTUK SEMUA STEP -->
                        <form action="{{ route('form-informasi-publik.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- STEP 1: Identitas Pemohon -->
                            <div x-show="step === 1">
                                <div class="form-group mb-3">
                                    <label class="label-form" for="pi_kategori_pemohon">Permohonan Informasi Dilakukan
                                        Atas</label>
                                    <select x-model="kategori" class="form-select" id="pi_kategori_pemohon"
                                        name="t_permohonan_informasi[pi_kategori_pemohon]" required>
                                        <option value="">- Pilih Kategori Pemohon -</option>
                                        @foreach ($kategori as $item)
                                            <option value="{{ $item['nama'] }}">{{ $item['nama'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Form Organisasi -->
                                <div id="formOrganisasi" x-show="kategori === 'Organisasi'" x-cloak>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="label-form">Nama Organisasi <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control"
                                                    name="t_form_pi_organisasi[pi_nama_organisasi]"
                                                    :required="kategori === 'Organisasi'">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="label-form">No Telepon Organisasi <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control"
                                                    name="t_form_pi_organisasi[pi_no_telp_organisasi]"
                                                    :required="kategori === 'Organisasi'">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="label-form">Email dan Website / Media Sosial Organisasi <span
                                                class="text-danger">*</span></label>
                                        <p class="text-muted">
                                            Contoh : <br>
                                            * Email: organisasi@google.com, website: organisasi.co <br>
                                            * Email: organisasi@google.com, IG: organisasi_sosial_masyarakat
                                        </p>
                                        <input type="text" class="form-control"
                                            name="t_form_pi_organisasi[pi_email_atau_medsos_organisasi]"
                                            :required="kategori === 'Organisasi'">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="label-form">Nama Narahubung Organisasi <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control"
                                                    name="t_form_pi_organisasi[pi_nama_narahubung]"
                                                    :required="kategori === 'Organisasi'">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="label-form">No Telepon Narahubung <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control"
                                                    name="t_form_pi_organisasi[pi_no_telp_narahubung]"
                                                    :required="kategori === 'Organisasi'">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="label-form">Upload Foto Kartu Identitas Pemohon <span
                                                class="text-danger">*</span></label>
                                        <br>
                                        <label class="text-muted">Silakan scan / Foto kartu identitas (KTP/SIM/Paspor)
                                            pemohon. Semua data pada kartu identitas harus tampak jelas dan
                                            terang.</label>

                                        <div x-data="uploadHandler('pi_identitas_narahubung')" class="upload-box">
                                            <div class="upload-zone relative border-2 border-dashed border-gray-300 rounded-lg p-6 transition-all hover:border-orange-500 text-center"
                                                @dragover.prevent="dragging = true" @dragleave="dragging = false"
                                                @drop.prevent="handleDrop($event)"
                                                :class="{ 'border-orange-500': dragging }">

                                                <template x-if="previewUrl">
                                                    <img :src="previewUrl" class="upload-preview" alt="Preview">
                                                </template>

                                                <div x-show="!previewUrl" class="upload-placeholder">
                                                    <i class="fas fa-upload text-4xl text-gray-400 mb-3"></i>
                                                    <p class="text-sm text-gray-600">
                                                        <strong>Drag and drop <span
                                                                class="text-orange-500 font-semibold">or choose
                                                                file</span> to upload</strong>
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 2MB</p>
                                                    <button type="button" @click="$refs.fileInput.click()"
                                                        class="btn upload-btn px-4 py-2 shadow-sm">
                                                        Pilih File
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Hidden file input with correct name -->
                                            <input type="file" x-ref="fileInput" name="pi_identitas_narahubung"
                                                class="absolute invisible w-0 h-0" accept="image/*"
                                                @change="handleFileSelect" :required="kategori === 'Organisasi'">

                                            <div class="text-red-500 text-sm mt-2" x-text="errorMessage"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Orang Lain -->
                                <div id="formOrangLain" x-show="kategori === 'Orang Lain'" x-cloak>
                                    <h5 class="mb-3">Data Penginput (Pemohon)</h5>
                                    <div class="form-group mb-3">
                                        <label class="label-form">Nama Penginput <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control"
                                            name="t_form_pi_orang_lain[pi_nama_pengguna_penginput]"
                                            :required="kategori === 'Orang Lain'">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="label-form">Alamat Penginput <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control"
                                            name="t_form_pi_orang_lain[pi_alamat_pengguna_penginput]"
                                            :required="kategori === 'Orang Lain'">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="label-form">No HP Penginput <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control"
                                                    name="t_form_pi_orang_lain[pi_no_hp_pengguna_penginput]"
                                                    :required="kategori === 'Orang Lain'">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="label-form">Email Penginput <span
                                                        class="text-danger">*</span></label>
                                                <input type="email" class="form-control"
                                                    name="t_form_pi_orang_lain[pi_email_pengguna_penginput]"
                                                    :required="kategori === 'Orang Lain'">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="label-form">Upload Foto Kartu Identitas Penginput <span
                                                    class="text-danger">*</span></label>
                                            <br>
                                            <label class="text-muted">Silakan scan / Foto kartu identitas
                                                (KTP/SIM/Paspor) pemohon. Semua data pada kartu identitas harus tampak
                                                jelas dan terang.</label>

                                            <div x-data="uploadHandler('pi_upload_nik_pengguna_penginput')" class="upload-box">
                                                <div class="upload-zone relative border-2 border-dashed border-gray-300 rounded-lg p-6 transition-all hover:border-orange-500 text-center"
                                                    @dragover.prevent="dragging = true" @dragleave="dragging = false"
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
                                                                    file</span> to upload</strong>
                                                        </p>
                                                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 2MB
                                                        </p>
                                                        <button type="button" @click="$refs.fileInput.click()"
                                                            class="btn upload-btn px-4 py-2 shadow-sm">
                                                            Pilih File
                                                        </button>
                                                    </div>
                                                </div>

                                                <input type="file" x-ref="fileInput"
                                                    name="pi_upload_nik_pengguna_penginput"
                                                    class="absolute invisible w-0 h-0" accept="image/*"
                                                    @change="handleFileSelect" :required="kategori === 'Orang Lain'">

                                                <div class="text-red-500 text-sm mt-2" x-text="errorMessage"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <h5 class="mb-3 mt-4">Data Pengguna Informasi</h5>
                                    <div class="form-group mb-3">
                                        <label class="label-form">Nama Pengguna Informasi <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control"
                                            name="t_form_pi_orang_lain[pi_nama_pengguna_informasi]"
                                            :required="kategori === 'Orang Lain'">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="label-form">Alamat Pengguna Informasi <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control"
                                            name="t_form_pi_orang_lain[pi_alamat_pengguna_informasi]"
                                            :required="kategori === 'Orang Lain'">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="label-form">No HP Pengguna Informasi <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control"
                                                    name="t_form_pi_orang_lain[pi_no_hp_pengguna_informasi]"
                                                    :required="kategori === 'Orang Lain'">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="label-form">Email Pengguna Informasi <span
                                                        class="text-danger">*</span></label>
                                                <input type="email" class="form-control"
                                                    name="t_form_pi_orang_lain[pi_email_pengguna_informasi]"
                                                    :required="kategori === 'Orang Lain'">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="label-form">Upload Foto Kartu Identitas Pengguna Informasi <span
                                                class="text-danger">*</span></label>
                                        <br>
                                        <label class="text-muted">Silakan scan / Foto kartu identitas (KTP/SIM/Paspor)
                                            pemohon. Semua data pada kartu identitas harus tampak jelas dan
                                            terang.</label>

                                        <div x-data="uploadHandler('pi_upload_nik_pengguna_informasi')" class="upload-box">
                                            <div class="upload-zone relative border-2 border-dashed border-gray-300 rounded-lg p-6 transition-all hover:border-orange-500 text-center"
                                                @dragover.prevent="dragging = true" @dragleave="dragging = false"
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
                                                                file</span> to upload</strong>
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 2MB</p>
                                                    <button type="button" @click="$refs.fileInput.click()"
                                                        class="btn upload-btn px-4 py-2 shadow-sm">
                                                        Pilih File
                                                    </button>
                                                </div>
                                            </div>

                                            <input type="file" x-ref="fileInput"
                                                name="pi_upload_nik_pengguna_informasi"
                                                class="absolute invisible w-0 h-0" accept="image/*"
                                                @change="handleFileSelect" :required="kategori === 'Orang Lain'">

                                            <div class="text-red-500 text-sm mt-2" x-text="errorMessage"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Diri Sendiri -->
                                <div id="formDiriSendiri" x-show="kategori === 'Diri Sendiri'" x-cloak>
                                    <div class="alert alert-info">
                                        <p class="mb-0">Anda mengajukan permohonan informasi untuk diri sendiri.</p>
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

                            <!-- STEP 2: Data Permohonan -->
                            <div x-show="step === 2" x-cloak>
                                @foreach ($pertanyaanForm as $pertanyaan)
                                    <div class="form-group mb-3">
                                        <label class="label-form">
                                            {{ $pertanyaan['label'] }}
                                            @if (!empty($pertanyaan['required']))
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>

                                        {{-- Jika input type textarea --}}
                                        @if ($pertanyaan['type'] === 'textarea')
                                            <textarea class="form-control" name="t_permohonan_informasi[{{ $pertanyaan['name'] }}]"
                                                @if (!empty($pertanyaan['required'])) required @endif rows="4"></textarea>

                                            {{-- Jika input type text --}}
                                        @elseif ($pertanyaan['type'] === 'text')
                                            <input type="text" class="form-control"
                                                name="t_permohonan_informasi[{{ $pertanyaan['name'] }}]"
                                                @if (!empty($pertanyaan['required'])) required @endif>

                                            {{-- Jika input type checkbox --}}
                                       @elseif ($pertanyaan['type'] === 'radiobutton')
    @foreach ($pertanyaan['options'] as $option)
        <div class="form-check">
            <input class="form-check-input" type="radio"
                name="t_permohonan_informasi[{{ $pertanyaan['name'] }}]"
                id="{{ $pertanyaan['name'] }}_{{ $loop->index }}"
                value="{{ $option }}"
                @if (!empty($pertanyaan['required'])) required @endif>
            <label class="form-check-label" for="{{ $pertanyaan['name'] }}_{{ $loop->index }}">
                {{ $option }}
            </label>
        </div>
    @endforeach

                                            {{-- Jika input type select (dropdown) --}}
                                        @elseif ($pertanyaan['type'] === 'select')
                                            <select class="form-select"
                                                name="t_permohonan_informasi[{{ $pertanyaan['name'] }}]"
                                                @if (!empty($pertanyaan['required'])) required @endif>
                                                <option value="">- Pilih {{ $pertanyaan['label'] }} -</option>
                                                @foreach ($pertanyaan['options'] as $option)
                                                    <option value="{{ $option }}">{{ $option }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                @endforeach

                                <!-- Tombol Back & Submit -->
                                <div class="mt-4 d-flex justify-content-between gap-2">
                                    <button type="button" class="btn btn-secondary w-auto px-4"
                                        @click="step = 1">Kembali</button>
                                    <button type="submit" class="btn btn-success w-auto px-4">Kirim
                                        Permohonan</button>
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
                    } else {
                        // Show file preview for images
                        if (this.files[0].type.startsWith('image/')) {
                            this.showPreview(this.files[0]);
                        }
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
                    'select[name="t_permohonan_informasi[pi_kategori_pemohon]"]');
                FormValidator.showError(select, 'Kategori pemohon harus dipilih');
                isValid = false;
            }

            if (kategori === 'Organisasi') {
                isValid = validateOrganisasiForm() && isValid;
            } else if (kategori === 'Orang Lain') {
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

        // Validate Organisasi form
        function validateOrganisasiForm() {
            let isValid = true;
            const requiredFields = [
                't_form_pi_organisasi[pi_nama_organisasi]',
                't_form_pi_organisasi[pi_no_telp_organisasi]',
                't_form_pi_organisasi[pi_email_atau_medsos_organisasi]',
                't_form_pi_organisasi[pi_nama_narahubung]',
                't_form_pi_organisasi[pi_no_telp_narahubung]'
            ];

            requiredFields.forEach(fieldName => {
                const input = form.querySelector(`[name="${fieldName}"]`);
                if (input && !input.value.trim()) {
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
            const penginputFields = [
                't_form_pi_orang_lain[pi_nama_pengguna_penginput]',
                't_form_pi_orang_lain[pi_alamat_pengguna_penginput]',
                't_form_pi_orang_lain[pi_no_hp_pengguna_penginput]',
                't_form_pi_orang_lain[pi_email_pengguna_penginput]'
            ];

            penginputFields.forEach(fieldName => {
                const input = form.querySelector(`[name="${fieldName}"]`);
                if (input && !input.value.trim()) {
                    FormValidator.showError(input, 'Field ini wajib diisi');
                    isValid = false;
                }
            });

            // Validate pengguna informasi data
            const penggunaFields = [
                't_form_pi_orang_lain[pi_nama_pengguna_informasi]',
                't_form_pi_orang_lain[pi_alamat_pengguna_informasi]',
                't_form_pi_orang_lain[pi_no_hp_pengguna_informasi]',
                't_form_pi_orang_lain[pi_email_pengguna_informasi]'
            ];

            penggunaFields.forEach(fieldName => {
                const input = form.querySelector(`[name="${fieldName}"]`);
                if (input && !input.value.trim()) {
                    FormValidator.showError(input, 'Field ini wajib diisi');
                    isValid = false;
                }
            });

            // Validate required file uploads
            const nikPenggunaFile = form.querySelector('[name="pi_upload_nik_pengguna_informasi"]');
            if (nikPenggunaFile && !nikPenggunaFile.files.length) {
                FormValidator.showError(nikPenggunaFile, 'File NIK pengguna informasi harus diupload');
                isValid = false;
            }

            return isValid;
        }
    });
</script>
