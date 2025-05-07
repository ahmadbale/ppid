@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $permohonanPerawatanAdminUrl = WebMenuModel::getDynamicMenuUrl('permohonan-sarana-dan-prasarana-admin');
@endphp
@extends('sisfo::layouts.template')
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <a href="{{ url($permohonanPerawatanAdminUrl) }}"
                    class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
            <h3 class="card-title"><strong> E-Form Permohonan Perawatan Sarana Prasarana </strong></h3>
        </div>
        <div class="card-body">

            <form id="permohonanForm"
                action="{{ url($permohonanPerawatanAdminUrl . '/createData') }}"
                method="POST" enctype="multipart/form-data" novalidate>
                @csrf

                <!-- Data Pelapor -->
                <h4 class=" text-muted d-block mb-3">Data Pelapor</h4>

                <div class="form-group">
                    <label for="pp_nama_pengguna">Nama Lengkap Pengusul<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="pp_nama_pengguna"
                        name="t_permohonan_perawatan[pp_nama_pengguna]"
                        value="{{ old('t_permohonan_perawatan.pp_nama_pengguna') }}">
                    <div class="invalid-feedback" id="f_pp_nama_pengguna_error">Nama Lengkap Pengusul tidak boleh kosong.
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="pp_no_hp_pengguna">Nomor HP Pengusul<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="pp_no_hp_pengguna"
                            name="t_permohonan_perawatan[pp_no_hp_pengguna]"
                            value="{{ old('t_permohonan_perawatan.pp_no_hp_pengguna') }}" maxlength="13">
                        <div class="invalid-feedback" id="f_pp_no_hp_pengguna_error">Nomor HP Pengusul tidak boleh kosong.
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="pp_email_pengguna">Email Pengusul<span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="pp_email_pengguna"
                            name="t_permohonan_perawatan[pp_email_pengguna]"
                            value="{{ old('t_permohonan_perawatan.pp_email_pengguna') }}">
                        <div class="invalid-feedback" id="f_pp_email_pengguna_error">Email Pengusul tidak boleh kosong.
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="pp_unit_kerja">Unit Kerja Pengusul <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="pp_unit_kerja"
                        name="t_permohonan_perawatan[pp_unit_kerja]"
                        value="{{ old('t_permohonan_perawatan.pp_unit_kerja') }}">
                    <div class="invalid-feedback" id="f_pp_unit_kerja_error">Unit Kerja Pengusul tidak boleh kosong.</div>
                </div>
                <hr>

                <!-- Data Permohonan Perawatan Sarana Prasarana -->
                <h4 class="text-muted d-block mb-3 mt-4">Detail Permohonan Perawatan Sarana Prasarana</h4>

                <div class="form-group">
                    <label for="pp_perawatan_yang_diusulkan">Perawatan Yang Diusulkan<span
                            class="text-danger">*</span></label>
                    <select class="form-control" id="pp_perawatan_yang_diusulkan"
                        name="t_permohonan_perawatan[pp_perawatan_yang_diusulkan]" required>
                        <option value="">-- Pilih Jenis Perawatan --</option>
                        <option value="Alat Angkutan"
                            {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Alat Angkutan' ? 'selected' : '' }}>
                            Alat Angkutan</option>
                        <option value="Alat Bengkel dan Alat Ukur"
                            {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Alat Bengkel dan Alat Ukur' ? 'selected' : '' }}>
                            Alat Bengkel dan Alat Ukur</option>
                        <option value="Alat Besar"
                            {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Alat Besar' ? 'selected' : '' }}>
                            Alat Besar</option>
                        <option value="Alat Eksploarasi"
                            {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Alat Eksploarasi' ? 'selected' : '' }}>
                            Alat Eksploarasi</option>
                        <option value="Alat Kantor dan Rumah Tangga"
                            {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Alat Kantor dan Rumah Tangga' ? 'selected' : '' }}>
                            Alat Kantor dan Rumah Tangga</option>
                        <option value="Alat Laboratorium"
                            {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Alat Laboratorium' ? 'selected' : '' }}>
                            Alat Laboratorium</option>
                        <option value="Alat Peraga"
                            {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Alat Peraga' ? 'selected' : '' }}>
                            Alat Peraga</option>
                        <option value="Alat Produksi, Pengolahan dan Pemurnian"
                            {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Alat Produksi, Pengolahan dan Pemurnian' ? 'selected' : '' }}>
                            Alat Produksi, Pengolahan dan Pemurnian</option>
                        <option value="Alat Studio, Komunikasi dan Pemancar"
                            {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Alat Studio, Komunikasi dan Pemancar' ? 'selected' : '' }}>
                            Alat Studio, Komunikasi dan Pemancar</option>
                        <option value="Bangunan Air"
                            {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Bangunan Air' ? 'selected' : '' }}>
                            Bangunan Air</option>
                        <option value="Bangunan Gedung"
                            {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Bangunan Gedung' ? 'selected' : '' }}>
                            Bangunan Gedung</option>
                        <option value="Jalan dan Jembatan"
                            {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Jalan dan Jembatan' ? 'selected' : '' }}>
                            Jalan dan Jembatan</option>
                        <option value="Jaringan"
                            {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Jaringan' ? 'selected' : '' }}>
                            Jaringan</option>
                        <option value="Peralatan Proses/Produksi"
                            {{ old('t_permohonan_perawatan.pp_perawatan_yang_diusulkan') == 'Peralatan Proses/Produksi' ? 'selected' : '' }}>
                            Peralatan Proses/Produksi</option>
                    </select>
                    <div class="invalid-feedback" id="f_pp_perawatan_yang_diusulkan_error">Jenis Perawatan harus dipilih.
                    </div>
                </div>

                <div class="form-group">
                    <label for="pp_keluhan_kerusakan">Keluhan Kerusakan<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="pp_keluhan_kerusakan"
                        name="t_permohonan_perawatan[pp_keluhan_kerusakan]" required
                        value="{{ old('t_permohonan_perawatan.pp_keluhan_kerusakan') }}">
                    <div class="invalid-feedback" id="f_pp_keluhan_kerusakan_error">Keluhan Kerusakan tidak boleh kosong.
                    </div>
                </div>

                <div class="form-group">
                    <label for="pp_lokasi_perawatan">Lokasi Perawatan<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="pp_lokasi_perawatan"
                        name="t_permohonan_perawatan[pp_lokasi_perawatan]"
                        value="{{ old('t_permohonan_perawatan.pp_lokasi_perawatan') }}">
                    <div class="invalid-feedback" id="f_pp_lokasi_perawatan_error">Lokasi Perawatan tidak boleh kosong.
                    </div>
                </div>

                <div class="form-group">
                    <label for="pp_foto_kondisi">
                        Upload Foto Kondisi Saat Ini (Opsional)
                    </label>
                    <small class="text-muted d-block mb-2">
                        Maksimal 2MB
                    </small>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="pp_foto_kondisi" name="pp_foto_kondisi"
                            accept="file/*">
                        <label class="custom-file-label" for="pp_foto_kondisi">Pilih foto</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="pi_bukti_aduan">
                        Upload Bukti Aduan <span class="text-danger">*</span>
                    </label>
                    <small class="text-muted d-block mb-2">Maksimal 2MB</small>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="pp_bukti_aduan" name="pp_bukti_aduan"
                            accept="file/*">
                        <label class="custom-file-label" for="pp_bukti_aduan">Pilih file</label>
                    </div>
                    <div class="invalid-feedback" id="f_pp_bukti_aduan_error"></div>
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

                <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>Ajukan Permohonan Perawatan Sarana
                    Prasarana</button>
            </form>
        </div>
    </div>

    @push('js')
    <script>
        let isSubmitting = false;

        $(document).ready(function () {
            setupCheckboxListener();
            setupFileInputValidation();
            setupFormSubmission();
        });

        function setupCheckboxListener() {
            $('#persetujuan').change(function () {
                $('#btnSubmit').prop('disabled', !$(this).is(':checked'));
            });
        }

        function setupFileInputValidation() {
            const fileInputs = ['#pp_bukti_aduan', '#pp_foto_kondisi'];

            fileInputs.forEach(selector => {
                $(selector).change(function () {
                    const label = $(this).siblings('.custom-file-label');
                    validateFile($(this), label);
                });
            });
        }

        function validateFile(input, labelElement) {
            const file = input[0].files[0];

            if (file) {
                const fileSizeMB = file.size / (1024 * 1024);
                labelElement.addClass('selected').text(`${file.name} (${fileSizeMB.toFixed(2)} MB)`);

                if (fileSizeMB > 2) {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: `Ukuran file ${fileSizeMB.toFixed(2)} MB melebihi batas 2MB`,
                        icon: 'warning'
                    });

                    input.val('');
                    labelElement.removeClass('selected').text('Pilih file');
                }
            }
        }

        function setupFormSubmission() {
            const form = $('#permohonanForm');
            const button = $('#btnSubmit');

            // Cegah double binding
            form.off('submit').on('submit', function (e) {
                e.preventDefault();

                if (isSubmitting) return;

                button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);
                isSubmitting = true;

                const isValid = validateForm();

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Mohon periksa kembali input Anda.'
                    });

                    button.html('Submit').attr('disabled', false);
                    isSubmitting = false;
                    return;
                }

                const formData = new FormData(this);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            }).then(() => {
                                window.location.href = "{{ url($permohonanPerawatanAdminUrl) }}";
                            });
                        } else {
                            handleServerErrors(response.errors);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan saat menyimpan data.'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan server. Silakan coba lagi.'
                        });
                    },
                    complete: function () {
                        button.html('Submit').attr('disabled', false);
                        isSubmitting = false;
                    }
                });
            });
        }

        function validateForm() {
            let isValid = validateRequiredFields();
            let isPhoneValid = validatePhoneNumber();
            return isValid && isPhoneValid;
        }

        function validateRequiredFields() {
            let valid = true;
            const requiredFields = [
                '#pp_nama_pengguna',
                '#pp_no_hp_pengguna',
                '#pp_email_pengguna',
                '#pp_unit_kerja',
                '#pp_perawatan_yang_diusulkan',
                '#pp_keluhan_kerusakan',
                '#pp_lokasi_perawatan',
                '#pp_bukti_aduan'
            ];

            requiredFields.forEach(selector => {
                const field = $(selector);
                const value = field.val()?.trim();
                const feedback = field.siblings('.invalid-feedback');

                if (value === '' || value === undefined) {
                    field.addClass('is-invalid');
                    feedback.show();
                    valid = false;
                } else {
                    field.removeClass('is-invalid');
                    feedback.hide();
                }
            });

            return valid;
        }

        function validatePhoneNumber() {
            const phoneInput = $('#pp_no_hp_pengguna');
            const phoneNumber = phoneInput.val().trim();

            if (phoneNumber === '' || phoneNumber.length > 13) {
                phoneInput.addClass('is-invalid');
                phoneInput.siblings('.invalid-feedback').text(
                    phoneNumber === ''
                        ? 'Nomor HP Pengusul tidak boleh kosong.'
                        : 'Nomor HP Pengusul harus terdiri dari maksimal 13 digit.'
                ).show();
                return false;
            } else {
                phoneInput.removeClass('is-invalid');
                phoneInput.siblings('.invalid-feedback').hide();
                return true;
            }
        }

        function handleServerErrors(errors) {
            for (const field in errors) {
                const input = $(`#${field}`);
                const message = errors[field][0];
                input.addClass('is-invalid');
                input.siblings('.invalid-feedback').text(message).show();
            }
        }
    </script>
    @endpush
@endsection
