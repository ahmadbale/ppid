<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>E-Form Permohonan Informasi</title>
    @vite(['resources/css/app.css', 'resources/js/upload-ktp.js', 'resources/js/upload-bukti.js'])
</head>

<body>
    @include('user::layouts.header')
    <section>
        <div class="title-page text-black text-center mx-auto px-3 px-md-5">
            <h2 class="fw-bold"> Formulir Pengaduan Masyarakat</h2>
        </div>
        <div class="form-wrap">
            <div class="container d-flex justify-content-center align-items-center ">
                <div class="e-form p-4 rounded bg-white shadow-lg" style="max-width: 600px; width: 100%;">
                    <div x-data="{ step: 1, kategori: '' }" x-cloak>
                        <!-- Stepper Indicator -->
                        <div class="d-flex align-items-center justify-content-center mb-4">
                            <div class="step-indicator" :class="step >= 1 ? 'active' : ''">1</div>
                            <div class="step-line" :class="step >= 2 ? 'active-line' : ''"></div>
                            <div class="step-indicator" :class="step >= 2 ? 'active' : ''">2</div>
                            <div class="step-line" :class="step === 3 ? 'active-line' : ''"></div>
                            <div class="step-indicator" :class="step === 3 ? 'active' : ''">3</div>
                        </div>

                        <!-- STEP 1: Caution -->
                        <form action="{{ url('SistemInformasi/EForm/PermohonanInformasi/storePermohonanInformasi') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <div x-show="step === 1">
                                <p class="text-danger fw-bold text-center fs-3">
                                    <strong>PERHATIAN!</strong>
                                </p>
                                <p>
                                    e-Form ini digunakan untuk menyampaikan keluhan, aspirasi, atau laporan terkait pelayanan publik
                                    di lingkungan Politeknik Negeri Malang.
                                    <br><br>
                                    Pengaduan dapat mencakup ketidaksesuaian layanan, pungutan liar, atau permasalahan lain
                                    yang berdampak pada masyarakat.
                                </p>
                                <p>
                                    Kami menghargai segala informasi yang Anda sampaikan.
                                    <strong>Identitas pelapor akan dijaga kerahasiaannya</strong>, dan setiap pengaduan akan
                                    ditindaklanjuti sesuai dengan ketentuan yang berlaku.
                                </p>
                                <!-- Tombol Back & Next -->
                                <div class="mt-4 d-flex justify-content-between gap-2">
                                    <button type="button" class="lanjut-btn btn-primary w-100 mt-3"
                                        @click="step = 2">
                                        Lanjut
                                    </button>
                                </div>
                            </div>

                            <!-- STEP 2: Pertanyaan & Informasi -->
                            <div x-show="step === 2" x-cloak>
                                <form
                                    action="{{ url('SistemInformasi/EForm/PermohonanInformasi/storePermohonanInformasi') }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label class="label-form" for="pi_kategori_pemohon">Pengaduan Dilakukan
                                            Atas</label>
                                        <select x-model="kategori" class="form-select" id="pi_kategori_pemohon"
                                            name="pi_kategori_pemohon" required>
                                            <option value="">- Pilih Kategori Pemohon -</option>
                                            @foreach ($kategori as $item)
                                                <option value="{{ $item['nama'] }}">{{ $item['nama'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="formOrangLain" x-show="kategori === 'Orang Lain'" x-cloak>
                                        <div class="form-group mb-3">
                                            <label class="label-form">Nama Pemohon <span class="text-danger">*</span>
                                            </label>
                                            <br>
                                            <label class="text-muted">Nama lengkap sesuai KTP</label>
                                            <input type="text" class="form-control"
                                                name="pi_nama_pengguna_informasi">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="label-form">Alamat Pemohon <span class="text-danger">*</span>
                                            </label>
                                            <br>
                                            <label class="text-muted">Alamat lengkap sesuai KTP</label>
                                            <input type="text" class="form-control"
                                                name="pi_alamat_pengguna_informasi" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="label-form">No HP / Telepon Pemohon <span
                                                            class="text-danger">*</span> </label>
                                                    <input type="text" class="form-control"
                                                        name="pi_no_hp_pengguna_informasi" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="label-form">Email Pemohon <span
                                                            class="text-danger">*</span>
                                                    </label>
                                                    <input type="email" class="form-control"
                                                        name="pi_email_pengguna_informasi" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="label-form">Upload Foto Kartu Identitas Pemohon <span
                                                    class="text-danger">*</span> </label>
                                            <br>
                                            <label class="text-muted">Silakan scan / Foto kartu identitas
                                                (KTP/SIM/Paspor)
                                                pemohon. Semua data pada kartu identitas harus tampak jelas dan
                                                terang.</label>

                                            <div x-data="uploadHandler" class="upload-box">
                                                <div class="upload-zone relative border-2 border-dashed border-gray-300 rounded-lg p-6 transition-all hover:border-orange-500 text-center"
                                                    @dragover.prevent="dragging = true; console.log('Dragging over')"
                                                    @dragleave="dragging = false; console.log('Dragging left')"
                                                    @drop.prevent="handleDrop($event); console.log('File dropped')"
                                                    :class="{ 'border-orange-500': dragging }">
                                                    <template x-if="previewUrl">
                                                        <img :src="previewUrl" class="upload-preview"
                                                            alt="Preview">
                                                    </template>

                                                    <div x-show="!previewUrl" class="upload-placeholder">
                                                        <i class="fas fa-upload text-4xl text-gray-400 mb-3"></i>
                                                        <p class="text-sm text-gray-600">
                                                            <strong> Drag and drop <span
                                                                    class="text-orange-500 font-semibold">or choose
                                                                    file</span> to
                                                                upload </strong>
                                                        </p>
                                                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 2MB
                                                        </p>
                                                        <div x-data>
                                                            <button type="button" @click="$refs.fileInput.click()"
                                                                class="btn upload-btn  px-4 py-2 shadow-sm">
                                                                Pilih File
                                                            </button>
                                                            <input type="file" x-ref="fileInput"
                                                                class="absolute invisible w-0 h-0" accept="image/*"
                                                                @change="handleFileSelect" required>
                                                        </div>


                                                        <div id="file-error" class="text-red-500 text-sm mt-2"
                                                            x-text="errorMessage"></div>
                                                    </div>
                                                </div>

                                                <!-- Progress Bar -->
                                                <div x-show="uploading" class="upload-progress mt-3">
                                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                        <div class="bg-orange-500 h-2.5 rounded-full"
                                                            :style="`width: ${uploadProgress}%`">
                                                        </div>
                                                    </div>
                                                    <p class="text-sm text-gray-600 mt-2 text-center">
                                                        Mengupload... <span x-text="uploadProgress + '%'"></span>
                                                    </p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div id="formDiriSendiri" x-show="kategori === 'Diri Sendiri'" x-cloak>
                                    </div>
                                    <div class="mt-4 d-flex justify-content-between gap-2 align-items-center">
                                        <button type="button" class="btn btn-secondary px-4 flex-grow-0 text-nowrap"
                                            style="height: 38px; line-height: 1;" @click="step = 1">Kembali</button>
                                        <button type="button" class="btn btn-primary px-4 flex-grow-0 text-nowrap"
                                            style="height: 38px; line-height: 1;" @click="step = 3">Lanjut</button>
                                    </div>
                                </form>
                            </div>
                            <!-- STEP 3: Pertanyaan & Informasi -->
                            <div x-show="step === 3" x-cloak>
                                <form
                                    action="{{ url('SistemInformasi/EForm/PermohonanInformasi/storePermohonanInformasi') }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label class="label-form mb-3" for="jenis_laporan">
                                            Jenis Laporan <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="jenis_laporan" name="jenis_laporan" required>
                                            <option value="">- Pilih Jenis Pengaduan -</option>
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
                                        <label class="label-form">Nama Unit / Pegawai yang Dilaporkan<span class="text-danger">*</span> </label>
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
                                        <label class="label-form">Uraian Pengaduan<span class="text-danger">*</span> </label>
                                        <br>
                                        <label class="text-muted">Jelaskan kronologis pengaduan secara lengkap dan runtut.</label>
                                        <textarea class="form-control" name="pi_alasan_permohonan_informasi" required rows="4"></textarea>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="label-form">Upload Bukti Pendukung Pengaduan<span class="text-danger">*</span></label>
                                        <br>
                                        <label class="text-muted">Upload maksimum 5 file yang didukung. Maks 100 MB per file.</label>
                                        <div class="col-md-12 mx-auto">
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
                                                        <span x-show="totalSize > 0" class="text-xs cursor-pointer text-red-500" @click="clearAllFiles"><br>Hapus Semua</span>
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

                                <!-- Tombol Back & Submit -->
                                <div class="mt-4 d-flex justify-content-between gap-2">
                                    <button type="button" class="btn btn-secondary w-auto px-4"
                                        @click="step = 2">Kembali</button>
                                    <button type="submit" class="btn btn-success w-auto px-4"
                                        >Kirim Pengaduan</button>
                                </div>
                            </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    @include('user::layouts.footer')
</body>

</html>
