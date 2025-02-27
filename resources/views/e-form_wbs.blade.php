<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>E-Form Whistle Blowing System</title>
    @vite(['resources/css/app.css','resources/js/upload-bukti.js'])
</head>
<body style="background: url('img/bg-eform.svg') center/cover no-repeat;>
    @include('layouts.header')
    {{-- @include('layouts.navbar') --}}
    <div class="title-page text-white">
        <h2> Formulir Whsitle Blowing System</h2>
    </div>
    <section class="container py-4 mb-5">
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="title-form "> Identitas Pemohon Permohonan Informasi</h3>
        </div>
        <div class="card-body ">
            <form action="{{ url('SistemInformasi/EForm/PermohonanInformasi/storePermohonanInformasi') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-3">
                    <label class="label-form">Nama Pemohon (Nama Tanpa Gelar) <span class="text-danger">*</span> </label>
                    <br>
                    <label class="text-muted">Jangan Khawatitr Identitas Pelapor akan sangat dirahasikan, kami menghargai setiap informasi yang anda laporkan.</label>
                    <input type="text" class="form-control" name="pi_nama_pengguna_informasi">
                </div>
                <div class="form-group mb-3">
                    <label class="label-form mb-3" for="jenis_laporan">
                        Jenis Laporan <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="jenis_laporan" name="jenis_laporan" required>
                        <option value="">- Pilih Jenis Laporan -</option>
                        <option value="Pelanggaran Disiplin Pegawai">Pelanggaran Disiplin Pegawai</option>
                        <option value="Penyalahgunaan Wewenang / Mal Administrasi">Penyalahgunaan Wewenang / Mal Administrasi</option>
                        <option value="Pungutan Liar, Percaloan, dan Pengurusan Dokumen">Pungutan Liar, Percaloan, dan Pengurusan Dokumen</option>
                        <option value="Perilaku Amoral (Kekerasan Rumah Tangga / KDRT / Perselingkuhan)">Perilaku Amoral (Kekerasan Rumah Tangga / KDRT / Perselingkuhan)</option>
                        <option value="Pengadaan Barang dan Jasa">Pengadaan Barang dan Jasa</option>
                        <option value="Narkoba">Narkoba</option>
                        <option value="Pelayanan Publik">Pelayanan Publik</option>
                    </select>
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
                
                <div class="form-group mb-3">
                    <label class="label-form">Upload Bukti Pendukung Laporan <span class="text-danger">*</span></label>
                    <br>
                    <label class="text-muted">Upload maksimum 5 file yang didukung. Maks 100 MB per file.</label>
                    <div class="col-md-6">
                        <div x-data="multiUploadHandler" class="upload-box">
                            <div class="upload-zone relative border-2 border-dashed border-gray-300 rounded-lg p-6 transition-all hover:border-orange-500 text-center"
                                @dragover.prevent="dragging = true"
                                @dragleave="dragging = false"
                                @drop.prevent="handleDrop($event)"
                                :class="{ 'border-orange-500': dragging }">
                                
                                <div x-show="fileList.length === 0" class="upload-placeholder">
                                    <i class="fas fa-upload text-4xl text-gray-400 mb-3"></i>
                                    <p class="text-sm text-gray-600">
                                        <strong>Drag and drop <span class="text-orange-500 font-semibold">or choose files</span> to upload</strong>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF, PDF, DOCX, dll. (maks 5 file, total 100MB)</p>
                                    <div x-data>
                                        <button type="button" @click="$refs.fileInput.click()" class="btn upload-btn px-4 py-2 shadow-sm">
                                            Pilih File
                                        </button>
                                        <input type="file" multiple x-ref="fileInput" class="absolute invisible w-0 h-0" @change="handleFileSelect">
                                    </div>
                                    <div id="file-error" class="text-red-500 text-sm mt-2" x-text="errorMessage"></div>
                                </div>
                            </div>
                
                            <!-- File List Display -->
                            <div x-show="fileList.length > 0" class="mt-4">
                                <h4 class="font-medium text-sm mb-2">File yang akan diupload (<span x-text="fileList.length"></span>/5):</h4>
                                <div class="total-size-info flex justify-between text-sm mb-2">
                                    <span>Total ukuran: <span x-text="formatSize(totalSize)"></span>/100MB</span>
                                    <span x-show="totalSize > 0" class="text-xs cursor-pointer text-red-500" @click="clearAllFiles">Hapus Semua</span>
                                </div>
                                
                                <template x-for="(file, index) in fileList" :key="index">
                                    <div class="file-item flex items-center justify-between p-2 mb-2 bg-gray-100 rounded">
                                        <div class="file-info flex items-center">
                                            <i class="fas fa-file mr-2 text-orange-500"></i>
                                            <div>
                                                <p class="text-sm font-medium truncate max-w-xs" x-text="file.name"></p>
                                                <p class="text-xs text-gray-500" x-text="formatSize(file.size)"></p>
                                            </div>
                                        </div>
                                        <button @click="removeFile(index)" class="btn upload-btn px-4 py-2 shadow-sm">
                                            Hapus
                                        </button>
                                    </div>
                                </template>
                                
                                <div x-show="fileList.length < 5" class="mt-3">
                                    <button type="button" @click="$refs.addMoreInput.click()" class="btn upload-btn px-4 py-2 shadow-sm">
                                        Tambah file lagi
                                    </button>
                                    <input type="file" multiple x-ref="addMoreInput" class="absolute invisible w-0 h-0" @change="handleFileSelect">
                                </div>
                                
                                <button x-show="fileList.length > 0" @click="uploadFiles" class="btn upload-btn px-4 py-2 shadow-sm mt-2">
                                    Upload Semua File
                                </button>
                            </div>
                
                            <!-- Upload Progress -->
                            <div x-show="uploading" class="mt-4">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-orange-500 h-2.5 rounded-full" :style="`width: ${uploadProgress}%`"></div>
                                </div>
                                <p class="text-sm text-gray-600 mt-2 text-center">
                                    Mengupload... <span x-text="uploadProgress + '%'"></span>
                                </p>
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
