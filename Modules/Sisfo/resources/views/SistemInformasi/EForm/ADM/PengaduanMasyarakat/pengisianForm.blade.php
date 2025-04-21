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

            <form id="pengaduanForm"
                action="{{ url('SistemInformasi/EForm/' . Auth::user()->level->level_kode . '/PengaduanMasyarakat/createData') }}"
                method="POST" enctype="multipart/form-data" novalidate>
                @csrf

                <!-- Data Pelapor -->
                <h4 class="mb-3">Data Pelapor</h4>

                <div class="form-group">
                    <label for="pm_nama_tanpa_gelar">Nama Lengkap Pelapor (Tanpa Gelar)<span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="pm_nama_tanpa_gelar"
                        name="t_pengaduan_masyarakat[pm_nama_tanpa_gelar]"
                        value="{{ old('t_pengaduan_masyarakat.pm_nama_tanpa_gelar') }}">
                    <div class="invalid-feedback" id="f_pm_nama_tanpa_gelar_error"></div>
                </div>

                <div class="form-group">
                    <label for="pm_no_hp_pengguna">Nomor HP Pelapor<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="pm_no_hp_pengguna"
                        name="t_pengaduan_masyarakat[pm_no_hp_pengguna]"
                        value="{{ old('t_pengaduan_masyarakat.pm_no_hp_pengguna') }}">
                    <div class="invalid-feedback" id="f_pm_no_hp_pengguna_error"></div>
                </div>

                <div class="form-group">
                    <label for="pm_email_pengguna">Email Pelapor<span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="pm_email_pengguna"
                        name="t_pengaduan_masyarakat[pm_email_pengguna]"
                        value="{{ old('t_pengaduan_masyarakat.pm_email_pengguna') }}">
                    <div class="invalid-feedback" id="f_pm_email_pengguna_error"></div>
                </div>

                <div class="form-group">
                    <label for="pm_nik_pengguna">Upload NIK Pelapor <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="pm_nik_pengguna"
                        name="t_pengaduan_masyarakat[pm_nik_pengguna]"
                        value="{{ old('t_pengaduan_masyarakat.pm_nik_pengguna') }}">
                    <div class="invalid-feedback" id="f_pm_nik_pengguna_error"></div>
                </div>

                <div class="form-group">
                    <label for="pm_upload_nik_pengguna">Upload KTP/Identitas Pelapor <span
                            class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="pm_upload_nik_pengguna" name="pm_upload_nik_pengguna"
                        accept="image/*">
                    <div class="invalid-feedback" id="f_pm_upload_nik_pengguna_error"></div>
                </div>

                <!-- Data Pengaduan -->
                <h4 class="mb-3 mt-4">Detail Pengaduan</h4>

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
                        <input class="form-check-input" type="radio" id="jabatan_1"
                            name="t_pengaduan_masyarakat[pm_jabatan]" value="Staff"
                            {{ old('t_pengaduan_masyarakat.pm_jabatan') == 'Staff' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="jabatan_1">Staff</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="jabatan_2"
                            name="t_pengaduan_masyarakat[pm_jabatan]" value="Dosen"
                            {{ old('t_pengaduan_masyarakat.pm_jabatan') == 'Dosen' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="jabatan_2">Dosen</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="jabatan_3"
                            name="t_pengaduan_masyarakat[pm_jabatan]" value="Tidak tahu"
                            {{ old('t_pengaduan_masyarakat.pm_jabatan') == 'Tidak tahu' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="jabatan_3">Tidak tahu</label>
                    </div>
                    <div class="invalid-feedback" id="f_pm_jabatan_error"></div>
                </div>

                <div class="form-group">
                    <label for="pm_waktu_kejadian">Waktu Kejadian <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control" id="pm_waktu_kejadian"
                        name="t_pengaduan_masyarakat[pm_waktu_kejadian]"
                        value="{{ old('t_pengaduan_masyarakat.pm_waktu_kejadian') }}">
                    <div class="invalid-feedback" id="f_pm_waktu_kejadian_error"></div>
                </div>

                <div class="form-group">
                    <label for="pm_lokasi_kejadian">Lokasi Kejadian <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="pm_lokasi_kejadian"
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
                    <label for="pm_bukti_pendukung">Bukti Pendukung <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="pm_bukti_pendukung" name="pm_bukti_pendukung">
                    <small class="form-text text-muted">
                        Format yang diizinkan:
                        <br>- Dokumen: PDF, DOC, DOCX
                        <br>- Gambar: JPG, JPEG, PNG, SVG
                        <br>- Video: MP4, AVI, MOV, WMV, 3GP
                        <br>- Audio/Rekaman: MP3, WAV, OGG, M4A
                        <br>Maksimal 100MB.
                    </small>
                    <div class="invalid-feedback" id="f_pm_bukti_pendukung_error"></div>
                </div>

                <div class="form-group">
                    <label for="pm_catatan_tambahan">Catatan Tambahan (opsional)</label>
                    <textarea class="form-control" id="pm_catatan_tambahan" name="t_pengaduan_masyarakat[pm_catatan_tambahan]"
                        rows="4">{{ old('t_pengaduan_masyarakat.pm_catatan_tambahan') }}</textarea>
                    <div class="invalid-feedback" id="f_pm_catatan_tambahan_error"></div>
                </div>


                <div class="form-group">
                    <label for="pm_bukti_aduan">Upload Bukti Aduan <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="pm_bukti_aduan" name="pm_bukti_aduan"
                        accept="file/*">
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

                <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>Ajukan Pengaduan</button>
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
                $('#pengaduanForm').submit(function(e) {
                    let isValid = true;

                    const namaPelapor = $('#pm_nama_tanpa_gelar').val();
                    if (namaPelapor === '') {
                        isValid = false;
                        $('#pm_nama_tanpa_gelar').addClass('is-invalid');
                        $('#f_pm_nama_tanpa_gelar_error').text('Nama Pelapor Wajib diisi.').show();
                    }

                    const noHpPelapor = $('#pm_no_hp_pengguna').val();
                    if (noHpPelapor === '') {
                        isValid = false;
                        $('#pm_no_hp_pengguna').addClass('is-invalid');
                        $('#f_pm_no_hp_pengguna_error').text('Nomor HP Pelapor Wajib diisi.').show();
                    }

                    const emailPelapor = $('#pm_email_pengguna').val();
                    if (emailPelapor === '') {
                        isValid = false;
                        $('#pm_email_pengguna').addClass('is-invalid');
                        $('#f_pm_email_pengguna_error').text('Email Pelapor Wajib diisi.').show();
                    }

                    const nikPelapor = $('#pm_nik_pengguna').val();
                    if (nikPelapor === '') {
                        isValid = false;
                        $('#pm_nik_pengguna').addClass('is-invalid');
                        $('#f_pm_nik_pengguna_error').text('NIK Pelapor Wajib diisi.').show();
                    }

                    const fileUploadNik = $('#pm_upload_nik_pengguna').val();
                    if (fileUploadNik === '') {
                        isValid = false;
                        $('#pm_upload_nik_pengguna').addClass('is-invalid');
                        $('#f_pm_upload_nik_pengguna_error').text('KTP/Identitas Pelapor wajib diupload.')
                        .show();
                    }

                    const laporan = $('input[name="t_pengaduan_masyarakat[pm_jenis_laporan]"]:checked').val();
                    if (!laporan) {
                        isValid = false;
                        $('#f_pm_jenis_laporan_error').text('Jenis laporan wajib dipilih.').show();
                    }

                    const jabatan = $('input[name="t_pengaduan_masyarakat[pm_jabatan]"]:checked').val();
                    if (!jabatan) {
                        isValid = false;
                        $('#f_pm_jabatan_error').text('Jabatan wajib dipilih.').show();
                    }


                    const pihakDilaporkan = $('#pm_yang_dilaporkan').val();
                    if (pihakDilaporkan === '') {
                        isValid = false;
                        $('#pm_yang_dilaporkan').addClass('is-invalid');
                        $('#f_pm_yang_dilaporkan_error').text('Pihak yang dilaporkan wajib diisi.').show();
                    }

                    const waktuKejadian = $('#pm_waktu_kejadian').val();
                    if (waktuKejadian === '') {
                        isValid = false;
                        $('#pm_waktu_kejadian').addClass('is-invalid');
                        $('#f_pm_waktu_kejadian_error').text('Waktu kejadian wajib diisi.').show();
                    }

                    const lokasiKejadian = $('#pm_lokasi_kejadian').val();
                    if (lokasiKejadian === '') {
                        isValid = false;
                        $('#pm_lokasi_kejadian').addClass('is-invalid');
                        $('#f_pm_lokasi_kejadian_error').text('Lokasi kejadian wajib diisi.').show();
                    }

                    const kronologisKejadian = $('#pm_kronologis_kejadian').val();
                    if (kronologisKejadian === '') {
                        isValid = false;
                        $('#pm_kronologis_kejadian').addClass('is-invalid');
                        $('#f_pm_kronologis_kejadian_error').text('Kronologis kejadian wajib diisi.').show();
                    }

                    const buktiPendukung = $('#pm_bukti_pendukung').val();
                    if (buktiPendukung === '') {
                        isValid = false;
                        $('#pm_bukti_pendukung').addClass('is-invalid');
                        $('#f_pm_bukti_pendukung_error').text('Bukti pendukung wajib diupload.').show();
                    }

                    // Validasi untuk Catatan Tambahan (opsional)
                    const catatanTambahan = $('#pm_catatan_tambahan').val();
                    if (catatanTambahan && catatanTambahan.length > 500) { // Contoh validasi panjang karakter
                        isValid = false;
                        $('#pm_catatan_tambahan').addClass('is-invalid');
                        $('#f_pm_catatan_tambahan_error').text(
                            'Catatan tambahan tidak boleh lebih dari 500 karakter.').show();
                    }

                    const buktiAduan = $('#pm_bukti_aduan').val();
                    if (buktiAduan === '') {
                        isValid = false;
                        $('#pm_bukti_aduan').addClass('is-invalid');
                        $('#f_pm_bukti_aduan_error').text('Bukti aduan wajib diupload.').show();
                    }

                    if (!isValid) {
        console.log('Form validation failed');
        e.preventDefault(); // Prevent form submission
    } else {
        console.log('Form is valid');
    }
                });
            });
        </script>
    @endpush
@endsection
