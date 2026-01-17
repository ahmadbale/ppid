<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register PPID</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/register.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .border-danger {
            border-color: #dc3545 !important;
            border-width: 2px !important;
        }
        
        .text-danger {
            color: #dc3545 !important;
            font-weight: 500;
        }
        
        .form-control:focus.border-danger {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        /* Animasi untuk error message */
        span[id^="error-"] {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="text-center mb-4">
            <img src="{{ asset('img/PPIDlogo.svg') }}" alt="PPID Logo" class="logo">
        </div>
        <div class="card-register shadow">
            <h3 class="text-center mb-4">Daftar Akun Baru</h3>
            <form id="form-register" enctype="multipart/form-data" novalidate>
                @csrf
                <!-- Hidden input untuk default level Responden -->
                <input type="hidden" name="hak_akses_id" value="5">
                
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap (sesuai KTP) *</label>
                    <input type="text" id="nama_pengguna" name="m_user[nama_pengguna]" class="form-control">
                    <span id="error-nama_pengguna" class="text-danger d-block mt-1" style="font-size: 0.875rem; display: none;"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label">E-mail *</label>
                    <input type="text" id="email_pengguna" name="m_user[email_pengguna]" class="form-control" placeholder="contoh: nama@email.com">
                    <span id="error-email_pengguna" class="text-danger d-block mt-1" style="font-size: 0.875rem; display: none;"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nomor HP *</label>
                    <input type="text" id="no_hp_pengguna" name="m_user[no_hp_pengguna]" class="form-control">
                    <span id="error-no_hp_pengguna" class="text-danger d-block mt-1" style="font-size: 0.875rem; display: none;"></span>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Password *</label>
                        <div class="position-relative">
                            <input type="password" id="password" name="password" class="form-control">
                            <i class="fa fa-eye password-toggle" onclick="togglePassword('password')"></i>
                        </div>
                        <span id="error-password" class="text-danger d-block mt-1" style="font-size: 0.875rem; display: none;"></span>
                    </div>
                    <div class="col">
                        <label class="form-label">Confirm Password *</label>
                        <div class="position-relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                            <i class="fa fa-eye password-toggle" onclick="togglePassword('confirmPassword')"></i>
                        </div>
                        <span id="error-password_confirmation" class="text-danger d-block mt-1" style="font-size: 0.875rem; display: none;"></span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat *</label>
                    <textarea id="alamat_pengguna" name="m_user[alamat_pengguna]" class="form-control"></textarea>
                    <span id="error-alamat_pengguna" class="text-danger d-block mt-1" style="font-size: 0.875rem; display: none;"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pekerjaan *</label>
                    <input type="text" id="pekerjaan_pengguna" name="m_user[pekerjaan_pengguna]" class="form-control">
                    <span id="error-pekerjaan_pengguna" class="text-danger d-block mt-1" style="font-size: 0.875rem; display: none;"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label">NIK (16 digit) *</label>
                    <input type="text" id="nik_pengguna" name="m_user[nik_pengguna]" class="form-control" maxlength="16">
                    <span id="error-nik_pengguna" class="text-danger d-block mt-1" style="font-size: 0.875rem; display: none;"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload Foto KTP *</label>
                    <div x-data="uploadHandler" class="upload-box">
                        <div class="upload-zone relative border-2 border-dashed border-gray-300 rounded-lg p-6 transition-all hover:border-orange-500 text-center"
                            @dragover.prevent="dragging = true; console.log('Dragging over')"
                            @dragleave="dragging = false; console.log('Dragging left')"
                            @drop.prevent="handleDrop($event); console.log('File dropped')"
                            :class="{ 'border-orange-500': dragging }">



                            <template x-if="previewUrl">
                                <img :src="previewUrl" class="upload-preview" alt="Preview">
                            </template>

                            <div x-show="!previewUrl" class="upload-placeholder">
                                <i class="fas fa-upload text-4xl text-gray-400 mb-3"></i>
                                <p class="text-sm text-gray-600">
                                   <strong> Drag and drop <span class="text-orange-500 font-semibold">or choose file</span> to
                                    upload </strong>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 2MB</p>
                                <div x-data>
                                    <button type="button" @click="$refs.fileInput.click()" class="btn btn-warning px-4 py-2 shadow-sm">
                                        Pilih File
                                    </button>
                                    <input type="file" id="upload_nik_pengguna" name="upload_nik_pengguna" x-ref="fileInput" class="absolute invisible w-0 h-0" accept="image/*" @change="handleFileSelect">
                                </div>


                                <div id="file-error" class="text-red-500 text-sm mt-2" x-text="errorMessage"></div>
                                <span id="error-upload_nik_pengguna" class="text-danger d-block mt-1" style="font-size: 0.875rem; display: none;"></span>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                            <div x-show="uploading" class="upload-progress mt-3">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-orange-500 h-2.5 rounded-full" :style="`width: ${uploadProgress}%`">
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mt-2 text-center">
                                Mengupload... <span x-text="uploadProgress + '%'"></span>
                            </p>
                            </div>
                        </div>
                </div>

                <button type="submit" class="btn btn-warning w-100">DAFTAR</button>
                
                <div class="text-center mt-3">
                    <a href="{{ url('login') }}">Sudah Punya Akun?</a>
                </div>

            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // NIK dan HP hanya angka
            $('#nik_pengguna, #no_hp_pengguna').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Clear error on input
            $('input, select, textarea').on('input change', function() {
                const fieldName = $(this).attr('name');
                if (fieldName && fieldName.includes('[')) {
                    const baseName = fieldName.split('[')[1].replace(']', '');
                    $('#error-' + baseName).text('').hide();
                    // Hapus border merah
                    $('#' + baseName).removeClass('border-danger');
                } else if (fieldName) {
                    $('#error-' + fieldName).text('').hide();
                    // Hapus border merah
                    $('#' + fieldName).removeClass('border-danger');
                }
            });

            // Validasi client-side sebelum submit
            function validateForm() {
                let isValid = true;
                let errorCount = 0;
                
                // Clear all errors first
                $('.text-danger').text('').hide();
                $('input, textarea').removeClass('border-danger');

                // Validasi Nama
                const nama = $('#nama_pengguna').val().trim();
                if (nama.length === 0) {
                    $('#error-nama_pengguna').text('⚠ Nama tidak boleh kosong').show();
                    $('#nama_pengguna').addClass('border-danger');
                    isValid = false;
                    errorCount++;
                } else if (nama.length < 2 || nama.length > 50) {
                    $('#error-nama_pengguna').text('⚠ Nama harus antara 2-50 karakter').show();
                    $('#nama_pengguna').addClass('border-danger');
                    isValid = false;
                    errorCount++;
                }

                // Validasi Email
                const email = $('#email_pengguna').val().trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email.length === 0) {
                    $('#error-email_pengguna').text('⚠ Email tidak boleh kosong').show();
                    $('#email_pengguna').addClass('border-danger');
                    isValid = false;
                    errorCount++;
                } else if (!emailRegex.test(email)) {
                    $('#error-email_pengguna').text('⚠ Format email tidak valid (contoh: nama@email.com)').show();
                    $('#email_pengguna').addClass('border-danger');
                    isValid = false;
                    errorCount++;
                }

                // Validasi Nomor HP
                const hp = $('#no_hp_pengguna').val().trim();
                if (hp.length === 0) {
                    $('#error-no_hp_pengguna').text('⚠ Nomor HP tidak boleh kosong').show();
                    $('#no_hp_pengguna').addClass('border-danger');
                    isValid = false;
                    errorCount++;
                } else if (hp.length < 4 || hp.length > 15) {
                    $('#error-no_hp_pengguna').text('⚠ Nomor HP harus 4-15 digit').show();
                    $('#no_hp_pengguna').addClass('border-danger');
                    isValid = false;
                    errorCount++;
                }

                // Validasi Password
                const password = $('#password').val();
                const passwordConfirm = $('#password_confirmation').val();
                
                if (password.length === 0) {
                    $('#error-password').text('⚠ Password tidak boleh kosong').show();
                    $('#password').addClass('border-danger');
                    isValid = false;
                    errorCount++;
                } else if (password.length < 5) {
                    $('#error-password').text('⚠ Password minimal 5 karakter').show();
                    $('#password').addClass('border-danger');
                    isValid = false;
                    errorCount++;
                }

                if (passwordConfirm.length === 0) {
                    $('#error-password_confirmation').text('⚠ Konfirmasi password tidak boleh kosong').show();
                    $('#password_confirmation').addClass('border-danger');
                    isValid = false;
                    errorCount++;
                } else if (password !== passwordConfirm) {
                    $('#error-password_confirmation').text('⚠ Password tidak cocok').show();
                    $('#password_confirmation').addClass('border-danger');
                    isValid = false;
                    errorCount++;
                }

                // Validasi Alamat
                const alamat = $('#alamat_pengguna').val().trim();
                if (alamat.length === 0) {
                    $('#error-alamat_pengguna').text('⚠ Alamat tidak boleh kosong').show();
                    $('#alamat_pengguna').addClass('border-danger');
                    isValid = false;
                    errorCount++;
                } else if (alamat.length < 5) {
                    $('#error-alamat_pengguna').text('⚠ Alamat minimal 5 karakter').show();
                    $('#alamat_pengguna').addClass('border-danger');
                    isValid = false;
                    errorCount++;
                }

                // Validasi Pekerjaan
                const pekerjaan = $('#pekerjaan_pengguna').val().trim();
                if (pekerjaan.length === 0) {
                    $('#error-pekerjaan_pengguna').text('⚠ Pekerjaan tidak boleh kosong').show();
                    $('#pekerjaan_pengguna').addClass('border-danger');
                    isValid = false;
                    errorCount++;
                } else if (pekerjaan.length < 2) {
                    $('#error-pekerjaan_pengguna').text('⚠ Pekerjaan minimal 2 karakter').show();
                    $('#pekerjaan_pengguna').addClass('border-danger');
                    isValid = false;
                    errorCount++;
                }

                // Validasi NIK
                const nik = $('#nik_pengguna').val().trim();
                if (nik.length === 0) {
                    $('#error-nik_pengguna').text('⚠ NIK tidak boleh kosong').show();
                    $('#nik_pengguna').addClass('border-danger');
                    isValid = false;
                    errorCount++;
                } else if (nik.length !== 16) {
                    $('#error-nik_pengguna').text('⚠ NIK harus 16 digit (saat ini: ' + nik.length + ' digit)').show();
                    $('#nik_pengguna').addClass('border-danger');
                    isValid = false;
                    errorCount++;
                }

                // Validasi Upload File
                const fileInput = $('#upload_nik_pengguna')[0];
                if (!fileInput.files || fileInput.files.length === 0) {
                    $('#error-upload_nik_pengguna').text('⚠ Foto KTP harus diupload').show();
                    isValid = false;
                    errorCount++;
                } else {
                    const file = fileInput.files[0];
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    const maxSize = 2 * 1024 * 1024; // 2MB

                    if (!allowedTypes.includes(file.type)) {
                        $('#error-upload_nik_pengguna').text('⚠ File harus JPG, JPEG, atau PNG').show();
                        isValid = false;
                        errorCount++;
                    } else if (file.size > maxSize) {
                        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                        $('#error-upload_nik_pengguna').text('⚠ Ukuran file ' + fileSizeMB + 'MB (maksimal 2MB)').show();
                        isValid = false;
                        errorCount++;
                    }
                }

                // Scroll ke error pertama jika ada error
                if (!isValid) {
                    const firstError = $('.border-danger:first, #error-upload_nik_pengguna:visible').first();
                    if (firstError.length) {
                        $('html, body').animate({
                            scrollTop: firstError.offset().top - 100
                        }, 500);
                    }
                }

                return isValid;
            }

            // Form submit AJAX ke backend Sisfo
            $('#form-register').on('submit', function(e) {
                e.preventDefault();
                
                // Validasi client-side terlebih dahulu
                if (!validateForm()) {
                    // Tidak perlu SweetAlert, error sudah ditampilkan di masing-masing field
                    return false;
                }

                // Disable button saat proses submit
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');

                const formData = new FormData(this);

                $.ajax({
                    url: '{{ url("register") }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        submitBtn.prop('disabled', false).html(originalText);
                        
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Registrasi Berhasil!',
                                text: response.message || 'Akun Anda berhasil dibuat. Silakan login.',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'Login Sekarang'
                            }).then(() => {
                                window.location.href = response.redirect || '{{ url("login") }}';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan',
                                confirmButtonColor: '#FFC107'
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalText);
                        
                        if (xhr.status === 422) {
                            // Validation errors dari server
                            const errors = xhr.responseJSON.errors;
                            let errorCount = 0;
                            
                            $.each(errors, function(key, value) {
                                errorCount++;
                                // Handle nested m_user fields
                                if (key.includes('m_user.')) {
                                    const fieldName = key.replace('m_user.', '');
                                    $('#error-' + fieldName).text('⚠ ' + value[0]).show();
                                    $('#' + fieldName).addClass('border-danger');
                                } else {
                                    $('#error-' + key).text('⚠ ' + value[0]).show();
                                    $('#' + key).addClass('border-danger');
                                }
                            });

                            // Scroll ke error pertama
                            const firstError = $('.border-danger:first').first();
                            if (firstError.length) {
                                $('html, body').animate({
                                    scrollTop: firstError.offset().top - 100
                                }, 500);
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal',
                                text: 'Terdapat ' + errorCount + ' kesalahan. Mohon periksa form kembali.',
                                confirmButtonColor: '#FFC107'
                            });
                        } else if (xhr.status === 500) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan Server',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan pada server. Silakan coba lagi.',
                                confirmButtonColor: '#dc3545'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan. Silakan coba lagi.',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>