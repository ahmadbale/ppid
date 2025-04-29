<!-- pengisian form halaman admin -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $pengaduanMasyarakatAdminUrl = WebMenuModel::getDynamicMenuUrl('pengaduan-masyarakat-admin');
@endphp
@extends('sisfo::layouts.template')
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <a href="{{ url($pengaduanMasyarakatAdminUrl) }}"
                    class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
            <h3 class="card-title"><strong> E-Form Permohonan Informasi </strong></h3>
        </div>
        <div class="card-body">

            <form id="permohonanForm"
                action="{{ url($pengaduanMasyarakatAdminUrl . '/createData') }}"
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
                            Silakan scan / foto kartu identitas (KTP/SIM/Paspor) pelapor. Semua data pada kartu identitas harus tampak jelas dan terang.
                        </small>
                        <small class="text-muted d-block mb-1">Maks. 2mb</small>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="pi_upload_nik_pengguna" name="pi_upload_nik_pengguna" accept="image/*">
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
                        <label for="pi_alamat_pengguna_penginput">Alamat Pelapor <span class="text-danger">*</span></label>
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
                        <label for="pi_upload_nik_pengguna">
                            Upload Foto Kartu Identitas pelapor <span class="text-danger">*</span>
                        </label>
                        <small class="text-muted d-block mb-2">
                            Silakan scan / foto kartu identitas (KTP/SIM/Paspor). Semua data pada kartu identitas harus tampak jelas dan terang.
                        </small>
                        <small class="text-muted d-block mb-1">Maks. 2mb</small>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="pi_upload_nik_pengguna" name="pi_upload_nik_pengguna" accept="image/*">
                            <label class="custom-file-label" for="pi_upload_nik_pengguna">Pilih file (PNG, JPG)</label>
                        </div>
                        <div class="invalid-feedback" id="f_pi_upload_nik_pengguna_error"></div>
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
                            Silakan scan / foto kartu identitas (KTP/SIM/Paspor). Semua data pada kartu identitas harus tampak jelas dan terang.
                        </small>
                        <small class="text-muted d-block mb-1">Maks. 2mb</small>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="pi_upload_nik_pengguna_informasi" name="pi_upload_nik_pengguna_informasi" accept="image/*">
                            <label class="custom-file-label" for="pi_upload_nik_pengguna_informasi">Pilih file (PNG, JPG)</label>
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
                            Upload Foto Kartu Identitas Pelapor <span class="text-danger">*</span>
                        </label>
                        <small class="text-muted d-block mb-2">
                            Silakan scan / foto kartu identitas (KTP/SIM/Paspor) pelapor. Semua data pada kartu identitas harus tampak jelas dan terang.
                        </small>
                        <small class="text-muted d-block mb-1">Maks. 2mb</small>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="pi_identitas_narahubung" name="pi_identitas_narahubung" accept="image/*">
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
                        <input type="file" class="custom-file-input" id="pi_bukti_aduan" name="pi_bukti_aduan" accept="file/*">
                        <label class="custom-file-label" for="pi_bukti_aduan">Pilih file (Maks. 2mb)</label>
                    </div>
                    <div class="invalid-feedback" id="f_pi_bukti_aduan_error"></div>
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

                <button type="submit" class="btn btn-success" id="btnSubmit" disabled>Ajukan Permohonan
                    Informasi</button>
            </form>
        </div>
    </div>

    @push('js')
        <script>
            $(document).ready(function() {
                // Tampilkan form yang sesuai saat halaman di-load berdasarkan nilai yang tersimpan
                const savedValue = "{{ old('t_permohonan_informasi.pi_kategori_pemohon') }}";
                if (savedValue) {
                    showFormBasedOnSelection(savedValue);
                }

                $('#pi_kategori_pemohon').change(function() {
                    const selectedValue = $(this).val();
                    showFormBasedOnSelection(selectedValue);
                });

                function showFormBasedOnSelection(selectedValue) {
                    // Sembunyikan semua form tambahan
                    $('#formDiriSendiri, #formOrangLain, #formOrganisasi').hide();

                    // Reset required attributes
                    $('#formDiriSendiri input, #formOrangLain input, #formOrganisasi input').prop('required', false);

                    // Tampilkan form sesuai pilihan
                    if (selectedValue === 'Orang Lain') {
                        $('#formOrangLain').show();
                        $('#formOrangLain input:not([type="file"])').prop('required', true);
                        $('#pi_upload_nik_pengguna_penginput, #pi_upload_nik_pengguna_informasi').prop('required',
                            true);
                    } else if (selectedValue === 'Organisasi') {
                        $('#formOrganisasi').show();
                        $('#formOrganisasi input:not([type="file"])').prop('required', true);
                        $('#pi_identitas_narahubung').prop('required', true);
                    } else if (selectedValue === 'Diri Sendiri') {
                        $('#formDiriSendiri').show();
                        $('#formDiriSendiri input:not([type="file"])').prop('required', true);
                        $('#pi_upload_nik_pengguna').prop('required', true);
                    }
                }

                // Toggle tombol submit berdasarkan checkbox persetujuan
                $('#persetujuan').change(function() {
                    if ($(this).is(':checked')) {
                        $('#btnSubmit').prop('disabled', false);
                    } else {
                        $('#btnSubmit').prop('disabled', true);
                    }
                });

                // Update nama file di custom file input
                $('.custom-file-input').on('change', function() {
                    const fileName = $(this).val().split('\\').pop();
                    $(this).next('.custom-file-label').addClass("selected").html(fileName);
                });

                // Client-side validation
                $('#permohonanForm').on('submit', function(e) {
                    let isValid = true;

                    // Validasi kategori pemohon
                    const kategoriPemohon = $('#pi_kategori_pemohon').val();
                    if (!kategoriPemohon) {
                        $('#f_pi_kategori_pemohon_error').text('Pilih kategori pemohon').show();
                        isValid = false;
                    } else {
                        $('#f_pi_kategori_pemohon_error').hide();
                    }

                    // Validasi form untuk kategori Diri Sendiri
                    if (kategoriPemohon === 'Diri Sendiri') {
                        isValid &= validateField('#pi_nama_pengguna', '#f_pi_nama_pengguna_error');
                        isValid &= validateField('#pi_alamat_pengguna', '#f_pi_alamat_pengguna_error');
                        isValid &= validateField('#pi_no_hp_pengguna', '#f_pi_no_hp_pengguna_error');
                        isValid &= validateField('#pi_email_pengguna', '#f_pi_email_pengguna_error');
                        isValid &= validateFile('#pi_upload_nik_pengguna', '#f_pi_upload_nik_pengguna_error');
                    }

                    // Validasi form untuk kategori Orang Lain
                    if (kategoriPemohon === 'Orang Lain') {
                        isValid &= validateField('#pi_nama_pengguna_penginput',
                            '#f_pi_nama_pengguna_penginput_error');
                        isValid &= validateField('#pi_alamat_pengguna_penginput',
                            '#f_pi_alamat_pengguna_penginput_error');
                        isValid &= validateField('#pi_no_hp_pengguna_penginput',
                            '#f_pi_no_hp_pengguna_penginput_error');
                        isValid &= validateField('#pi_email_pengguna_penginput',
                            '#f_pi_email_pengguna_penginput_error');
                        isValid &= validateFile('#pi_upload_nik_pengguna', '#f_pi_upload_nik_pengguna_error');

                        isValid &= validateField('#pi_nama_pengguna_informasi',
                            '#f_pi_nama_pengguna_informasi_error');
                        isValid &= validateField('#pi_alamat_pengguna_informasi',
                            '#f_pi_alamat_pengguna_informasi_error');
                        isValid &= validateField('#pi_no_hp_pengguna_informasi',
                            '#f_pi_no_hp_pengguna_informasi_error');
                        isValid &= validateField('#pi_email_pengguna_informasi',
                            '#f_pi_email_pengguna_informasi_error');
                        isValid &= validateFile('#pi_upload_nik_pengguna_informasi',
                            '#f_pi_upload_nik_pengguna_informasi_error');
                    }

                    // Validasi form untuk kategori Organisasi
                    if (kategoriPemohon === 'Organisasi') {
                        isValid &= validateField('#pi_nama_organisasi', '#f_pi_nama_organisasi_error');
                        isValid &= validateField('#pi_no_telp_organisasi', '#f_pi_no_telp_organisasi_error');
                        isValid &= validateField('#pi_email_atau_medsos_organisasi',
                            '#f_pi_email_atau_medsos_organisasi_error');
                        isValid &= validateField('#pi_nama_narahubung', '#f_pi_nama_narahubung_error');
                        isValid &= validateField('#pi_no_telp_narahubung', '#f_pi_no_telp_narahubung_error');
                        isValid &= validateFile('#pi_identitas_narahubung', '#f_pi_identitas_narahubung_error');
                    }

                    // Validasi form umum (semua kategori)
                    isValid &= validateField('#pi_informasi_yang_dibutuhkan',
                        '#f_pi_informasi_yang_dibutuhkan_error');
                    isValid &= validateField('#pi_alasan_permohonan_informasi',
                        '#f_pi_alasan_permohonan_informasi_error');
                    isValid &= validateField('#pi_alamat_sumber_informasi',
                        '#f_pi_alamat_sumber_informasi_error');
                    isValid &= validateFile('#pi_bukti_aduan', '#f_pi_bukti_aduan_error');

                    // Validasi Sumber Informasi
                    if (!$('input[name="t_permohonan_informasi[pi_sumber_informasi]"]:checked').length) {
                        $('#f_pi_sumber_informasi_error').text('Pilih sumber informasi').show();
                        isValid = false;
                    } else {
                        $('#f_pi_sumber_informasi_error').hide();
                    }

                    // Jika validasi gagal, cegah pengiriman form
                    if (!isValid) {
                        e.preventDefault();
                    }
                });

                // Function to validate required fields with a custom error message
                function validateField(fieldSelector, errorSelector) {
                    const fieldValue = $(fieldSelector).val();
                    if (!fieldValue) {
                        $(errorSelector).text('Field ini wajib diisi').show();
                        console.log("Field validation failed for:", fieldSelector); // Debugging line
                        return false;
                    } else {
                        $(errorSelector).hide();
                        return true;
                    }
                }


                // Function to validate file inputs
                function validateFile(fieldSelector, errorSelector) {
                    const fileValue = $(fieldSelector).val();
                    if (!fileValue) {
                        $(errorSelector).text('File wajib diunggah').show();
                        return false;
                    } else {
                        $(errorSelector).hide();
                        return true;
                    }
                }
            });
        </script>
    @endpush
@endsection
