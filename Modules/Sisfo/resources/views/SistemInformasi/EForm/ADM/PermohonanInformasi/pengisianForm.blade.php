<!-- pengisian form halaman admin -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $permohonanInformasiAdminUrl = WebMenuModel::getDynamicMenuUrl('permohonan-informasi-admin');
@endphp
@extends('sisfo::layouts.template')
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <a href="{{ url($permohonanInformasiAdminUrl) }}"
                    class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
            <h3 class="card-title"><strong> E-Form Permohonan Informasi </strong></h3>
        </div>
        <div class="card-body">

            <form id="permohonanForm"
                action="{{ url($permohonanInformasiAdminUrl . '/createData') }}"
                method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="form-group">
                    <label for="pi_kategori_pemohon">Permohonan Informasi Dilakukan Atas <span
                            class="text-danger">*</span></label>
                    <select class="form-control" id="pi_kategori_pemohon" name="t_permohonan_informasi[pi_kategori_pemohon]"
                        required>
                        <option value="">-- Silakan Pilih Kategori Pemohon --</option>
                        <option value="Diri Sendiri"
                            {{ old('t_permohonan_informasi.pi_kategori_pemohon') == 'Diri Sendiri' ? 'selected' : '' }}>Diri
                            Sendiri</option>
                        <option value="Orang Lain"
                            {{ old('t_permohonan_informasi.pi_kategori_pemohon') == 'Orang Lain' ? 'selected' : '' }}>Orang
                            Lain</option>
                        <option value="Organisasi"
                            {{ old('t_permohonan_informasi.pi_kategori_pemohon') == 'Organisasi' ? 'selected' : '' }}>
                            Organisasi</option>
                    </select>
                    <div class="invalid-feedback" id="f_pi_kategori_pemohon_error"></div>
                </div>

                <!-- Form untuk Diri Sendiri Bagian Admin -->
                <div id="formDiriSendiri" style="display: none;">
                    <hr><label class="text-muted d-block mb-2">Identitas Pelapor</label>
                    <div class="form-group">
                        <label for="pi_nama_pengguna">Nama Pelapor <span class="text-danger">*</span></label>
                        <small class="text-muted d-block">Nama lengkap sesuai KTP</small>
                        <input type="text" class="form-control" id="pi_nama_pengguna"
                            name="t_form_pi_diri_sendiri[pi_nama_pengguna]"
                            value="{{ old('t_form_pi_diri_sendiri.pi_nama_pengguna') }}">
                        <div class="invalid-feedback" id="f_pi_nama_pengguna_error"></div>
                    </div>
                    <div class="form-group">
                        <label for="pi_alamat_pengguna">Alamat Pelapor <span class="text-danger">*</span></label>
                        <small class="text-muted d-block">Alamat lengkap sesuai KTP</small>
                        <input type="text" class="form-control" id="pi_alamat_pengguna"
                            name="t_form_pi_diri_sendiri[pi_alamat_pengguna]"
                            value="{{ old('t_form_pi_diri_sendiri.pi_alamat_pengguna') }}">
                        <div class="invalid-feedback" id="f_pi_alamat_pengguna_error"></div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="pi_no_hp_pengguna">No Hp Pelapor <span class="text-danger">*</span></label>
                            <small class="text-muted d-block">08xxx xxxx xxxx</small>
                            <input type="text" class="form-control" id="pi_no_hp_pengguna"
                                name="t_form_pi_diri_sendiri[pi_no_hp_pengguna]"
                                value="{{ old('t_form_pi_diri_sendiri.pi_no_hp_pengguna') }}">
                            <div class="invalid-feedback" id="f_pi_no_hp_pengguna_error"></div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="pi_email_pengguna">Email Pelapor <span class="text-danger">*</span></label>
                            <small class="text-muted d-block">nama_email@gmail.com</small>
                            <input type="email" class="form-control" id="pi_email_pengguna"
                                name="t_form_pi_diri_sendiri[pi_email_pengguna]"
                                value="{{ old('t_form_pi_diri_sendiri.pi_email_pengguna') }}">
                            <div class="invalid-feedback" id="f_pi_email_pengguna_error"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pi_upload_nik_pengguna">
                            Upload Foto Kartu Identitas Pelapor <span class="text-danger">*</span>
                        </label>
                        <small class="text-muted d-block mb-2">
                            Silakan scan / foto kartu identitas (KTP/SIM/Paspor) pelapor. Semua data pada kartu identitas
                            harus tampak jelas dan terang.
                        </small>
                        <small class="text-muted d-block mb-1">Maks. 2mb</small>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="pi_upload_nik_pengguna"
                                name="pi_upload_nik_pengguna" accept="image/*">
                            <label class="custom-file-label" for="pi_upload_nik_pengguna">Pilih file (PNG, JPG)</label>
                        </div>
                        <div class="invalid-feedback" id="f_pi_upload_nik_pengguna_error"></div>
                    </div>
                    <hr>
                </div>

                <!-- Form untuk Orang Lain Bagian Admin -->
                <div id="formOrangLain" style="display: none;">
                    <div class="form-group">
                        <label for="pi_nama_pengguna_penginput">Nama Pelapor <span class="text-danger">*</span></label>
                        <small class="text-muted d-block">Nama lengkap sesuai KTP</small>
                        <input type="text" class="form-control" id="pi_nama_pengguna_penginput"
                            name="t_form_pi_orang_lain[pi_nama_pengguna_penginput]"
                            value="{{ old('t_form_pi_orang_lain.pi_nama_pengguna_penginput') }}">
                        <div class="invalid-feedback" id="f_pi_nama_pengguna_penginput_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="pi_alamat_pengguna_penginput">Alamat Pelapor <span
                                class="text-danger">*</span></label>
                        <small class="text-muted d-block">Alamat lengkap sesuai KTP</small>
                        <input type="text" class="form-control" id="pi_alamat_pengguna_penginput"
                            name="t_form_pi_orang_lain[pi_alamat_pengguna_penginput]"
                            value="{{ old('t_form_pi_orang_lain.pi_alamat_pengguna_penginput') }}">
                        <div class="invalid-feedback" id="f_pi_alamat_pengguna_penginput_error"></div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="pi_no_hp_pengguna_penginput">No Hp Pelapor <span
                                    class="text-danger">*</span></label>
                            <small class="text-muted d-block">08xxx xxxx xxxx</small>
                            <input type="text" class="form-control" id="pi_no_hp_pengguna_penginput"
                                name="t_form_pi_orang_lain[pi_no_hp_pengguna_penginput]"
                                value="{{ old('t_form_pi_orang_lain.pi_no_hp_pengguna_penginput') }}">
                            <div class="invalid-feedback" id="f_pi_no_hp_pengguna_penginput_error"></div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="pi_email_pengguna_penginput">Email Pelapor <span
                                    class="text-danger">*</span></label>
                            <small class="text-muted d-block">nama_email@gmail.com</small>
                            <input type="email" class="form-control" id="pi_email_pengguna_penginput"
                                name="t_form_pi_orang_lain[pi_email_pengguna_penginput]"
                                value="{{ old('t_form_pi_orang_lain.pi_email_pengguna_penginput') }}">
                            <div class="invalid-feedback" id="f_pi_email_pengguna_penginput_error"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pi_upload_nik_pengguna_penginput">
                            Upload Foto Kartu Identitas Pelapor <span class="text-danger">*</span>
                        </label>
                        <small class="text-muted d-block mb-2">
                            Silakan scan / foto kartu identitas (KTP/SIM/Paspor) pemohon. Semua data pada kartu identitas
                            harus tampak jelas dan terang.
                        </small>
                        <small class="text-muted d-block mb-1">Maks. 2mb</small>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="pi_upload_nik_pengguna_penginput"
                                name="pi_upload_nik_pengguna_penginput" accept="image/*">
                            <label class="custom-file-label" for="pi_upload_nik_pengguna_penginput">Pilih file (PNG, JPG)</label>
                        </div>
                        <div class="invalid-feedback" id="f_pi_upload_nik_pengguna_penginput_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="pi_nama_pengguna_informasi">Nama Pemohon<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="pi_nama_pengguna_informasi"
                            name="t_form_pi_orang_lain[pi_nama_pengguna_informasi]"
                            value="{{ old('t_form_pi_orang_lain.pi_nama_pengguna_informasi') }}">
                        <div class="invalid-feedback" id="f_pi_nama_pengguna_informasi_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="pi_alamat_pengguna_informasi">Alamat Pemohon <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="pi_alamat_pengguna_informasi"
                            name="t_form_pi_orang_lain[pi_alamat_pengguna_informasi]"
                            value="{{ old('t_form_pi_orang_lain.pi_alamat_pengguna_informasi') }}">
                        <div class="invalid-feedback" id="f_pi_alamat_pengguna_informasi_error"></div>
                    </div>
                    <div class="form-group">
                        <label for="pi_no_hp_pengguna_informasi">No HP Pemohon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="pi_no_hp_pengguna_informasi"
                            name="t_form_pi_orang_lain[pi_no_hp_pengguna_informasi]"
                            value="{{ old('t_form_pi_orang_lain.pi_no_hp_pengguna_informasi') }}">
                        <div class="invalid-feedback" id="f_pi_no_hp_pengguna_informasi_error"></div>
                    </div>
                    <div class="form-group">
                        <label for="pi_email_pengguna_informasi">Email Pemohon <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="pi_email_pengguna_informasi"
                            name="t_form_pi_orang_lain[pi_email_pengguna_informasi]"
                            value="{{ old('t_form_pi_orang_lain.pi_email_pengguna_informasi') }}">
                        <div class="invalid-feedback" id="f_pi_email_pengguna_informasi_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="pi_upload_nik_pengguna_informasi">
                            Upload Foto Kartu Identitas Pemohon <span class="text-danger">*</span>
                        </label>
                        <small class="text-muted d-block mb-2">
                            Silakan scan / foto kartu identitas (KTP/SIM/Paspor) pemohon. Semua data pada kartu identitas
                            harus tampak jelas dan terang.
                        </small>
                        <small class="text-muted d-block mb-1">Maks. 2mb</small>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="pi_upload_nik_pengguna_informasi"
                                name="pi_upload_nik_pengguna_informasi" accept="image/*">
                            <label class="custom-file-label" for="pi_upload_nik_pengguna_informasi">Pilih file (PNG,
                                JPG)</label>
                        </div>
                        <div class="invalid-feedback" id="f_pi_upload_nik_pengguna_informasi_error"></div>
                    </div>
                </div>

                <!-- Form untuk Organisasi Bagian Admin -->
                <div id="formOrganisasi" style="display: none;">
                    <div class="form-group">
                        <label for="pi_nama_organisasi">Nama Organisasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="pi_nama_organisasi"
                            name="t_form_pi_organisasi[pi_nama_organisasi]"
                            value="{{ old('t_form_pi_organisasi.pi_nama_organisasi') }}">
                        <div class="invalid-feedback" id="f_pi_nama_organisasi_error"></div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="pi_no_telp_organisasi">No Telepon Organisasi <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="pi_no_telp_organisasi"
                                name="t_form_pi_organisasi[pi_no_telp_organisasi]"
                                value="{{ old('t_form_pi_organisasi.pi_no_telp_organisasi') }}">
                            <div class="invalid-feedback" id="f_pi_no_telp_organisasi_error"></div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="pi_email_atau_medsos_organisasi">Email/Media Sosial Organisasi <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="pi_email_atau_medsos_organisasi"
                                name="t_form_pi_organisasi[pi_email_atau_medsos_organisasi]"
                                value="{{ old('t_form_pi_organisasi.pi_email_atau_medsos_organisasi') }}">
                            <div class="invalid-feedback" id="f_pi_email_atau_medsos_organisasi_error"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pi_nama_narahubung">Nama Narahubung <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="pi_nama_narahubung"
                            name="t_form_pi_organisasi[pi_nama_narahubung]"
                            value="{{ old('t_form_pi_organisasi.pi_nama_narahubung') }}">
                        <div class="invalid-feedback" id="f_pi_nama_narahubung_error"></div>
                    </div>
                    <div class="form-group">
                        <label for="pi_no_telp_narahubung">No Telepon Narahubung <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="pi_no_telp_narahubung"
                            name="t_form_pi_organisasi[pi_no_telp_narahubung]"
                            value="{{ old('t_form_pi_organisasi.pi_no_telp_narahubung') }}">
                        <div class="invalid-feedback" id="f_pi_no_telp_narahubung_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="pi_identitas_narahubung">
                            Upload Foto Kartu Identitas Narahubung <span class="text-danger">*</span>
                        </label>
                        <small class="text-muted d-block mb-2">
                            Silakan scan / foto kartu identitas (KTP/SIM/Paspor). Semua data pada kartu identitas harus
                            tampak jelas dan terang.
                        </small>
                        <small class="text-muted d-block mb-1">Maks. 2mb</small>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="pi_identitas_narahubung"
                                name="pi_identitas_narahubung" accept="image/*">
                            <label class="custom-file-label" for="pi_identitas_narahubung">Pilih file (PNG, JPG)</label>
                        </div>
                        <div class="invalid-feedback" id="f_pi_identitas_narahubung_error"></div>
                    </div>
                </div>

                <!-- Form umum untuk semua kategori -->
                <div class="form-group">
                    <label for="pi_informasi_yang_dibutuhkan">Informasi yang Dibutuhkan <span
                            class="text-danger">*</span></label>
                    <textarea class="form-control" id="pi_informasi_yang_dibutuhkan"
                        name="t_permohonan_informasi[pi_informasi_yang_dibutuhkan]" required rows="4">{{ old('t_permohonan_informasi.pi_informasi_yang_dibutuhkan') }}</textarea>
                    <div class="invalid-feedback" id="f_pi_informasi_yang_dibutuhkan_error"></div>
                </div>
                <div class="form-group">
                    <label for="pi_alasan_permohonan_informasi">Alasan Permohonan Informasi <span
                            class="text-danger">*</span></label>
                    <textarea class="form-control" id="pi_alasan_permohonan_informasi"
                        name="t_permohonan_informasi[pi_alasan_permohonan_informasi]" required rows="4">{{ old('t_permohonan_informasi.pi_alasan_permohonan_informasi') }}</textarea>
                    <div class="invalid-feedback" id="f_pi_alasan_permohonan_informasi_error"></div>
                </div>
                <div class="form-group">
                    <label>Sumber Informasi <span class="text-danger">*</span></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="sumber_1"
                            name="t_permohonan_informasi[pi_sumber_informasi]" value="Pertanyaan Langsung Pemohon"
                            {{ old('t_permohonan_informasi.pi_sumber_informasi') == 'Pertanyaan Langsung Pemohon' ? 'checked' : '' }}
                            required>
                        <label class="form-check-label" for="sumber_1">Pertanyaan Langsung Pemohon</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="sumber_2"
                            name="t_permohonan_informasi[pi_sumber_informasi]"
                            value="Website / Media Sosial Milik Polinema"
                            {{ old('t_permohonan_informasi.pi_sumber_informasi') == 'Website / Media Sosial Milik Polinema' ? 'checked' : '' }}>
                        <label class="form-check-label" for="sumber_2">Website / Media Sosial Milik Polinema</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="sumber_3"
                            name="t_permohonan_informasi[pi_sumber_informasi]"
                            value="Website / Media Sosial Bukan Milik Polinema"
                            {{ old('t_permohonan_informasi.pi_sumber_informasi') == 'Website / Media Sosial Bukan Milik Polinema' ? 'checked' : '' }}>
                        <label class="form-check-label" for="sumber_3">Website / Media Sosial Bukan Milik Polinema</label>
                    </div>
                    <div class="invalid-feedback" id="f_pi_sumber_informasi_error"></div>
                </div>
                <div class="form-group">
                    <label for="pi_alamat_sumber_informasi">Alamat Sumber Informasi <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="pi_alamat_sumber_informasi"
                        name="t_permohonan_informasi[pi_alamat_sumber_informasi]"
                        value="{{ old('t_permohonan_informasi.pi_alamat_sumber_informasi') }}" required>
                    <div class="invalid-feedback" id="f_pi_alamat_sumber_informasi_error"></div>
                </div>

                <div class="form-group">
                    <label for="pi_bukti_aduan">
                        Upload Bukti Aduan <span class="text-danger">*</span>
                    </label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="pi_bukti_aduan" name="pi_bukti_aduan"
                            accept="application/pdf,image/*">
                        <label class="custom-file-label" for="pi_bukti_aduan">Pilih file (Maks.2mb)</label>
                    </div>
                    <div class="invalid-feedback" id="f_pi_bukti_aduan_error"></div>
                </div>

                <div class="alert alert-info mt-3 mb-4">
                    <p class="mb-0">
                        <strong>Catatan:</strong> Dengan mengajukan laporan ini, Anda menyatakan bahwa
                        informasi yang diberikan adalah benar dan Anda bersedia memberikan keterangan
                        lebih lanjut jika diperlukan.
                    </p>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="persetujuan" required>
                        <label class="custom-control-label" for="persetujuan">Saya menyatakan bahwa informasi yang saya
                            berikan adalah benar dan dapat dipertanggungjawabkan</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-success" id="btnSubmit" disabled>Ajukan Permohonan
                    Informasi</button>
            </form>
        </div>
    </div>

    @push('js')
    <script>
        $(function() {
            const form = $('#permohonanForm');
            const button = $('#btnSubmit');
            let isSubmitting = false;

            init();

            function init() {
                showSavedForm();
                $('#pi_kategori_pemohon').change(handleCategoryChange);
                $('#persetujuan').change(toggleSubmitButton);
                $('.custom-file-input').change(updateFileNameLabel);
                setupFileInputs();

                form.off('submit');
                form.off('submit').on('submit', handleFormSubmit);
            }

            function showSavedForm() {
                const savedValue = "{{ old('t_permohonan_informasi.pi_kategori_pemohon') }}";
                if (savedValue) showFormBasedOnSelection(savedValue);
            }

            function handleCategoryChange() {
                const selectedValue = $(this).val();
                showFormBasedOnSelection(selectedValue);
            }

            function showFormBasedOnSelection(selectedValue) {
                const forms = ['#formDiriSendiri', '#formOrangLain', '#formOrganisasi'];
                $(forms.join(',')).hide().find('input').prop('required', false);

                if (selectedValue === 'Diri Sendiri') {
                    $('#formDiriSendiri').show().find('input:not([type="file"])').prop('required', true);
                    $('#pi_upload_nik_pengguna').prop('required', true);
                } else if (selectedValue === 'Orang Lain') {
                    $('#formOrangLain').show().find('input:not([type="file"])').prop('required', true);
                    $('#pi_upload_nik_pengguna_penginput, #pi_upload_nik_pengguna_informasi').prop('required', true);
                } else if (selectedValue === 'Organisasi') {
                    $('#formOrganisasi').show().find('input:not([type="file"])').prop('required', true);
                    $('#pi_identitas_narahubung').prop('required', true);
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
                    { input: '#pi_upload_nik_pengguna' },
                    { input: '#pi_upload_nik_pengguna_penginput'},
                    { input: '#pi_upload_nik_pengguna_informasi'},
                    { input: '#pi_identitas_narahubung'},
                    { input: '#pi_bukti_aduan'}
                ];

                fileInputs.forEach(item => {
                    $(item.input).change(function() {
                        validateAndUpdateFile($(this), item.label);
                    });
                });
            }

            function validateAndUpdateFile(input, labelSelector) {
                const file = input[0].files[0];
                if (file) {
                    const fileSizeMB = file.size / (1024 * 1024);
                    $(labelSelector).text(file.name + ' (' + fileSizeMB.toFixed(2) + ' MB)');

                    if (fileSizeMB > 2) {
                        Swal.fire({
                            title: 'Peringatan!',
                            text: 'Ukuran file ' + fileSizeMB.toFixed(2) + ' MB melebihi batas 2MB',
                            icon: 'warning'
                        });
                        input.val('');
                        $(labelSelector).text('Pilih file');
                        return false;
                    }
                }
                return true;
            }

            function handleFormSubmit(e) {
                e.preventDefault();

                if (isSubmitting) return; // cegah klik submit berulang
                isSubmitting = true;
                button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);

                let isValid = validateForm();

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Mohon periksa kembali input Anda'
                    });
                    
                    button.html('Ajukan Permohonan Informasi').attr('disabled', false);
                    isSubmitting = false;
                    return; // Hentikan proses submit
                }

                // Submit pakai AJAX
                const formData = new FormData(this);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('Success response:', response);
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            }).then(() => {
                                window.location.href = "{{ url($permohonanInformasiAdminUrl) }}";
                            });
                        } else {
                            // Perbaikan: Tampilkan pesan kesalahan yang lebih detail
                            if (response.errors) {
                                handleServerErrors(response.errors);
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Terjadi kesalahan saat menyimpan data'
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Error status:', xhr.status);
                        console.error('Error response:', xhr.responseText);
                        
                        let errorMessage = 'Terjadi kesalahan server. Silakan coba lagi.';
                        
                        // Coba parse error message dari response JSON
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            if (errorResponse.message) {
                                errorMessage = errorResponse.message;
                            }
                            
                            if (errorResponse.errors) {
                                handleServerErrors(errorResponse.errors);
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: errorMessage
                        });
                    },
                    complete: function() {
                        button.html('Ajukan Permohonan Informasi').attr('disabled', false);
                        isSubmitting = false;
                    }
                });
            }

            function validateForm() {
                let isValid = true;
                const kategoriPemohon = $('#pi_kategori_pemohon').val();
                if (kategoriPemohon === 'Diri Sendiri') {
                    const fileInput = document.getElementById('pi_upload_nik_pengguna');
                    if (!fileInput.files || fileInput.files.length === 0) {
                        showError('#f_pi_upload_nik_pengguna_error', 'File identitas pelapor wajib diunggah');
                        isValid = false;
                    } else {
                        hideError('#f_pi_upload_nik_pengguna_error');
                    }
                }

                // Validasi spesifik berdasarkan kategori
                if (kategoriPemohon === 'Diri Sendiri') {
                    isValid &= validateField('#pi_nama_pengguna', '#f_pi_nama_pengguna_error');
                    isValid &= validateField('#pi_alamat_pengguna', '#f_pi_alamat_pengguna_error');
                    isValid &= validateField('#pi_no_hp_pengguna', '#f_pi_no_hp_pengguna_error');
                    isValid &= validateField('#pi_email_pengguna', '#f_pi_email_pengguna_error');
                    isValid &= validateFile('#pi_upload_nik_pengguna', '#f_pi_upload_nik_pengguna_error');
                } else if (kategoriPemohon === 'Orang Lain') {
                    ['#pi_nama_pengguna_penginput', '#pi_alamat_pengguna_penginput', '#pi_no_hp_pengguna_penginput', '#pi_email_pengguna_penginput',
                     '#pi_nama_pengguna_informasi', '#pi_alamat_pengguna_informasi', '#pi_no_hp_pengguna_informasi', '#pi_email_pengguna_informasi']
                    .forEach(selector => {
                        isValid &= validateField(selector, `#f_${selector.replace('#', '')}_error`);
                    });
                    isValid &= validateFile('#pi_upload_nik_pengguna_penginput', '#f_pi_upload_nik_pengguna_penginput_error');
                    isValid &= validateFile('#pi_upload_nik_pengguna_informasi', '#f_pi_upload_nik_pengguna_informasi_error');
                } else if (kategoriPemohon === 'Organisasi') {
                    ['#pi_nama_organisasi', '#pi_no_telp_organisasi', '#pi_email_atau_medsos_organisasi', '#pi_nama_narahubung', '#pi_no_telp_narahubung']
                    .forEach(selector => {
                        isValid &= validateField(selector, `#f_${selector.replace('#', '')}_error`);
                    });
                    isValid &= validateFile('#pi_identitas_narahubung', '#f_pi_identitas_narahubung_error');
                }

                // Validasi umum
                isValid &= validateField('#pi_informasi_yang_dibutuhkan', '#f_pi_informasi_yang_dibutuhkan_error');
                isValid &= validateField('#pi_alasan_permohonan_informasi', '#f_pi_alasan_permohonan_informasi_error');
                isValid &= validateField('#pi_alamat_sumber_informasi', '#f_pi_alamat_sumber_informasi_error');
                isValid &= validateFile('#pi_bukti_aduan', '#f_pi_bukti_aduan_error');

                // Validasi radio button
                if (!$('input[name="t_permohonan_informasi[pi_sumber_informasi]"]:checked').length) {
                    showError('#f_pi_sumber_informasi_error', 'Pilih sumber informasi');
                    isValid = false;
                } else {
                    hideError('#f_pi_sumber_informasi_error');
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
                const value = $(fieldSelector).val();
                if (!value) {
                    showError(errorSelector, 'File wajib diunggah');
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
                $(selector).hide();
            }

            function handleServerErrors(errors) {
                // Reset semua pesan error sebelumnya
                $('.invalid-feedback').hide();
                
                if (errors) {
                    $.each(errors, function(key, messages) {
                        // Tangani error untuk form utama
                        if (key.includes('t_permohonan_informasi.')) {
                            const fieldName = key.replace('t_permohonan_informasi.', '');
                            $(`#f_${fieldName}_error`).text(messages[0]).show();
                        } 
                        // Tangani error untuk form diri sendiri
                        else if (key.includes('t_form_pi_diri_sendiri.')) {
                            const fieldName = key.replace('t_form_pi_diri_sendiri.', '');
                            $(`#f_${fieldName}_error`).text(messages[0]).show();
                        }
                        // Tangani error untuk form orang lain
                        else if (key.includes('t_form_pi_orang_lain.')) {
                            const fieldName = key.replace('t_form_pi_orang_lain.', '');
                            $(`#f_${fieldName}_error`).text(messages[0]).show();
                        }
                        // Tangani error untuk form organisasi
                        else if (key.includes('t_form_pi_organisasi.')) {
                            const fieldName = key.replace('t_form_pi_organisasi.', '');
                            $(`#f_${fieldName}_error`).text(messages[0]).show();
                        }
                        // Tangani error untuk file upload
                        else {
                            $(`#f_${key}_error`).text(messages[0]).show();
                        }
                    });
                }
            }
        });
        </script>
        @endpush
@endsection
