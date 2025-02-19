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
        <h2> Formulir Permohonan Informasi</h2>
    </div>
    <section class="container e-form py-4 mb-5">
<div x-data="{ kategori: '' }">
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="title-form "> Identitas Pemohon Permohonan Informasi</h3>
        </div>
        <div class="card-body ">
            <form action="{{ url('SistemInformasi/EForm/PermohonanInformasi/storePermohonanInformasi') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="form-group ">
                    <label class="label-form" for="pi_kategori_pemohon">Permohonan Informasi Dilakukan Atas</label>
                    <select  x-model="kategori" class="form-select" id="pi_kategori_pemohon" name="pi_kategori_pemohon" required>
                        <option value="">- Pilih Kategori Pemohon -</option>
                        <option value="Diri Sendiri">Diri Sendiri</option>
                        <option value="Orang Lain">Orang Lain</option>
                        <option value="Organisasi">Organisasi / Lembaga Masyarakat atau Yayasan atau Perusahaan</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    
   <!-- Form untuk Organisasi -->
   <div id="formOrganisasi" x-show="kategori === 'Organisasi'" x-cloak>
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between ">
            <h3 class="title-form ">Data Identitas Pemohon Organisasi</h3>
        </div>
        <div class="card-body ">
            <form action="{{ url('SistemInformasi/EForm/PermohonanInformasi/storePermohonanInformasi') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                    <div class="form-group mb-3">
                        <label class="label-form">Nama Organisasi <span class="text-danger">*</span> </label>
                        <input type="text" class="form-control" name="pi_nama_organisasi">
                    </div>
                    <div class="form-group mb-3">
                        <label class="label-form">No Telepon Organisasi <span class="text-danger">*</span> </label>
                        <input type="text" class="form-control" name="pi_no_telp_organisasi">
                    </div>
                    <div class="form-group mb-3">
                        <label class="label-form">Email dan Website / Media Sosial Organisasi <span class="text-danger">*</span> </label>
                        <p class="text-muted">
                            Contoh : <br>
                            * Email: organisasi@google.com, website: organisasi.co <br>
                            * Email: organisasi@google.com, IG: organisasi_sosial_masyarakat
                        </p>
                        <input type="text" class="form-control" name="pi_email_atau_medsos_organisasi">
                    </div>
                    <div class="form-group mb-3">
                        <label class="label-form">Nama Narahubung Organisasi <span class="text-danger">*</span> </label>
                        <input type="text" class="form-control" name="pi_nama_narahubung">
                    </div>
                    <div class="form-group mb-3">
                        <label class="label-form">No Telepon Narahubung Organisasi <span class="text-danger">*</span> </label>
                        <input type="text" class="form-control" name="pi_no_telp_narahubung">
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
                </form>
        </div>
    </div>
   </div>

      <!-- Form untuk Orang Lain -->
    <div id="formOrangLain" x-show="kategori === 'Orang Lain'" x-cloak>
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between ">
            <h3 class="title-form ">Data Identitas Pemohon Orang Lain</h3>
        </div>
        <div class="card-body ">
            <form action="{{ url('SistemInformasi/EForm/PermohonanInformasi/storePermohonanInformasi') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
               
                    <div class="form-group mb-3">
                        <label class="label-form">Nama Pemohon <span class="text-danger">*</span> </label>
                        <br>
                        <label class="text-muted">Nama lengkap sesuai KTP</label>
                        <input type="text" class="form-control" name="pi_nama_pengguna_informasi">
                    </div>
                    <div class="form-group mb-3">
                        <label class="label-form">Alamat Pemohon <span class="text-danger">*</span> </label>
                        <br>
                        <label class="text-muted">Alamat lengkap sesuai KTP</label>
                        <input type="text" class="form-control" name="pi_alamat_pengguna_informasi">
                    </div>
                    <div class="form-group mb-3">
                        <label class="label-form">No HP / Telepon Pemohon <span class="text-danger">*</span> </label>
                        <input type="text" class="form-control" name="pi_no_hp_pengguna_informasi">
                    </div>
                    <div class="form-group mb-3">
                        <label class="label-form">Email Pemohon <span class="text-danger">*</span> </label>
                        <input type="email" class="form-control" name="pi_email_pengguna_informasi">
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
            </form>
        </div>
    </div>
 </div>

</div>

     <!-- Form umum untuk semua kategori -->
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between ">
            <h3 class="title-form "> Pertanyaan dan Kebutuhan Informasi</h3>
        </div>
        <div class="card-body ">
            <form action="{{ url('SistemInformasi/EForm/PermohonanInformasi/storePermohonanInformasi') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-3">
                    <label class="label-form">Informasi yang Dibutuhkan <span class="text-danger">*</span> </label>
                    <textarea class="form-control" name="pi_informasi_yang_dibutuhkan" required rows="4"></textarea>
                </div>
                <div class="form-group mb-3">
                    <label class="label-form">Alasan Permohonan Informasi <span class="text-danger">*</span> </label>
                    <textarea class="form-control" name="pi_alasan_permohonan_informasi" required rows="4"></textarea>
                </div>
                <div class="form-group  mb-3">
                    <label class="label-form">Sumber Informasi <span class="text-danger">*</span> </label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="pi_sumber_informasi[]"
                            value="Pertanyaan Langsung Pemohon">
                        <label class="form-check-label">Pertanyaan Langsung Pemohon</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="pi_sumber_informasi[]"
                            value="Website / Media Sosial Milik Polinema">
                        <label class="form-check-label">Website / Media Sosial Milik Polinema</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="pi_sumber_informasi[]"
                            value="Website / Media Sosial Bukan Milik Polinema">
                        <label class="form-check-label">Website / Media Sosial Bukan Milik Polinema</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="label-form">Alamat Sumber Informasi <span class="text-danger">*</span> </label>
                    <label class="text-muted">Isikan tautan / alamat website jika pertanyaan bukan merupakan pertanyaan langsung pemohon</label>
                    <input type="text" class="form-control" name="pi_alamat_sumber_informasi" required>
                </div>
            </form>
        </div>
    </div>
    <button type="submit" class="form-btn mt-3">Submit</button>
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
