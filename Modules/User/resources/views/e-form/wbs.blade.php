<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Form | Whistle Blowing System</title>
    @vite(['resources/css/app.css', 'resources/js/upload-ktp.js', 'resources/js/upload-bukti.js'])
</head>

<body class="eform-bg" style="background: url('{{ asset('img/bgwavy.webp') }}') repeat; background-size: contain;">
    @include('user::layouts.header')
    <section>
        <div class="title-page text-black text-center mx-auto px-3 px-md-5">
            <h2 class="fw-bold"> Formulir Pelaporan Whistle Blowing System</h2>
        </div>
        <div class="form-wrap">
            <div class="container d-flex justify-content-center align-items-center ">
                <div class="p-4 rounded bg-white shadow-lg" style="max-width: 600px; width: 100%;">
                    <div x-data="{ step: 1 }" x-cloak>
                        <!-- Stepper Indicator - Dimodifikasi menjadi 2 step -->
                        <div class="d-flex align-items-center justify-content-center mb-4">
                            <div class="step-indicator" :class="step === 1 ? 'active' : ''">1</div>
                            <div class="step-line" :class="step === 2 ? 'active-line' : ''"></div>
                            <div class="step-indicator" :class="step === 2 ? 'active' : ''">2</div>
                        </div>

                        <!-- Alert Messages -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-4">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show mb-4">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- FORM UTAMA -->
                        <form action="{{ route('form-wbs.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <!-- STEP 1: Caution -->
                            <div x-show="step === 1">
                                <p class="text-danger fw-bold text-center fs-3">
                                    <strong>PERHATIAN!</strong>
                                </p>
                                <p>
                                    e-Form ini digunakan untuk pelaporan dugaan penyimpangan perilaku atau penyalahgunaan wewenang
                                    atau permasalahan lain yang dilakukan pegawai di lingkungan Politeknik Negeri Malang.
                                    <br><br>
                                    Jika laporan Anda memenuhi syarat / kriteria WBS, maka akan diproses lebih lanjut.
                                </p>
                                <p>
                                    Kami menghargai segala informasi yang Anda laporkan.
                                    <strong>PPID MENJAGA</strong> dan <strong>MERAHASIAKAN</strong> identitas pribadi Saudara sebagai <strong>PELAPOR</strong>.
                                </p>
                                <!-- Tombol Lanjut -->
                                <div class="mt-4 d-flex justify-content-between gap-2">
                                    <button type="button" class="lanjut-btn btn-primary w-100 mt-3"
                                        @click="step = 2">
                                        Lanjut
                                    </button>
                                </div>
                            </div>

                            <!-- STEP 2: Form Pelaporan -->
                            <div x-show="step === 2" x-cloak>
                                <div class="form-group mb-3">
                                    <label class="label-form mb-3" for="wbs_jenis_laporan">
                                        Jenis Laporan <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="wbs_jenis_laporan" name="wbs_jenis_laporan" required>
                                        <option value="">- Pilih Jenis Pelaporan -</option>
                                        <option value="Pelanggaran Disiplin Pegawai">Pelanggaran Disiplin Pegawai</option>
                                        <option value="Penyalahgunaan Wewenang / Mal Administrasi">Penyalahgunaan Wewenang / Mal Administrasi</option>
                                        <option value="Pungutan Liar, Percaloan, dan Pengurusan Dokumen">Pungutan Liar, Percaloan, dan Pengurusan Dokumen</option>
                                        <option value="Perilaku Amoral (Kekerasan Rumah Tangga / KDRT / Perselingkuhan)">Perilaku Amoral (Kekerasan Rumah Tangga / KDRT / Perselingkuhan)</option>
                                        <option value="Pengadaan Barang dan Jasa">Pengadaan Barang dan Jasa</option>
                                        <option value="Narkoba">Narkoba</option>
                                        <option value="Pelayanan Publik">Pelayanan Publik</option>
                                    </select>
                                </div>

                                <!-- Data Pelapor -->
                                <div class="form-group mb-4">
                                    <h5>Data Pelapor</h5>
                                    <div class="alert alert-info">
                                        <small><i class="fas fa-info-circle mr-1"></i> Data pelapor akan dijaga kerahasiaannya dan hanya digunakan untuk keperluan verifikasi</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="label-form">Nama Pelapor <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="wbs_nama_pelapor" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="label-form">NIK Pelapor <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="wbs_nik_pelapor" required maxlength="16" minlength="16">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="label-form">No HP / Telepon <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="wbs_no_hp_pelapor" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="label-form">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="wbs_email_pelapor" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="label-form">Upload Foto Kartu Identitas <span
                                            class="text-danger">*</span></label>
                                    <br>
                                    <label class="text-muted">Silakan scan / Foto kartu identitas (KTP/SIM/Paspor).
                                        Semua data pada kartu identitas harus tampak jelas.</label>

                                    <div x-data="uploadHandler('wbs_upload_nik_pelapor')" class="upload-box">
                                        <div class="upload-zone relative border-2 border-dashed border-gray-300 rounded-lg p-6 transition-all hover:border-orange-500 text-center"
                                            @dragover.prevent="dragging = true" 
                                            @dragleave="dragging = false"
                                            @drop.prevent="handleDrop($event)"
                                            :class="{ 'border-orange-500': dragging }">

                                            <template x-if="previewUrl">
                                                <img :src="previewUrl" class="upload-preview" alt="Preview">
                                            </template>

                                            <div x-show="!previewUrl" class="upload-placeholder">
                                                <i class="fas fa-upload text-4xl text-gray-400 mb-3"></i>
                                                <p class="text-sm text-gray-600">
                                                    <strong>Drag and drop <span
                                                            class="text-orange-500 font-semibold">or choose
                                                            file</span> to upload</strong>
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 2MB</p>
                                                <button type="button" @click="$refs.fileInput.click()"
                                                    class="btn upload-btn px-4 py-2 shadow-sm">
                                                    Pilih File
                                                </button>
                                            </div>
                                        </div>

                                        <input type="file" x-ref="fileInput" name="wbs_upload_nik_pelapor"
                                            class="absolute invisible w-0 h-0" accept="image/*"
                                            @change="handleFileSelect" required>

                                        <div class="text-red-500 text-sm mt-2" x-text="errorMessage"></div>
                                    </div>
                                </div>

                                <!-- Informasi Laporan -->
                                <div class="form-group mb-4 mt-4">
                                    <h5>Informasi Laporan</h5>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="label-form">Nama Pegawai / Staff atau Bagian yang dilaporkan <span class="text-danger">*</span></label>
                                    <br>
                                    <label class="text-muted">Identitas Pelapor akan sangat dirahasiakan, kami menghargai setiap informasi yang anda laporkan.</label>
                                    <input type="text" class="form-control" name="wbs_yang_dilaporkan" required>
                                </div>

                                <div class="row">
                                    <!-- Jabatan Pegawai / Staff -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="label-form" for="wbs_jabatan_terlapor">Jabatan Pegawai / Staff</label>
                                            <select class="form-select" id="wbs_jabatan_terlapor" name="wbs_jabatan_terlapor" required>
                                                <option value="">- Pilih Jabatan -</option>
                                                <option value="Pegawai">Pegawai</option>
                                                <option value="Dosen">Dosen</option>
                                                <option value="Tidak Tahu">Tidak Tahu</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Tanggal & Waktu Kejadian -->
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="label-form" for="wbs_tanggal_kejadian">Tanggal & Waktu Kejadian</label>
                                            <input type="datetime-local" name="wbs_tanggal_kejadian" id="wbs_tanggal_kejadian" class="form-control" required>
                                            <small id="error-wbs_tanggal_kejadian" class="error-text form-text text-danger"></small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="label-form">Lokasi Kejadian <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="wbs_lokasi_kejadian" required>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="label-form">Kronologis Kejadian <span class="text-danger">*</span></label>
                                    <br>
                                    <label class="text-muted">Jelaskan kronologis kejadian secara lengkap dan runtut.</label>
                                    <textarea class="form-control" name="wbs_kronologis_kejadian" required rows="4"></textarea>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="label-form">Upload Bukti Pendukung <span class="text-danger">*</span></label>
                                    <br>
                                    <label class="text-muted">Upload maksimum 5 file yang didukung. Maks 5MB per file.</label>
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
                <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF, PDF, DOCX, dll. (maks 5 file, total 25MB)</p>
                <div x-data>
                    <button type="button" @click="$refs.fileInput.click()" class="btn upload-btn px-4 py-2 shadow-sm">
                        Pilih File
                    </button>
                    <input type="file" multiple x-ref="fileInput" name="wbs_bukti_pendukung[]" class="absolute invisible w-0 h-0" @change="handleFileSelect">
                    <!-- Hidden input for validation -->
                    <input type="hidden" :required="fileList.length === 0" :value="fileList.length ? 'has-files' : ''" name="wbs_bukti_pendukung_validation">
                    <div class="text-danger mt-2" x-show="fileList.length === 0 && formSubmitted">
                        Bukti pendukung wajib diupload
                    </div>
                </div>
                <div id="file-error" class="text-red-500 text-sm mt-2" x-text="errorMessage"></div>
            </div>
        </div>


                                            <!-- File List Display -->
                                            <div x-show="fileList.length > 0" class="mt-4">
                                                <h4 class="font-medium text-sm mb-2">File yang akan diupload (<span x-text="fileList.length"></span>/5):</h4>
                                                <div class="total-size-info flex justify-between text-sm mb-2">
                                                    <span>Total ukuran: <span x-text="formatSize(totalSize)"></span>/25MB</span>
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

                                <div class="form-group mb-4">
                                    <label class="label-form">Catatan</label>
                                    <br>
                                    <label class="text-muted">Opsional.</label>
                                    <textarea class="form-control" name="wbs_catatan" rows="4"></textarea>
                                </div>

                                <!-- Tombol Back & Submit -->
                                <div class="mt-4 d-flex justify-content-between gap-2">
                                    <button type="button" class="btn btn-secondary w-auto px-4"
                                        @click="step = 1">Kembali</button>
                                    <button type="submit" class="btn btn-success w-auto px-4">Kirim Pelaporan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('user::layouts.footer')
