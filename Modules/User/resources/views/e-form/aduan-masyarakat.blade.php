<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Form | Pengaduan Masyarakat</title>
    @vite(['resources/css/app.css', 'resources/js/upload-ktp.js', 'resources/js/upload-bukti.js'])

    <!-- Tambahkan CSS khusus untuk upload -->
    <style>
        .upload-zone {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 24px;
            text-align: center;
            transition: all 0.2s ease;
            cursor: pointer;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .upload-zone:hover {
            border-color: #f97316;
            background-color: #fff7ed;
        }

        .upload-zone.border-orange-500 {
            border-color: #f97316;
            background-color: #fff7ed;
        }

        .upload-preview {
            max-width: 200px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .file-preview {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .upload-btn {
            background-color: #f97316;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 14px;
            transition: background-color 0.2s;
            cursor: pointer;
        }

        .upload-btn:hover {
            background-color: #ea580c;
            color: white;
        }

        .upload-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .text-gray-400 {
            color: #9ca3af;
        }

        .text-gray-600 {
            color: #4b5563;
        }

        .text-gray-500 {
            color: #6b7280;
        }

        .text-orange-500 {
            color: #f97316;
        }

        .font-semibold {
            font-weight: 600;
        }

        .font-medium {
            font-weight: 500;
        }

        .text-sm {
            font-size: 0.875rem;
        }

        .text-xs {
            font-size: 0.75rem;
        }

        .text-4xl {
            font-size: 2.25rem;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mt-1 {
            margin-top: 0.25rem;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .d-none {
            display: none !important;
        }

        .invisible {
            visibility: hidden;
        }

        /* Alpine.js cloak */
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="eform-bg" style="background: url('{{ asset('img/bgwavy.webp') }}') repeat; background-size: contain;">
    @include('user::layouts.header')
    <section>
        <div class="title-page text-black text-center mx-auto px-3 px-md-5">
            <h2 class="fw-bold"> Formulir Pengaduan Masyarakat</h2>
        </div>
        <div class="form-wrap">
            <div class="container d-flex justify-content-center align-items-center">
                <div class="p-4 rounded bg-white shadow-lg" style="max-width: 600px; width: 100%;">
                    <div x-data="{ step: 1 }" x-cloak>
                        <!-- Stepper Indicator -->
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

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show mb-4">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- FORM UTAMA -->
                        <form action="{{ route('form-aduan-masyarakat.store') }}" method="POST" enctype="multipart/form-data"
                            id="aduan-form">
                            @csrf
                            
                            <!-- STEP 1: Caution -->
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
                                <!-- Tombol Lanjut -->
                                <div class="mt-4 d-flex justify-content-between gap-2">
                                    <button type="button" class="lanjut-btn btn-primary w-100 mt-3" @click="step = 2">
                                        Lanjut
                                    </button>
                                </div>
                            </div>

                            <!-- STEP 2: Form Pengaduan -->
                            <div x-show="step === 2" x-cloak>
                                <div class="form-group mb-3">
                                    <label class="label-form mb-3" for="jenis_laporan">
                                        Jenis Laporan <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('pm_jenis_laporan') is-invalid @enderror"
                                        id="jenis_laporan" name="pm_jenis_laporan" required>
                                        <option value="">- Pilih Jenis Pengaduan -</option>
                                        <option value="Pelanggaran Disiplin Pegawai"
                                            {{ old('pm_jenis_laporan') == 'Pelanggaran Disiplin Pegawai' ? 'selected' : '' }}>
                                            Pelanggaran Disiplin Pegawai</option>
                                        <option value="Penyalahgunaan Wewenang / Mal Administrasi"
                                            {{ old('pm_jenis_laporan') == 'Penyalahgunaan Wewenang / Mal Administrasi' ? 'selected' : '' }}>
                                            Penyalahgunaan Wewenang / Mal Administrasi</option>
                                        <option value="Pungutan Liar, Percaloan, dan Pengurusan Dokumen"
                                            {{ old('pm_jenis_laporan') == 'Pungutan Liar, Percaloan, dan Pengurusan Dokumen' ? 'selected' : '' }}>
                                            Pungutan Liar, Percaloan, dan Pengurusan Dokumen</option>
                                        <option value="Perilaku Amoral (Kekerasan Rumah Tangga / KDRT / Perselingkuhan)"
                                            {{ old('pm_jenis_laporan') == 'Perilaku Amoral (Kekerasan Rumah Tangga / KDRT / Perselingkuhan)' ? 'selected' : '' }}>
                                            Perilaku Amoral (Kekerasan Rumah Tangga / KDRT / Perselingkuhan)</option>
                                        <option value="Pengadaan Barang dan Jasa"
                                            {{ old('pm_jenis_laporan') == 'Pengadaan Barang dan Jasa' ? 'selected' : '' }}>
                                            Pengadaan Barang dan Jasa</option>
                                        <option value="Narkoba"
                                            {{ old('pm_jenis_laporan') == 'Narkoba' ? 'selected' : '' }}>Narkoba
                                        </option>
                                        <option value="Pelayanan Publik"
                                            {{ old('pm_jenis_laporan') == 'Pelayanan Publik' ? 'selected' : '' }}>
                                            Pelayanan Publik</option>
                                    </select>
                                    @error('pm_jenis_laporan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Data Pelapor -->
                                <div class="form-group mb-4">
                                    <h5>Data Pelapor</h5>
                                    <div class="alert alert-info">
                                        <small><i class="fas fa-info-circle mr-1"></i> Data pelapor akan dijaga
                                            kerahasiaannya dan hanya digunakan untuk keperluan verifikasi</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="label-form">Nama Pelapor <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('pm_nama_pelapor') is-invalid @enderror"
                                                name="pm_nama_pelapor" value="{{ old('pm_nama_pelapor') }}" required>
                                            @error('pm_nama_pelapor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="label-form">NIK Pelapor <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('pm_nik_pelapor') is-invalid @enderror"
                                                name="pm_nik_pelapor" value="{{ old('pm_nik_pelapor') }}" required
                                                maxlength="16" minlength="16" pattern="[0-9]{16}"
                                                title="NIK harus 16 digit angka">
                                            @error('pm_nik_pelapor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="label-form">No HP / Telepon <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('pm_no_hp_pelapor') is-invalid @enderror"
                                                name="pm_no_hp_pelapor" value="{{ old('pm_no_hp_pelapor') }}"
                                                required>
                                            @error('pm_no_hp_pelapor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="label-form">Email <span class="text-danger">*</span></label>
                                            <input type="email"
                                                class="form-control @error('pm_email_pelapor') is-invalid @enderror"
                                                name="pm_email_pelapor" value="{{ old('pm_email_pelapor') }}"
                                                required>
                                            @error('pm_email_pelapor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Upload KTP -->
                                <div class="form-group mb-3">
                                    <label class="label-form">Upload Foto Kartu Identitas <span
                                            class="text-danger">*</span></label>
                                    <br>
                                    <label class="text-muted">Silakan scan / Foto kartu identitas (KTP/SIM/Paspor).
                                        Semua data pada kartu identitas harus tampak jelas.</label>

                                    <div x-data="uploadHandler('pm_upload_nik_pengguna')" class="upload-box mt-2">
                                        <div class="upload-zone" @dragover.prevent="dragging = true"
                                            @dragleave="dragging = false" @drop.prevent="handleDrop($event)"
                                            @click="$refs.fileInput.click()"
                                            :class="{ 'border-orange-500': dragging }">

                                            <template x-if="previewUrl && isImage">
                                                <div>
                                                    <img :src="previewUrl" class="upload-preview" alt="Preview">
                                                    <div class="mt-2">
                                                        <button type="button" @click.stop="clearFile()"
                                                            class="btn btn-danger btn-sm">
                                                            Hapus File
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>

                                            <template x-if="fileName && !isImage">
                                                <div class="file-preview">
                                                    <i class="fas fa-file text-4xl text-orange-500 mb-2"></i>
                                                    <p class="text-sm font-medium" x-text="fileName"></p>
                                                    <p class="text-xs text-gray-500" x-text="fileSize"></p>
                                                    <button type="button" @click.stop="clearFile()"
                                                        class="btn btn-danger btn-sm mt-2">
                                                        Hapus File
                                                    </button>
                                                </div>
                                            </template>

                                            <div x-show="!fileName" class="upload-placeholder">
                                                <i class="fas fa-upload text-4xl text-gray-400 mb-3"></i>
                                                <p class="text-sm text-gray-600">
                                                    <strong>Drag and drop <span
                                                            class="text-orange-500 font-semibold">or choose file</span>
                                                        to upload</strong>
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG maksimal 2MB</p>
                                                <button type="button" @click.stop="$refs.fileInput.click()"
                                                    class="btn upload-btn">
                                                    Pilih File
                                                </button>
                                            </div>
                                        </div>

                                        <input type="file" x-ref="fileInput" name="pm_upload_nik_pengguna"
    class="d-none" accept="image/*" @change="handleFileSelect" required>

                                        <div class="text-danger small mt-2" x-text="errorMessage"></div>
                                        @error('pm_upload_nik_pengguna')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Informasi Pengaduan -->
                                <div class="form-group mb-4 mt-4">
                                    <h5>Informasi Pengaduan</h5>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="label-form">Nama Unit / Pegawai yang Dilaporkan <span
                                            class="text-danger">*</span></label>
                                    <br>
                                    <label class="text-muted">Identitas Pelapor akan sangat dirahasikan, kami
                                        menghargai setiap informasi yang anda laporkan.</label>
                                    <input type="text"
                                        class="form-control @error('pm_nama_terlapor') is-invalid @enderror"
                                        name="pm_nama_terlapor" value="{{ old('pm_nama_terlapor') }}" required>
                                    @error('pm_nama_terlapor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="label-form" for="jabatan_terlapor">Jabatan <span class="text-danger">*</span></label>
                                            <select
                                                class="form-select @error('pm_jabatan_terlapor') is-invalid @enderror"
                                                id="jabatan_terlapor" name="pm_jabatan_terlapor" required>
                                                <option value="">- Pilih Jabatan -</option>
                                                <option value="Staff"
                                                    {{ old('pm_jabatan_terlapor') == 'Staff' ? 'selected' : '' }}>
                                                    Staff</option>
                                                <option value="Dosen"
                                                    {{ old('pm_jabatan_terlapor') == 'Dosen' ? 'selected' : '' }}>
                                                    Dosen</option>
                                                <option value="Tidak Tahu"
                                                    {{ old('pm_jabatan_terlapor') == 'Tidak Tahu' ? 'selected' : '' }}>
                                                    Tidak Tahu</option>
                                            </select>
                                            @error('pm_jabatan_terlapor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="label-form" for="tanggal_kejadian">Tanggal & Waktu
                                                Kejadian <span class="text-danger">*</span></label>
                                            <input type="datetime-local" name="pm_tanggal_kejadian"
                                                id="tanggal_kejadian"
                                                class="form-control @error('pm_tanggal_kejadian') is-invalid @enderror"
                                                value="{{ old('pm_tanggal_kejadian') }}" required>
                                            @error('pm_tanggal_kejadian')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="label-form">Lokasi Kejadian <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control @error('pm_lokasi_kejadian') is-invalid @enderror"
                                        name="pm_lokasi_kejadian" value="{{ old('pm_lokasi_kejadian') }}" required>
                                    @error('pm_lokasi_kejadian')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="label-form">Kronologis Kejadian <span
                                            class="text-danger">*</span></label>
                                    <br>
                                    <label class="text-muted">Jelaskan kronologis kejadian secara lengkap dan
                                        runtut.</label>
                                    <textarea class="form-control @error('pm_uraian_pengaduan') is-invalid @enderror" name="pm_uraian_pengaduan"
                                        required rows="4">{{ old('pm_uraian_pengaduan') }}</textarea>
                                    @error('pm_uraian_pengaduan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Upload Bukti Pendukung -->
                                <div class="form-group mb-3">
                                    <label class="label-form">Upload Bukti Pendukung <span
                                            class="text-danger">*</span></label>
                                    <br>
                                    <label class="text-muted">Upload file bukti pendukung. Format: PDF, gambar,
                                        dokumen, video, audio. Maksimal 100MB.</label>

                                    <div x-data="uploadHandler('bukti_pendukung')" class="upload-box mt-2">
                                        <div class="upload-zone" @dragover.prevent="dragging = true"
                                            @dragleave="dragging = false" @drop.prevent="handleDrop($event)"
                                            @click="$refs.fileInput.click()"
                                            :class="{ 'border-orange-500': dragging }">

                                            <template x-if="previewUrl && isImage">
                                                <div>
                                                    <img :src="previewUrl" class="upload-preview" alt="Preview">
                                                    <div class="mt-2">
                                                        <button type="button" @click.stop="clearFile()"
                                                            class="btn btn-danger btn-sm">
                                                            Hapus File
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>

                                            <template x-if="fileName && !isImage">
                                                <div class="file-preview">
                                                    <i class="fas fa-file text-4xl text-orange-500 mb-2"></i>
                                                    <p class="text-sm font-medium" x-text="fileName"></p>
                                                    <p class="text-xs text-gray-500" x-text="fileSize"></p>
                                                    <button type="button" @click.stop="clearFile()"
                                                        class="btn btn-danger btn-sm mt-2">
                                                        Hapus File
                                                    </button>
                                                </div>
                                            </template>

                                            <div x-show="!fileName" class="upload-placeholder">
                                                <i class="fas fa-upload text-4xl text-gray-400 mb-3"></i>
                                                <p class="text-sm text-gray-600">
                                                    <strong>Drag and drop <span
                                                            class="text-orange-500 font-semibold">or choose file</span>
                                                        to upload</strong>
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG, DOC, MP4, MP3,
                                                    dll. Maksimal 100MB</p>
                                                <button type="button" @click.stop="$refs.fileInput.click()"
                                                    class="btn upload-btn">
                                                    Pilih File
                                                </button>
                                            </div>
                                        </div>

                                        <input type="file" x-ref="fileInput" name="pm_bukti_pendukung"
                                            class="d-none"
                                            accept=".pdf,.jpg,.jpeg,.png,.svg,.doc,.docx,.mp4,.avi,.mov,.wmv,.3gp,.mp3,.wav,.ogg,.m4a"
                                            @change="handleFileSelect" required>

                                        <div class="text-danger small mt-2" x-text="errorMessage"></div>
                                        @error('pm_bukti_pendukung')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="label-form">Catatan</label>
                                    <br>
                                    <label class="text-muted">Opsional.</label>
                                    <textarea class="form-control @error('pm_catatan') is-invalid @enderror" name="pm_catatan" rows="4">{{ old('pm_catatan') }}</textarea>
                                    @error('pm_catatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Tombol Back & Submit -->
                                <div class="mt-4 d-flex justify-content-between gap-2">
                                    <button type="button" class="btn btn-secondary w-auto px-4"
                                        @click="step = 1">Kembali</button>
                                    <button type="submit" class="btn btn-success w-auto px-4">Kirim
                                        Pengaduan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('user::layouts.footer')

    <!-- Script Alpine.js dan JavaScript -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('uploadHandler', (inputName) => ({
                dragging: false,
                previewUrl: null,
                fileName: '',
                fileSize: '',
                isImage: false,
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

                    // Validate file size (2MB)
                    const maxSize = 2 * 1024 * 1024;
                    if (file.size > maxSize) {
                        this.errorMessage = 'Ukuran file maksimal 2MB';
                        return;
                    }

                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    if (!allowedTypes.includes(file.type)) {
                        this.errorMessage = 'File harus berupa gambar (JPG, PNG)';
                        return;
                    }

                    // Set file info
                    this.fileName = file.name;
                    this.fileSize = this.formatSize(file.size);
                    this.isImage = file.type.startsWith('image/');

                    // Show preview for images
                    if (this.isImage) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.previewUrl = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                },

                clearFile() {
                    this.previewUrl = null;
                    this.fileName = '';
                    this.fileSize = '';
                    this.isImage = false;
                    this.errorMessage = '';
                    this.$refs.fileInput.value = '';
                },

                formatSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                }
            }));

            Alpine.data('multiUploadHandler', {
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
            });
        });

        // Form validation sebelum submit
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('aduan-form');

            if (form) {
                form.addEventListener('submit', function(e) {
                    let hasError = false;
                    let errorMessages = [];

                    // Validasi NIK 16 digit
                    const nikInput = document.querySelector('input[name="nik_pelapor"]');
                    if (nikInput && (nikInput.value.length !== 16 || !/^\d+$/.test(nikInput.value))) {
                        errorMessages.push('NIK harus 16 digit angka');
                        hasError = true;
                    }

                    // Validasi file KTP wajib ada
                    const ktpInput = document.querySelector('input[name="pm_upload_nik_pengguna"]');
                    if (!ktpInput || !ktpInput.files || ktpInput.files.length === 0) {
                        errorMessages.push('File KTP/NIK wajib diupload');
                        hasError = true;
                    }

                    // Validasi email format
                    const emailInput = document.querySelector('input[name="email_pelapor"]');
                    if (emailInput) {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(emailInput.value)) {
                            errorMessages.push('Format email tidak valid');
                            hasError = true;
                        }
                    }

                    if (hasError) {
                        e.preventDefault();
                        alert('Terdapat kesalahan:\n\n' + errorMessages.join('\n'));
                        return false;
                    }

                    // Show loading state
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
                    }
                });
            }
        });
    </script>
</body>

</html>