<!-- pengisian form halaman admin -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $pernyataanKeberatanAdminUrl = WebMenuModel::getDynamicMenuUrl('pernyataan-keberatan-admin');
@endphp
@extends('sisfo::layouts.template')
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <a href="{{ url($pernyataanKeberatanAdminUrl) }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
            <h3 class="card-title"><strong> E-Form Pernyataan Keberatan </strong></h3>
        </div>
        <div class="card-body">

            <form id="permohonanForm"  action="{{ url($pernyataanKeberatanAdminUrl . '/createData') }}" method="POST"
                enctype="multipart/form-data" novalidate>
                @csrf
                <div class="form-group">
                    <label for="pk_kategori_pemohon">Pernyataan Keberatan Dilakukan Atas <span class="text-danger">*</span></label>
                    <select class="form-control"
                        id="pk_kategori_pemohon" name="t_pernyataan_keberatan[pk_kategori_pemohon]" required>
                        <option value="">-- Silakan Pilih Kategori Pemohon --</option>
                        <option value="Diri Sendiri" {{ old('t_pernyataan_keberatan.pk_kategori_pemohon') == 'Diri Sendiri' ? 'selected' : '' }}>Diri Sendiri</option>
                        <option value="Orang Lain" {{ old('t_pernyataan_keberatan.pk_kategori_pemohon') == 'Orang Lain' ? 'selected' : '' }}>Orang Lain</option>
                    </select>
                    <div class="invalid-feedback" id="f_pk_kategori_pemohon_error"></div>
                </div>

                <!-- Form untuk Diri Sendiri Bagian Admin -->
                <div id="formDiriSendiri" style="display: none;">
                    <div class="form-group">
                        <label for="pk_nama_pengguna">Nama Pelapor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control"
                            id="pk_nama_pengguna" name="t_form_pk_diri_sendiri[pk_nama_pengguna]"
                            value="{{ old('t_form_pk_diri_sendiri.pk_nama_pengguna') }}">
                            <div class="invalid-feedback" id="f_pk_nama_pengguna_error"></div>
                    </div>
                    <div class="form-group">
                        <label for="pk_alamat_pengguna">Alamat Pelapor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control"
                            id="pk_alamat_pengguna" name="t_form_pk_diri_sendiri[pk_alamat_pengguna]"
                            value="{{ old('t_form_pk_diri_sendiri.pk_alamat_pengguna') }}">
                            <div class="invalid-feedback" id="f_pk_alamat_pengguna_error"></div>
                    </div>
                    <div class="form-group">
                        <label for="pk_pekerjaan_pengguna">Pekerjaan Pelapor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control"
                            id="pk_pekerjaan_pengguna" name="t_form_pk_diri_sendiri[pk_pekerjaan_pengguna]"
                            value="{{ old('t_form_pk_diri_sendiri.pk_pekerjaan_pengguna') }}">
                            <div class="invalid-feedback" id="f_pk_pekerjaan_pengguna_error"></div>
                    </div>
                    <div class="row">
                    <div class="form-group col-md-6">
                        <label for="pk_no_hp_pengguna">No Hp Pelapor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control"
                            id="pk_no_hp_pengguna" name="t_form_pk_diri_sendiri[pk_no_hp_pengguna]"
                            value="{{ old('t_form_pk_diri_sendiri.pk_no_hp_pengguna') }}">
                            <div class="invalid-feedback" id="f_pk_no_hp_pengguna_error"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="pk_email_pengguna">Email Pelapor <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('t_form_pk_diri_sendiri.pk_email_pengguna') is-invalid @enderror"
                            id="pk_email_pengguna" name="t_form_pk_diri_sendiri[pk_email_pengguna]"
                            value="{{ old('t_form_pk_diri_sendiri.pk_email_pengguna') }}">
                        <div class="invalid-feedback" id="f_pk_email_pengguna_error"></div>
                    </div>
                    </div>
                    <div class="form-group">
                        <label for="pk_upload_nik_pengguna">
                            Upload Foto Kartu Identitas Pelapor <span class="text-danger">*</span>
                        </label>
                        <small class="text-muted d-block mb-2">
                            Silakan scan / foto kartu identitas (KTP/SIM/Paspor) pelapor. Semua data pada kartu identitas harus tampak jelas dan terang.
                        </small>
                        <small class="text-muted d-block mb-1">Maks. 2mb</small>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="pk_upload_nik_pengguna" name="pk_upload_nik_pengguna" accept="image/*">
                            <label class="custom-file-label" for="pk_upload_nik_pengguna">Pilih file (PNG, JPG)</label>
                        </div>
                        <div class="invalid-feedback" id="f_pk_upload_nik_pengguna_error"></div>
                    </div>
                </div>

                <!-- Form untuk Orang Lain Bagian Admin -->
                <div id="formOrangLain" style="display: none;">
                    <div class="form-group">
                        <label for="pk_nama_pengguna_penginput">Nama Pelapor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control"
                            id="pk_nama_pengguna_penginput" name="t_form_pk_orang_lain[pk_nama_pengguna_penginput]"
                            value="{{ old('t_form_pk_orang_lain.pk_nama_pengguna_penginput') }}">
                            <div class="invalid-feedback" id="f_pk_nama_pengguna_penginput_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="pk_alamat_pengguna_penginput">Alamat Pelapor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control"
                            id="pk_alamat_pengguna_penginput" name="t_form_pk_orang_lain[pk_alamat_pengguna_penginput]"
                            value="{{ old('t_form_pk_orang_lain.pk_alamat_pengguna_penginput') }}">
                            <div class="invalid-feedback" id="f_pk_alamat_pengguna_penginput_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="pk_pekerjaan_pengguna_penginput">Pekerjaan Pelapor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control"
                            id="pk_pekerjaan_pengguna_penginput" name="t_form_pk_orang_lain[pk_pekerjaan_pengguna_penginput]"
                            value="{{ old('t_form_pk_orang_lain.pk_pekerjaan_pengguna_penginput') }}">
                            <div class="invalid-feedback" id="f_pk_pekerjaan_pengguna_penginput_error"></div>
                    </div>
                    <div class="row">
                    <div class="form-group col-md-6">
                        <label for="pk_no_hp_pengguna_penginput">No Hp Pelapor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control"
                            id="pk_no_hp_pengguna_penginput" name="t_form_pk_orang_lain[pk_no_hp_pengguna_penginput]"
                            value="{{ old('t_form_pk_orang_lain.pk_no_hp_pengguna_penginput') }}">
                            <div class="invalid-feedback" id="f_pk_no_hp_pengguna_penginput_error"></div>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="pk_email_pengguna_penginput">Email Pelapor <span class="text-danger">*</span></label>
                        <input type="email" class="form-control"
                            id="pk_email_pengguna_penginput" name="t_form_pk_orang_lain[pk_email_pengguna_penginput]"
                            value="{{ old('t_form_pk_orang_lain.pk_email_pengguna_penginput') }}">
                            <div class="invalid-feedback" id="f_pk_email_pengguna_penginput_error"></div>
                    </div>
                </div>


                    <div class="form-group">
                        <label for="pk_upload_nik_pengguna_penginput">
                            Upload Foto Kartu Identitas Pelapor <span class="text-danger">*</span>
                        </label>
                        <small class="text-muted d-block mb-2">
                            Silakan scan / foto kartu identitas (KTP/SIM/Paspor) pemohon. Semua data pada kartu identitas harus tampak jelas dan terang.
                        </small>
                        <small class="text-muted d-block mb-1">Maks. 2mb</small>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="pk_upload_nik_pengguna_penginput" name="pk_upload_nik_pengguna_penginput" accept="image/*">
                            <label class="custom-file-label" for="pk_upload_nik_pengguna_penginput">Pilih file (PNG, JPG)</label>
                        </div>
                        <div class="invalid-feedback" id="f_pk_upload_nik_pengguna_penginput_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="pk_nama_kuasa_pemohon">Nama Kuasa Pemohon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control"
                            id="pk_nama_kuasa_pemohon" name="t_form_pk_orang_lain[pk_nama_kuasa_pemohon]"
                            value="{{ old('t_form_pk_orang_lain.pk_nama_kuasa_pemohon') }}">
                            <div class="invalid-feedback" id="f_pk_nama_kuasa_pemohon_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="pk_alamat_kuasa_pemohon">Alamat Kuasa Pemohon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control"
                            id="pk_alamat_kuasa_pemohon" name="t_form_pk_orang_lain[pk_alamat_kuasa_pemohon]"
                            value="{{ old('t_form_pk_orang_lain.pk_alamat_kuasa_pemohon') }}">
                            <div class="invalid-feedback" id="f_pk_alamat_kuasa_pemohon_error"></div>
                    </div>
                    <div class="row">
                    <div class="form-group col-md-6">
                        <label for="pk_no_hp_kuasa_pemohon">No HP Kuasa Pemohon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control"
                            id="pk_no_hp_kuasa_pemohon" name="t_form_pk_orang_lain[pk_no_hp_kuasa_pemohon]"
                            value="{{ old('t_form_pk_orang_lain.pk_no_hp_kuasa_pemohon') }}">
                            <div class="invalid-feedback" id="f_pk_no_hp_kuasa_pemohon_error"></div>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="pk_email_kuasa_pemohon">Email Kuasa Pemohon <span class="text-danger">*</span></label>
                        <input type="email" class="form-control"
                            id="pk_email_kuasa_pemohon" name="t_form_pk_orang_lain[pk_email_kuasa_pemohon]"
                            value="{{ old('t_form_pk_orang_lain.pk_email_kuasa_pemohon') }}">
                            <div class="invalid-feedback" id="f_pk_email_kuasa_pemohon_error"></div>
                    </div>
                </div>

                    <div class="form-group">
                        <label for="pk_upload_nik_kuasa_pemohon">
                            Upload Foto Kartu Identitas Kuasa Pemohon <span class="text-danger">*</span>
                        </label>
                        <small class="text-muted d-block mb-2">
                            Silakan scan / foto kartu identitas (KTP/SIM/Paspor). Semua data pada kartu identitas harus tampak jelas dan terang.
                        </small>
                        <small class="text-muted d-block mb-1">Maks. 2mb</small>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="pk_upload_nik_kuasa_pemohon" name="pk_upload_nik_kuasa_pemohon" accept="image/*">
                            <label class="custom-file-label" for="pk_upload_nik_kuasa_pemohon">Pilih file (PNG, JPG)</label>
                        </div>
                        <div class="invalid-feedback" id="f_pk_upload_nik_kuasa_pemohon_error"></div>
                    </div>
                </div>

                <!-- Form umum untuk semua kategori -->
                <div class="form-group">
                    <label for="pk_alasan_pengajuan_keberatan">Alasan Pengajuan Keberatan <span class="text-danger">*</span></label>
                    <textarea class="form-control"
                        id="pk_alasan_pengajuan_keberatan" name="t_pernyataan_keberatan[pk_alasan_pengajuan_keberatan]"
                        required rows="4">{{ old('t_pernyataan_keberatan.pk_alasan_pengajuan_keberatan') }}</textarea>
                        <div class="invalid-feedback" id="f_pk_alasan_pengajuan_keberatan_error"></div>
                </div>
                <div class="form-group">
                    <label>Sumber Informasi <span class="text-danger">*</span></label>
                    <div class="form-check">
                        <input class="form-check-input"
                            type="radio" id="sumber_1" name="t_pernyataan_keberatan[pk_kasus_posisi]"
                            value="Permohonan Informasi Ditolak"
                            {{ old('t_pernyataan_keberatan.pk_kasus_posisi') == 'Permohonan Informasi Ditolak' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="sumber_1">Permohonan Informasi Ditolak</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input"
                            type="radio" id="sumber_2" name="t_pernyataan_keberatan[pk_kasus_posisi]"
                            value="Form Informasi Berkala Tidak Tersedia"
                            {{ old('t_pernyataan_keberatan.pk_kasus_posisi') == 'Form Informasi Berkala Tidak Tersedia' ? 'checked' : '' }}>
                        <label class="form-check-label" for="sumber_2">Form Informasi Berkala Tidak Tersedia</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input"
                            type="radio" id="sumber_3" name="t_pernyataan_keberatan[pk_kasus_posisi]"
                            value="Permintaan Informasi Tidak Ditanggapi"
                            {{ old('t_pernyataan_keberatan.pk_kasus_posisi') == 'Permintaan Informasi Tidak Ditanggapi' ? 'checked' : '' }}>
                        <label class="form-check-label" for="sumber_3">Permintaan Informasi Tidak Ditanggapi</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input"
                            type="radio" id="sumber_3" name="t_pernyataan_keberatan[pk_kasus_posisi]"
                            value="Permohonan Informasi Ditanggapi Tidak Sebagaimana Yang Diminta"
                            {{ old('t_pernyataan_keberatan.pk_kasus_posisi') == 'Permohonan Informasi Ditanggapi Tidak Sebagaimana Yang Diminta' ? 'checked' : '' }}>
                        <label class="form-check-label" for="sumber_3">Permohonan Informasi Ditanggapi Tidak Sebagaimana Yang Diminta</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input"
                            type="radio" id="sumber_3" name="t_pernyataan_keberatan[pk_kasus_posisi]"
                            value="Permintaan Informasi Tidak Dipenuhi"
                            {{ old('t_pernyataan_keberatan.pk_kasus_posisi') == 'Permintaan Informasi Tidak Dipenuhi' ? 'checked' : '' }}>
                        <label class="form-check-label" for="sumber_3">Permintaan Informasi Tidak Dipenuhi</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input"
                            type="radio" id="sumber_3" name="t_pernyataan_keberatan[pk_kasus_posisi]"
                            value="Biaya yang Dikenakan Tidak Wajar"
                            {{ old('t_pernyataan_keberatan.pk_kasus_posisi') == 'Biaya yang Dikenakan Tidak Wajar' ? 'checked' : '' }}>
                        <label class="form-check-label" for="sumber_3">Biaya yang Dikenakan Tidak Wajar</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input"
                            type="radio" id="sumber_3" name="t_pernyataan_keberatan[pk_kasus_posisi]"
                            value="Informasi yang Disampaikan Melebihi Jangka Waktu yang Ditentukan"
                            {{ old('t_pernyataan_keberatan.pk_kasus_posisi') == 'Informasi yang Disampaikan Melebihi Jangka Waktu yang Ditentukan' ? 'checked' : '' }}>
                        <label class="form-check-label" for="sumber_3">Informasi yang Disampaikan Melebihi Jangka Waktu yang Ditentukan</label>
                    </div>
                    <div class="invalid-feedback" id="f_pk_kasus_posisi_error"></div>
                </div>

                <div class="form-group">
                    <label for="pk_bukti_aduan">
                        Upload Bukti Aduan <span class="text-danger">*</span>
                    </label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="pk_bukti_aduan" name="pk_bukti_aduan" accept="file/*">
                        <label class="custom-file-label" for="pk_bukti_aduan">Pilih file (Maks. 2mb)</label>
                    </div>
                    <div class="invalid-feedback" id="f_pk_bukti_aduan_error"></div>
                </div>

                <div class="alert alert-info mt-3 mb-4">
                    <p class="mb-0"><strong>Catatan:</strong> Dengan mengajukan laporan ini, Anda menyatakan bahwa informasi yang diberikan adalah benar dan Anda bersedia memberikan keterangan lebih lanjut jika diperlukan.</p>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="persetujuan" required>
                        <label class="custom-control-label" for="persetujuan">Saya menyatakan bahwa informasi yang saya berikan adalah benar dan dapat dipertanggungjawabkan</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-success" id="btnSubmit" disabled>Ajukan Pernyataan Keberatan</button>
            </form>
        </div>
    </div>

    @push('js')

    <script>
        $(function() {
            const form = $('#permohonanForm');
            const button = $('#btnSubmit');
            const maxFileSize = 2 * 1024 * 1024;
            let isSubmitting = false; //biar ga doble submit
            init();

            function init() {
                showSavedForm();
                $('#pk_kategori_pemohon').change(handleCategoryChange);
                $('#persetujuan').change(toggleSubmitButton);
                $('.custom-file-input').change(updateFileNameLabel);
                setupFileInputs();
                form.off('submit');
                form.submit(handleFormSubmit);
            }

            function showSavedForm() {
                const savedValue = "{{ old('t_pernyataan_keberatan.pk_kategori_pemohon') }}";
                if (savedValue) showFormBasedOnSelection(savedValue);
            }

            function handleCategoryChange() {
                const selectedValue = $(this).val();
                showFormBasedOnSelection(selectedValue);
            }

            function showFormBasedOnSelection(selectedValue) {
                const forms = ['#formDiriSendiri', '#formOrangLain'];
                $(forms.join(',')).hide().find('input').prop('required', false);

                if (selectedValue === 'Diri Sendiri') {
                    $('#formDiriSendiri').show().find('input:not([type="file"])').prop('required', true);
                    $('#pk_upload_nik_pengguna').prop('required', true);
                } else if (selectedValue === 'Orang Lain') {
                    $('#formOrangLain').show().find('input:not([type="file"])').prop('required', true);
                    $('#pk_upload_nik_pengguna_penginput, #pk_upload_nik_kuasa_pemohon').prop('required', true);
                }
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
                    { input: '#pk_upload_nik_pengguna' },
                    { input: '#pk_upload_nik_pengguna_penginput' },
                    { input: '#pk_upload_nik_kuasa_pemohon' },
                    { input: '#pk_bukti_aduan' }
                ];

                fileInputs.forEach(item => {
                    $(item.input).change(function() {
                        validateAndUpdateFile($(this));
                    });
                });
            }

            function validateAndUpdateFile(input) {
                const file = input[0].files[0];
                if (file) {
                    const fileSizeMB = file.size / (1024 * 1024);

                    if (fileSizeMB > 2) {
                        Swal.fire({
                            title: 'Peringatan!',
                            text: 'Ukuran file ' + fileSizeMB.toFixed(2) + ' MB melebihi batas 2MB',
                            icon: 'warning'
                        });
                        input.val('');
                        input.next('.custom-file-label').removeClass('selected').html('Pilih file');
                        return false;
                    }
                }
                return true;
            }

            function handleFormSubmit(e) {
                e.preventDefault();

                if (isSubmitting) return; // cegah klik submit berulang
                isSubmitting = true;
                button.html('<i class="fas fa-spinner fa-spin"></i> Mengirim...').attr('disabled', true);

                let isValid = validateForm();

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Mohon periksa kembali input Anda'
                    });
                    button.html('Ajukan Pernyataan Keberatan').attr('disabled', false);
                    isSubmitting = false;
                    return; // Hentikan proses submit
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
                                text: response.message
                            }).then(() => {
                                window.location.href = "{{ url($pernyataanKeberatanAdminUrl) }}";
                            });
                        } else {
                            handleServerErrors(response.errors);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan saat mengirim data'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan server. Silakan coba lagi.'
                        });
                    },
                    complete: function() {
                        button.html('Kirim').attr('disabled', false);
                        isSubmitting = false;
                    }
                });
            }

            function validateForm() {
                let isValid = true;
                const kategoriPemohon = $('#pk_kategori_pemohon').val();

                if (!kategoriPemohon) {
                    showError('#f_pk_kategori_pemohon_error', 'Pilih kategori pemohon');
                    isValid = false;
                } else {
                    hideError('#f_pk_kategori_pemohon_error');
                }

                if (kategoriPemohon === 'Diri Sendiri') {
                    isValid &= validateField('#pk_nama_pengguna', '#f_pk_nama_pengguna_error');
                    isValid &= validateField('#pk_alamat_pengguna', '#f_pk_alamat_pengguna_error');
                    isValid &= validateField('#pk_pekerjaan_pengguna', '#f_pk_pekerjaan_pengguna_error');
                    isValid &= validateField('#pk_no_hp_pengguna', '#f_pk_no_hp_pengguna_error');
                    isValid &= validateField('#pk_email_pengguna', '#f_pk_email_pengguna_error');
                    isValid &= validateFile('#pk_upload_nik_pengguna', '#f_pk_upload_nik_pengguna_error');
                } else if (kategoriPemohon === 'Orang Lain') {
                    isValid &= validateField('#pk_nama_pengguna_penginput', '#f_pk_nama_pengguna_penginput_error');
                    isValid &= validateField('#pk_alamat_pengguna_penginput', '#f_pk_alamat_pengguna_penginput_error');
                    isValid &= validateField('#pk_pekerjaan_pengguna_penginput', '#f_pk_pekerjaan_pengguna_penginput_error');
                    isValid &= validateField('#pk_no_hp_pengguna_penginput', '#f_pk_no_hp_pengguna_penginput_error');
                    isValid &= validateField('#pk_email_pengguna_penginput', '#f_pk_email_pengguna_penginput_error');
                    isValid &= validateFile('#pk_upload_nik_pengguna_penginput', '#f_pk_upload_nik_pengguna_penginput_error');

                    isValid &= validateField('#pk_nama_kuasa_pemohon', '#f_pk_nama_kuasa_pemohon_error');
                    isValid &= validateField('#pk_alamat_kuasa_pemohon', '#f_pk_alamat_kuasa_pemohon_error');
                    isValid &= validateField('#pk_no_hp_kuasa_pemohon', '#f_pk_no_hp_kuasa_pemohon_error');
                    isValid &= validateField('#pk_email_kuasa_pemohon', '#f_pk_email_kuasa_pemohon_error');
                    isValid &= validateFile('#pk_upload_nik_kuasa_pemohon', '#f_pk_upload_nik_kuasa_pemohon_error');
                }

                isValid &= validateField('#pk_alasan_pengajuan_keberatan', '#f_pk_alasan_pengajuan_keberatan_error');
                isValid &= validateFile('#pk_bukti_aduan', '#f_pk_bukti_aduan_error');

                if (!$('input[name="t_pernyataan_keberatan[pk_kasus_posisi]"]:checked').length) {
                    showError('#f_pk_kasus_posisi_error', 'Pilih sumber informasi');
                    isValid = false;
                } else {
                    hideError('#f_pk_kasus_posisi_error');
                }

                return !!isValid;
            }

            function validateField(fieldSelector, errorSelector) {
                const value = $(fieldSelector).val();
                if (!value) {
                    showError(errorSelector, 'Field ini wajib diisi');
                    return false;
                } else {
                    hideError(errorSelector);
                    return true;
                }
            }

            function validateFile(fieldSelector, errorSelector) {
                const input = $(fieldSelector)[0];
                const file = input.files[0];
                if (!file) {
                    showError(errorSelector, 'File wajib diunggah');
                    return false;
                }
                if (file.size > maxFileSize) {
                    showError(errorSelector, 'Ukuran file maksimal 2MB');
                    return false;
                }
                hideError(errorSelector);
                return true;
            }

            function showError(selector, message) {
                $(selector).text(message).show();
            }

            function hideError(selector) {
                $(selector).hide();
            }

            function handleServerErrors(errors) {
                if (errors) {
                    $.each(errors, function(key, messages) {
                        const cleanKey = key.replace('t_pernyataan_keberatan.', '');
                        $(`#${cleanKey}`).addClass('is-invalid');
                        $(`#error-${cleanKey}`).html(messages[0]);
                    });
                }
            }
        });
        </script>
    @endpush
@endsection
