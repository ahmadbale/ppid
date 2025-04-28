@extends('sisfo::layouts.template')
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <a href="{{ url('SistemInformasi/EForm/' . Auth::user()->level->level_kode . '/PermohonanPerawatan') }}"
                    class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
            <h3 class="card-title"><strong> E-Form Permohonan Perawatan Sarana Prasarana </strong></h3>
        </div>
        <div class="card-body">

            <form id="permohonanForm"
                action="{{ url('SistemInformasi/EForm/' . Auth::user()->level->level_kode . '/PermohonanPerawatan/createData') }}"
                method="POST" enctype="multipart/form-data" novalidate>
                @csrf

                <!-- Data Pelapor -->
                <h4 class="mb-3">Data Pelapor</h4>

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
                        <input type="number" class="form-control" id="pp_no_hp_pengguna"
                            name="t_permohonan_perawatan[pp_no_hp_pengguna]"
                            value="{{ old('t_permohonan_perawatan.pp_no_hp_pengguna') }}" maxlength="12">
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

                <!-- Data Permohonan Perawatan Sarana Prasarana -->
                <h4 class="mb-3 mt-4">Detail Permohonan Perawatan Sarana Prasarana</h4>

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
                        Unggah Foto Kondisi jika diperlukan.
                    </small>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="pp_foto_kondisi" name="pp_foto_kondisi"
                            accept="file/*">
                        <label class="custom-file-label" for="pp_foto_kondisi">Pilih foto (Maks. 2mb)</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="pi_bukti_aduan">
                        Upload Bukti Aduan <span class="text-danger">*</span>
                    </label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="pp_bukti_aduan" name="pp_bukti_aduan"
                            accept="file/*">
                        <label class="custom-file-label" for="pp_bukti_aduan">Pilih file (Maks. 2mb)</label>
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
            $(document).ready(function() {
                // Enable/disable submit button based on checkbox
                $('#persetujuan').change(function() {
                    if ($(this).is(':checked')) {
                        $('#btnSubmit').prop('disabled', false);
                    } else {
                        $('#btnSubmit').prop('disabled', true);
                    }
                });

                // Client-side validation for required fields
                $('#permohonanForm').submit(function(e) {
                    let isValid = true;

                    // Check if all required fields are filled
                    $('#pp_nama_pengguna, #pp_no_hp_pengguna, #pp_email_pengguna, #pp_unit_kerja, #pp_perawatan_yang_diusulkan, #pp_keluhan_kerusakan, #pp_lokasi_perawatan, #pp_bukti_aduan')
                        .each(function() {
                            if ($(this).val() === '') {
                                isValid = false;
                                $(this).addClass('is-invalid');
                                $(this).siblings('.invalid-feedback').show();
                            } else {
                                $(this).removeClass('is-invalid');
                                $(this).siblings('.invalid-feedback').hide();
                            }
                        });
                    // Check if the length of the phone number is within the limit
                    const phoneNumber = $('#pp_no_hp_pengguna').val();
                    if (phoneNumber.length > 12) {
                        isValid = false;
                        $('#pp_no_hp_pengguna').addClass('is-invalid');
                        $('#f_pp_no_hp_pengguna_error').text(
                            'Nomor HP Pengusul harus terdiri dari maksimal 12 digit.').show();
                    }

                    if (!isValid) {
                        e.preventDefault();
                    }
                });
            });
        </script>
    @endpush
@endsection
