<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>E-Form</title>
    @vite(['resources/css/e-form.css','resources/js/app.js'])
</head>
<body>
    @include('layouts.header')
    @include('layouts.navbar')
    <div class="title-page mt-5">
        <h2>Formulir Whistle Blwoing System</h2>   
    </div>
    <section class="container e-form py-4 mb-5">
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="title-form "> Identitas Pemohon Permohonan Informasi</h3>
        </div>
        <div class="card-body ">
            <form action="{{ url('SistemInformasi/EForm/PermohonanInformasi/storePermohonanInformasi') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="label-form">Nama Pemohon (Nama Tanpa Gelar) <span class="text-danger">*</span> </label>
                    <br>
                    <label class="text-muted">Jangan Khawatitr Identitas Pelapor akan sangat dirahasikan, kami menghargai setiap informasi yang anda laporkan.</label>
                    <input type="text" class="form-control" name="pi_nama_pengguna_informasi">
                </div>
            </form>
        </div>
    </div>


     <!-- Form umum untuk semua kategori -->
    <div class="card ">
        <div class="card-header">
            <h3 class="title-form "> Pertanyaan dan Kebutuhan Informasi</h3>
        </div>
        <div class="card-body ">
            <form action="{{ url('SistemInformasi/EForm/PermohonanInformasi/storePermohonanInformasi') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="form-group  mb-3">
                    <label class="label-form mb-3">Jenis Laporan <span class="text-danger">*</span> </label>
                    <div class="form-radio mb-2">
                        <input class="form-radio-input" type="radio" name="pi_alasan_pengajuan[]"
                            value="Pertanyaan Langsung Pemohon">
                        <label class="form-radio-label">Pelanggaran Disiplin Pegawai</label>
                    </div>
                    <div class="form-radio mb-2">
                        <input class="form-radio-input" type="radio" name="pi_alasan_pengajuan[]"
                            value="Website / Media Sosial Milik Polinema">
                        <label class="form-radio-label">Penyalahgunaan Wewenang / Mal Administrasi</label>
                    </div>
                    <div class="form-radio mb-2">
                        <input class="form-radio-input" type="radio" name="pi_alasan_pengajuan[]"
                            value="Website / Media Sosial Bukan Milik Polinema">
                        <label class="form-radio-label">Pungutan Liar, Percaloan, dan Pengurusan Dokumen</label>
                    </div>
                    <div class="form-radio mb-2">
                        <input class="form-radio-input" type="radio" name="pi_alasan_pengajuan[]"
                            value="Website / Media Sosial Bukan Milik Polinema">
                        <label class="form-radio-label">Perilaku Amoral (Kekerasan Rumah Tangga / KDRT / Perselingkuhan)</label>
                    </div>
                    <div class="form-radio mb-2">
                        <input class="form-radio-input" type="radio" name="pi_alasan_pengajuan[]"
                            value="Website / Media Sosial Bukan Milik Polinema">
                        <label class="form-radio-label">Pengadaan Barang dan Jasa</label>
                    </div>
                    <div class="form-radio mb-2">
                        <input class="form-radio-input" type="radio" name="pi_alasan_pengajuan[]"
                            value="Website / Media Sosial Bukan Milik Polinema">
                        <label class="form-radio-label">Narkoba</label>
                    </div>
                    <div class="form-radio mb-2">
                        <input class="form-radio-input" type="radio" name="pi_alasan_pengajuan[]"
                            value="Website / Media Sosial Bukan Milik Polinema">
                        <label class="form-radio-label">Pelayanan Publik</label>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label class="label-form">Nama Pegawai / Staff atau Bagian yang dilaporkan <span class="text-danger">*</span> </label>
                    <br>
                    <label class="text-muted">Identitas Pelapor akan sangat dirahasikan, kami menghargai setiap informasi yang anda laporkan.</label>
                    <input type="text" class="form-control" name="pi_nama_pengguna_informasi">
                </div>
                <div class="row">
                    <!-- Jabatan Pegawai / Staff -->
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="label-form" for="pi_kategori_pemohon">Jabatan Pegawai / Staff</label>
                            <select class="form-select" id="pi_kategori_pemohon" name="pi_kategori_pemohon" required>
                                <option value="">- Pilih Jabatan -</option>
                                <option value="Diri Sendiri">Pegawai</option>
                                <option value="Orang Lain">Dosen</option>
                                <option value="Orang Lain">Tidak Tahu</option>
                            </select>
                        </div>
                    </div>
                
                    <!-- Tanggal & Waktu Kejadian -->
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="label-form" for="pi_tanggal_waktu">Tanggal & Waktu Kejadian</label>
                            <input type="datetime-local" name="tanggal_selesai" id="tanggal_selesai" class="form-control" required>
                            <small id="error-tanggal_selesai" class="error-text form-text text-danger"></small>
                        </div>
                    </div>
                </div>                
                <div class="form-group mb-3">
                    <label class="label-form">Lokasi Kejadian <span class="text-danger">*</span> </label>
                    <input type="text" class="form-control" name="pi_alamat_pengguna_informasi">
                </div>
                <div class="form-group mb-3">
                    <label class="label-form">Kronologis Kejadian <span class="text-danger">*</span> </label>
                    <br>
                    <label class="text-muted">Jelaskan kronologis kejadian secara lengkap dan runtut.</label>
                    <textarea class="form-control" name="pi_alasan_permohonan_informasi" required rows="4"></textarea>
                </div>
                <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="label-form">Upload Bukti Pendukung Laporan <span class="text-danger">*</span> </label>
                    <br>
                    <label class="text-muted">Upload maksimum 5 file yang didukung. Maks 100 MB per file.</label>
                    <div class="upload-box">
                    <div class="upload-zone border-2 border-dashed border-gray-300 rounded-lg p-6 transition-all hover:border-orange-500 text-center">
                        <input type="file" id="ktp-upload" class="hidden" style="display: none;" accept="image/*">
                        <div id="upload-content">
                            <img src="" alt="" id="preview-image" class="max-h-32 mx-auto mb-4 hidden">
                            <div class="upload-placeholder">
                                <i class="bi bi-cloud-arrow-up"></i>
                                <p class="text-muted small mb-1">
                                    Drag and drop <span class="text-primary fw-semibold" role="button" id="upload-btn" >or browse</span> to upload
                                </p>
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
                <div class="form-group ">
                    <label class="label-form">Catatan</label>
                    <br>
                    <label class="text-muted">Opsional.</label>
                    <textarea class="form-control" name="pi_alasan_permohonan_informasi" required rows="4"></textarea>
                </div>
            </form>
        </div>
    </div>    
    <button type="submit" class="form-btn mt-3 ">Submit</button>
</section>
 
</body>
<footer>
    @include('layouts.footer')
</footer>
</html>
{{-- <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script> --}}
{{-- 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#pi_kategori_pemohon').change(function () {
            const selectedValue = $(this).val();

            // Sembunyikan semua form tambahan
            $('#formOrangLain, #formOrganisasi').hide();

            // Reset required attributes
            $('#formOrangLain input, #formOrganisasi input').prop('required', false);

            // Tampilkan form sesuai pilihan
            if (selectedValue === 'Orang Lain') {
                $('#formOrangLain').show();
                $('#formOrangLain input').prop('required', true);
            } else if (selectedValue === 'Organisasi') {
                $('#formOrganisasi').show();
                $('#formOrganisasi input').prop('required', true);
            }
        });
    });
</script> --}}
