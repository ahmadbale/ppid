@extends('sisfo::layouts.template')
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <a href="{{ url('SistemInformasi/EForm/ADM/PengaduanMasyarakat') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
            <h3 class="card-title"><strong> E-Form Pengaduan Masyarakat </strong></h3>
        </div>
        <div class="card-body">
            <form
                action="{{ url('SistemInformasi/EForm/' . Auth::user()->level->level_kode . '/PengaduanMasyarakat/createData') }}"
                method="POST" enctype="multipart/form-data" novalidate>
                @csrf

                <!-- Data Pelapor -->

                <h5 class="text-muted d-block mb-2">Identitas Pelapor</h5>
                <div class="form-group">
                    <label for="pm_nama_tanpa_gelar">Nama Lengkap Pelapor (Tanpa Gelar)<span
                            class="text-danger">*</span></label>
                    <small class="text-muted d-block">Nama lengkap sesuai KTP</small>
                    <input type="text" class="form-control" id="pm_nama_tanpa_gelar"
                        name="t_pengaduan_masyarakat[pm_nama_tanpa_gelar]"
                        value="{{ old('t_pengaduan_masyarakat.pm_nama_tanpa_gelar') }}">
                    <div class="invalid-feedback" id="f_pm_nama_tanpa_gelar_error"></div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="pm_no_hp_pengguna">Nomor HP Pelapor<span class="text-danger">*</span></label>
                        <small class="text-muted d-block">08xxx xxxx xxxx</small>
                        <input type="text" class="form-control" id="pm_no_hp_pengguna"
                            name="t_pengaduan_masyarakat[pm_no_hp_pengguna]"
                            value="{{ old('t_pengaduan_masyarakat.pm_no_hp_pengguna') }}">
                        <div class="invalid-feedback" id="f_pm_no_hp_pengguna_error"></div>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="pm_email_pengguna">Email Pelapor<span class="text-danger">*</span></label>
                        <small class="text-muted d-block">nama_email@gmail.com</small>
                        <input type="email" class="form-control" id="pm_email_pengguna"
                            name="t_pengaduan_masyarakat[pm_email_pengguna]"
                            value="{{ old('t_pengaduan_masyarakat.pm_email_pengguna') }}">
                        <div class="invalid-feedback" id="f_pm_email_pengguna_error"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="pm_upload_nik_pengguna">
                        Upload Foto Kartu Identitas Pelapor <span class="text-danger">*</span>
                    </label>
                    <small class="text-muted d-block mb-2">
                        Silakan scan / foto kartu identitas (KTP/SIM/Paspor) pelapor. Semua data pada kartu identitas harus
                        tampak jelas dan terang.
                    </small>
                    <small class="text-muted d-block mb-1">Maks. 2mb</small>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="pm_upload_nik_pengguna"
                            name="pm_upload_nik_pengguna" accept="image/*">
                        <label class="custom-file-label" for="pm_upload_nik_pengguna">Pilih file (PNG, JPG)</label>
                    </div>
                    <div class="invalid-feedback" id="f_pm_upload_nik_pengguna_error"></div>
                </div>
                <hr>

                <!-- Data Pengaduan -->
                <h5 class="text-muted d-block mb-2">Detail Pengaduan</h5>
                <div class="form-group">
                    <label>Jenis Laporan <span class="text-danger">*</span></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="jenis_laporan_1"
                            name="t_pengaduan_masyarakat[pm_jenis_laporan]" value="Pelanggaran Disiplin Pegawai"
                            {{ old('t_pengaduan_masyarakat.pm_jenis_laporan') == 'Pelanggaran Disiplin Pegawai' ? 'checked' : '' }}
                            required>
                        <label class="form-check-label" for="jenis_laporan_1">Pelanggaran Disiplin Pegawai</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="jenis_laporan_2"
                            name="t_pengaduan_masyarakat[pm_jenis_laporan]"
                            value="Penyalahgunaan Wewenang / Mal Administrasi"
                            {{ old('t_pengaduan_masyarakat.pm_jenis_laporan') == 'Penyalahgunaan Wewenang / Mal Administrasi' ? 'checked' : '' }}
                            required>
                        <label class="form-check-label" for="jenis_laporan_2">Penyalahgunaan Wewenang / Mal
                            Administrasi</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="jenis_laporan_3"
                            name="t_pengaduan_masyarakat[pm_jenis_laporan]"
                            value="Pungutan Liar, Percaloan, dan Pengurusan Dokumen"
                            {{ old('t_pengaduan_masyarakat.pm_jenis_laporan') == 'Pungutan Liar, Percaloan, dan Pengurusan Dokumen' ? 'checked' : '' }}
                            required>
                        <label class="form-check-label" for="jenis_laporan_3">Pungutan Liar, Percaloan, dan Pengurusan
                            Dokumen</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="jenis_laporan_4"
                            name="t_pengaduan_masyarakat[pm_jenis_laporan]"
                            value="Perilaku Amoral (Kekerasan Rumah Tangga / KDRT / Perselingkuhan)"
                            {{ old('t_pengaduan_masyarakat.pm_jenis_laporan') == 'Perilaku Amoral (Kekerasan Rumah Tangga / KDRT / Perselingkuhan)' ? 'checked' : '' }}
                            required>
                        <label class="form-check-label" for="jenis_laporan_4">Perilaku Amoral (Kekerasan Rumah Tangga / KDRT
                            / Perselingkuhan)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="jenis_laporan_5"
                            name="t_pengaduan_masyarakat[pm_jenis_laporan]" value="Pengadaan Barang dan Jasa"
                            {{ old('t_pengaduan_masyarakat.pm_jenis_laporan') == 'Pengadaan Barang dan Jasa' ? 'checked' : '' }}
                            required>
                        <label class="form-check-label" for="jenis_laporan_5">Pengadaan Barang dan Jasa</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="jenis_laporan_6"
                            name="t_pengaduan_masyarakat[pm_jenis_laporan]" value="Narkoba"
                            {{ old('t_pengaduan_masyarakat.pm_jenis_laporan') == 'Narkoba' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="jenis_laporan_6">Narkoba</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="jenis_laporan_7"
                            name="t_pengaduan_masyarakat[pm_jenis_laporan]" value="Pelayanan Publik"
                            {{ old('t_pengaduan_masyarakat.pm_jenis_laporan') == 'Pelayanan Publik' ? 'checked' : '' }}
                            required>
                        <label class="form-check-label" for="jenis_laporan_7">Pelayanan Publik</label>
                    </div>
                    <div class="invalid-feedback" id="f_pm_jenis_laporan_error"></div>
                </div>

                <div class="form-group">
                    <label for="pm_yang_dilaporkan">Pihak/Orang yang Dilaporkan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="pm_yang_dilaporkan"
                        name="t_pengaduan_masyarakat[pm_yang_dilaporkan]"
                        value="{{ old('t_pengaduan_masyarakat.pm_yang_dilaporkan') }}">
                    <div class="invalid-feedback" id="f_pm_yang_dilaporkan_error"></div>
                </div>

                <div class="form-group">
                    <label>Jabatan<span class="text-danger">*</span></label>
                    <div class="form-check">
                        <input class="form-check-input " type="radio" id="jabatan_1"
                            name="t_pengaduan_masyarakat[pm_jabatan]" value="Staff"
                            {{ old('t_pengaduan_masyarakat.pm_jabatan') == 'Staff' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="jabatan_1">Staff</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input " type="radio" id="jabatan_2"
                            name="t_pengaduan_masyarakat[pm_jabatan]" value="Dosen"
                            {{ old('t_pengaduan_masyarakat.pm_jabatan') == 'Dosen' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="jabatan_2">Dosen</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input " type="radio" id="jabatan_3"
                            name="t_pengaduan_masyarakat[pm_jabatan]" value="Tidak tahu"
                            {{ old('t_pengaduan_masyarakat.pm_jabatan') == 'Tidak tahu' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="jabatan_3">Tidak tahu</label>
                    </div>
                    <div class="invalid-feedback" id="f_pm_jabatan_error"></div>
                </div>

                <div class="form-group">
                    <label for="pm_waktu_kejadian">Waktu Kejadian <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control " id="pm_waktu_kejadian"
                        name="t_pengaduan_masyarakat[pm_waktu_kejadian]"
                        value="{{ old('t_pengaduan_masyarakat.pm_waktu_kejadian') }}">
                    <div class="invalid-feedback" id="f_pm_waktu_kejadian_error"></div>
                </div>

                <div class="form-group">
                    <label for="pm_lokasi_kejadian">Lokasi Kejadian <span class="text-danger">*</span></label>
                    <input type="text" class="form-control " id="pm_lokasi_kejadian"
                        name="t_pengaduan_masyarakat[pm_lokasi_kejadian]"
                        value="{{ old('t_pengaduan_masyarakat.pm_lokasi_kejadian') }}">
                    <div class="invalid-feedback" id="f_pm_lokasi_kejadian_error"></div>
                </div>

                <div class="form-group">
                    <label for="pm_kronologis_kejadian">Kronologis Kejadian <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="pm_kronologis_kejadian" name="t_pengaduan_masyarakat[pm_kronologis_kejadian]"
                        required rows="4">{{ old('t_pengaduan_masyarakat.pm_kronologis_kejadian') }}</textarea>
                    <div class="invalid-feedback" id="f_pm_kronologis_kejadian_error"></div>
                </div>

                <div class="form-group">
                    <label for="pm_bukti_pendukung">
                        Upload Bukti Pendukung <span class="text-danger">*</span>
                    </label>
                    <small class="form-text text-muted mb-2">
                        Apabila file lebih dari 2MB, dapat di-zip terlebih dahulu.
                    </small>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="pm_bukti_pendukung"
                            name="pm_bukti_pendukung" accept="file/*">
                        <label class="custom-file-label" for="pm_bukti_pendukung">Pilih file (Maks.2mb)</label>
                    </div>
                    <div class="invalid-feedback" id="f_pm_bukti_pendukung_error"></div>
                </div>


                <div class="form-group">
                    <label for="pm_catatan_tambahan">Catatan Tambahan (opsional)</label>
                    <textarea class="form-control " id="pm_catatan_tambahan" name="t_pengaduan_masyarakat[pm_catatan_tambahan]"
                        rows="4">{{ old('t_pengaduan_masyarakat.pm_catatan_tambahan') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="pm_bukti_aduan">
                        Upload Bukti Aduan <span class="text-danger">*</span>
                    </label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="pm_bukti_aduan" name="pm_bukti_aduan"
                            accept="file/*">
                        <label class="custom-file-label" for="pm_bukti_aduan">Pilih file (Maks.2mb)</label>
                    </div>
                    <div class="invalid-feedback" id="f_pm_bukti_aduan_error"></div>
                </div>

                <div class="alert alert-info mt-3 mb-4">
                    <p class="mb-0"><strong>Catatan:</strong> Dengan mengajukan laporan ini, Anda menyatakan bahwa
                        informasi yang diberikan adalah benar dan Anda bersedia memberikan keterangan lebih lanjut jika
                        diperlukan.</p>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="persetujuan" required>
                        <label class="custom-control-label" for="persetujuan">Saya menyatakan bahwa informasi yang saya
                            berikan adalah benar dan dapat dipertanggungjawabkan</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-success" id="btnSubmit" disabled>Ajukan Pengaduan</button>
            </form>
        </div>
    </div>

    @push('js')
        <script>
            $(function() {
                // Select the form correctly (your form doesn't have an ID, so selecting by tag)
                const form = $('form');
                const button = $('#btnSubmit');
                let isSubmitting = false;

                init();

                function init() {
                    toggleSubmitButton.call($('#persetujuan'));
                    $('#persetujuan').change(toggleSubmitButton);
                    $('.custom-file-input').change(updateFileNameLabel);
                    setupFileInputs();

                    form.off('submit').on('submit', handleFormSubmit);
                }

                function toggleSubmitButton() {
                    button.prop('disabled', !$(this).is(':checked'));
                }

                function updateFileNameLabel() {
                    const fileName = $(this).val().split('\\').pop();
                    $(this).next('.custom-file-label').addClass("selected").html(fileName);
                }

                function setupFileInputs() {
                    const fileInputs = [
                        '#pm_upload_nik_pengguna',
                        '#pm_bukti_pendukung',
                        '#pm_bukti_aduan'
                    ];

                    fileInputs.forEach(selector => {
                        $(selector).change(function() {
                            validateAndUpdateFile($(this));
                        });
                    });
                }

                function validateAndUpdateFile(input) {
                    const file = input[0].files[0];
                    const label = input.next('.custom-file-label');

                    if (file) {
                        const fileSizeMB = file.size / (1024 * 1024);
                        label.text(file.name + ' (' + fileSizeMB.toFixed(2) + ' MB)').addClass('selected');

                        if (fileSizeMB > 2) {
                            Swal.fire({
                                title: 'Peringatan!',
                                text: 'Ukuran file melebihi batas 2MB',
                                icon: 'warning'
                            });
                            input.val('');
                            label.text('Pilih file');
                            return false;
                        }
                    }
                    return true;
                }

                function handleFormSubmit(e) {
                    e.preventDefault();
                    if (isSubmitting) return;

                    // Reset any previous error messages
                    $('.is-invalid').removeClass('is-invalid');
                    $('[id^=f_]').hide();

                    isSubmitting = true;
                    button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);

                    if (!validateForm()) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: 'Mohon periksa kembali input Anda'
                        });
                        isSubmitting = false;
                        button.html('Ajukan Pengaduan').attr('disabled', false);
                        return;
                    }

                    const formData = new FormData(this);

                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message || 'Pengaduan berhasil diajukan'
                                }).then(() => {
                                    window.location.href =
                                        '/SistemInformasi/EForm/ADM/PengaduanMasyarakat';
                                });
                            } else {
                                // Display error message
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message ||
                                        'Terjadi kesalahan saat menyimpan data'
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'Terjadi kesalahan server. Silakan coba lagi.';
                            let response = null;

                            try {
                                response = xhr.responseJSON || JSON.parse(xhr.responseText);
                            } catch (e) {
                                console.error('Gagal parse JSON:', e);
                            }

                            // Jika response ada dan punya pesan error
                            if (response && response.message) {
                                errorMessage = response.message;
                            }

                            // Jika response punya errors (misal nanti ditambahkan di controller), tampilkan
                            if (response && response.errors) {
                                for (const [field, messages] of Object.entries(response.errors)) {
                                    handleFieldError(field, Array.isArray(messages) ? messages[0] :
                                        messages);
                                }
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: errorMessage
                            });
                        },


                        complete: function() {
                            button.html('Ajukan Pengaduan').attr('disabled', false);
                            isSubmitting = false;
                        }
                    });
                }

                // Helper function to handle field errors
                function handleFieldError(field, message) {
                    console.log(`Setting error for field: ${field}, message: ${message}`);

                    // Handle field names with or without t_pengaduan_masyarakat prefix
                    let fieldName = field;
                    if (field.includes('t_pengaduan_masyarakat.')) {
                        fieldName = field.replace('t_pengaduan_masyarakat.', '');
                    }

                    // Special case for file inputs which aren't in the array notation
                    if (fieldName === 'pm_upload_nik_pengguna' || fieldName === 'pm_bukti_pendukung' || fieldName ===
                        'pm_bukti_aduan') {
                        $(`#${fieldName}`).addClass('is-invalid');
                        $(`#f_${fieldName}_error`).text(message).show();
                        return;
                    }

                    // Handle radio buttons
                    if (fieldName === 'pm_jenis_laporan') {
                        $('#f_pm_jenis_laporan_error').text(message).show();
                        return;
                    }

                    if (fieldName === 'pm_jabatan') {
                        $('#f_pm_jabatan_error').text(message).show();
                        return;
                    }

                    // Regular fields
                    $(`#${fieldName}`).addClass('is-invalid');
                    $(`#f_${fieldName}_error`).text(message).show();
                }

                function validateForm() {
                    let isValid = true;

                    // Reset all validation states
                    $('.is-invalid').removeClass('is-invalid');
                    $('[id^=f_]').hide();

                    // Validasi biodata
                    isValid &= validateField('#pm_nama_tanpa_gelar', '#f_pm_nama_tanpa_gelar_error');
                    isValid &= validatePhone('#pm_no_hp_pengguna', '#f_pm_no_hp_pengguna_error');
                    isValid &= validateEmail('#pm_email_pengguna', '#f_pm_email_pengguna_error');
                    isValid &= validateFile('#pm_upload_nik_pengguna', '#f_pm_upload_nik_pengguna_error');

                    // Validasi umum
                    isValid &= validateRadioGroup('input[name="t_pengaduan_masyarakat[pm_jenis_laporan]"]',
                        '#f_pm_jenis_laporan_error', 'Pilih jenis pelaporan');
                    isValid &= validateField('#pm_yang_dilaporkan', '#f_pm_yang_dilaporkan_error');
                    isValid &= validateRadioGroup('input[name="t_pengaduan_masyarakat[pm_jabatan]"]',
                        '#f_pm_jabatan_error', 'Pilih jabatan');
                    isValid &= validateField('#pm_waktu_kejadian', '#f_pm_waktu_kejadian_error');
                    isValid &= validateField('#pm_lokasi_kejadian', '#f_pm_lokasi_kejadian_error');
                    isValid &= validateField('#pm_kronologis_kejadian', '#f_pm_kronologis_kejadian_error');
                    isValid &= validateFile('#pm_bukti_pendukung', '#f_pm_bukti_pendukung_error');
                    isValid &= validateFile('#pm_bukti_aduan', '#f_pm_bukti_aduan_error');

                    return isValid;
                }

                function validateField(fieldSelector, errorSelector) {
                    const value = $(fieldSelector).val();
                    if (!value) {
                        showError(errorSelector, 'Field ini wajib diisi');
                        $(fieldSelector).addClass('is-invalid');
                        return false;
                    } else {
                        hideError(errorSelector);
                        $(fieldSelector).removeClass('is-invalid');
                        return true;
                    }
                }

                function validateEmail(fieldSelector, errorSelector) {
                    const value = $(fieldSelector).val();
                    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!value || !regex.test(value)) {
                        showError(errorSelector, 'Masukkan email yang valid');
                        $(fieldSelector).addClass('is-invalid');
                        return false;
                    } else {
                        hideError(errorSelector);
                        $(fieldSelector).removeClass('is-invalid');
                        return true;
                    }
                }

                function validatePhone(fieldSelector, errorSelector) {
                    const value = $(fieldSelector).val();
                    const regex = /^[0-9]{9,15}$/;
                    if (!value || !regex.test(value)) {
                        showError(errorSelector, 'Masukkan nomor HP yang valid (hanya angka, 9-15 digit)');
                        $(fieldSelector).addClass('is-invalid');
                        return false;
                    } else {
                        hideError(errorSelector);
                        $(fieldSelector).removeClass('is-invalid');
                        return true;
                    }
                }

                function validateFile(fieldSelector, errorSelector) {
                    const value = $(fieldSelector).val();
                    if (!value) {
                        showError(errorSelector, 'File wajib diunggah');
                        $(fieldSelector).addClass('is-invalid');
                        return false;
                    } else {
                        hideError(errorSelector);
                        $(fieldSelector).removeClass('is-invalid');
                        return true;
                    }
                }

                function validateRadioGroup(fieldSelector, errorSelector, errorMessage) {
                    if (!$(fieldSelector + ':checked').length) {
                        showError(errorSelector, errorMessage);
                        return false;
                    } else {
                        hideError(errorSelector);
                        return true;
                    }
                }

                function showError(selector, message) {
                    $(selector).text(message).show();
                }

                function hideError(selector) {
                    $(selector).text('').hide();
                }
            });
        </script>
    @endpush
@endsection
