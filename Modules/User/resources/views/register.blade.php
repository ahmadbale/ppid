<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register PPID</title>
    @vite(['resources/css/app.css', 'resources/js/register.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container py-5">
        <div class="text-center mb-4">
            <img src="{{ asset('img/PPIDlogo.svg') }}" alt="PPID Logo" class="logo">
        </div>
        <div class="card-register shadow">
            <h3 class="text-center mb-4">Daftar Akun Baru</h3>
            <form id="form-register" enctype="multipart/form-data">
                @csrf
                <!-- Hidden input untuk default level Responden -->
                <input type="hidden" name="hak_akses_id" value="5">
                
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap (sesuai KTP) *</label>
                    <input type="text" id="nama_pengguna" name="m_user[nama_pengguna]" class="form-control" required>
                    <small id="error-nama_pengguna" class="text-danger d-block"></small>
                </div>
                <div class="mb-3">
                    <label class="form-label">E-mail *</label>
                    <input type="email" id="email_pengguna" name="m_user[email_pengguna]" class="form-control" required>
                    <small id="error-email_pengguna" class="text-danger d-block"></small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nomor HP *</label>
                    <input type="text" id="no_hp_pengguna" name="m_user[no_hp_pengguna]" class="form-control" required>
                    <small id="error-no_hp_pengguna" class="text-danger d-block"></small>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Password *</label>
                        <div class="position-relative">
                            <input type="password" id="password" name="password" class="form-control" required>
                            <i class="fa fa-eye password-toggle" onclick="togglePassword('password')"></i>
                        </div>
                        <small id="error-password" class="text-danger d-block"></small>
                    </div>
                    <div class="col">
                        <label class="form-label">Confirm Password *</label>
                        <div class="position-relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                            <i class="fa fa-eye password-toggle" onclick="togglePassword('confirmPassword')"></i>
                        </div>
                        <small id="error-password_confirmation" class="text-danger d-block"></small>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat *</label>
                    <textarea id="alamat_pengguna" name="m_user[alamat_pengguna]" class="form-control" required></textarea>
                    <small id="error-alamat_pengguna" class="text-danger d-block"></small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pekerjaan *</label>
                    <input type="text" id="pekerjaan_pengguna" name="m_user[pekerjaan_pengguna]" class="form-control" required>
                    <small id="error-pekerjaan_pengguna" class="text-danger d-block"></small>
                </div>
                <div class="mb-3">
                    <label class="form-label">NIK (16 digit) *</label>
                    <input type="text" id="nik_pengguna" name="m_user[nik_pengguna]" class="form-control" required maxlength="16">
                    <small id="error-nik_pengguna" class="text-danger d-block"></small>
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
                                <small id="error-upload_nik_pengguna" class="text-danger d-block"></small>
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
                    $('#error-' + baseName).text('');
                } else if (fieldName) {
                    $('#error-' + fieldName).text('');
                }
            });

            // Form submit AJAX ke backend Sisfo
            $('#form-register').on('submit', function(e) {
                e.preventDefault();
                
                $('.text-danger').text(''); // Clear errors

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
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Registrasi Berhasil!',
                                text: response.message || 'Akun Anda berhasil dibuat',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href = response.redirect || '{{ url("login") }}';
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                if (key.startsWith('m_user.')) {
                                    const fieldName = key.split('.')[1];
                                    $('#error-' + fieldName).text(errors[key][0]);
                                } else {
                                    $('#error-' + key).text(errors[key][0]);
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan saat registrasi',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>