</body>

</html>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('uploadHandler', (inputName) => ({
            dragging: false,
            previewUrl: null,
            uploading: false,
            uploadProgress: 0,
            errorMessage: '',
            
            handleFileSelect(event) {
                const file = event.target.files[0];
                this.processFile(file);
            },
            
            handleDrop(event) {
                this.dragging = false;
                const file = event.dataTransfer.files[0];
                this.processFile(file);
                
                // Set file to input
                const input = this.$refs.fileInput;
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
            },
            
            processFile(file) {
                this.errorMessage = '';
                
                if (!file) return;
                
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    this.errorMessage = 'File harus berupa gambar (JPG, PNG)';
                    return;
                }
                
                // Validate file size (2MB)
                const maxSize = 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    this.errorMessage = 'Ukuran file maksimal 2MB';
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewUrl = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }));

        Alpine.data('multiUploadHandler', () => ({
            dragging: false,
            fileList: [],
            totalSize: 0,
            maxFiles: 5,
            maxSize: 5 * 1024 * 1024, // 5MB per file
            maxTotalSize: 25 * 1024 * 1024, // 25MB total
            uploading: false,
            uploadProgress: 0,
            errorMessage: '',
            
            handleFileSelect(event) {
                this.errorMessage = '';
                const newFiles = Array.from(event.target.files);
                
                if (this.fileList.length + newFiles.length > this.maxFiles) {
                    this.errorMessage = `Maksimal ${this.maxFiles} file yang diperbolehkan`;
                    return;
                }
                
                for (let file of newFiles) {
                    // Check file size
                    if (file.size > this.maxSize) {
                        this.errorMessage = `File ${file.name} melebihi 5MB`;
                        continue;
                    }
                    
                    // Check total size
                    if (this.totalSize + file.size > this.maxTotalSize) {
                        this.errorMessage = `Total ukuran file melebihi 25MB`;
                        break;
                    }
                    
                    // Add file to list
                    this.fileList.push(file);
                    this.totalSize += file.size;
                }
                
                // Update the actual input for form submission
                this.updateFormInput();
            },
            
            handleDrop(event) {
                this.dragging = false;
                const newFiles = Array.from(event.dataTransfer.files);
                
                if (this.fileList.length + newFiles.length > this.maxFiles) {
                    this.errorMessage = `Maksimal ${this.maxFiles} file yang diperbolehkan`;
                    return;
                }
                
                for (let file of newFiles) {
                    if (file.size > this.maxSize) {
                        this.errorMessage = `File ${file.name} melebihi 5MB`;
                        continue;
                    }
                    
                    if (this.totalSize + file.size > this.maxTotalSize) {
                        this.errorMessage = `Total ukuran file melebihi 25MB`;
                        break;
                    }
                    
                    this.fileList.push(file);
                    this.totalSize += file.size;
                }
                
                this.updateFormInput();
            },
            
            removeFile(index) {
                this.totalSize -= this.fileList[index].size;
                this.fileList.splice(index, 1);
                this.updateFormInput();
            },
            
            clearAllFiles() {
                this.fileList = [];
                this.totalSize = 0;
                this.updateFormInput();
            },
            
            updateFormInput() {
                const input = this.$refs.fileInput;
                const dt = new DataTransfer();
                
                this.fileList.forEach(file => {
                    dt.items.add(file);
                });
                
                input.files = dt.files;
            },
            
            formatSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
        }));
    });
</script>