<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>E-Form Pernyataan Keberatan</title>
    @vite(['resources/css/app.css', 'resources/js/upload-ktp.js'])
</head>

<body>
    @include('user::layouts.header')
    <section>
        <div class="title-page text-black text-center">
            <h2 class="fw-bold"> Formulir Pernyataan Keberatan</h2>
        </div>
        <div class="form-wrap">
            <div class="container d-flex justify-content-center align-items-center ">
                <div class="e-form p-4 rounded bg-white shadow-lg" style="max-width: 600px; width: 100%;">
                    <div x-data="{ step: 1, kategori: '' }" x-cloak>
                        <!-- Stepper Indicator -->
                        <div class="d-flex align-items-center justify-content-center mb-4">
                            <div class="step-indicator" :class="step === 1 || step === 2 ? 'active' : ''">1</div>
                            <div class="step-line" :class="step === 2 ? 'active-line' : ''"></div>
                            <div class="step-indicator" :class="step === 2 ? 'active' : ''">2</div>
                        </div>

                        <form action="{{ url('SistemInformasi/EForm/PermohonanInformasi/storePermohonanInformasi') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <div x-show="step === 1">
                                <form
                                    action="{{ url('SistemInformasi/EForm/PermohonanInformasi/storePermohonanInformasi') }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label class="label-form" for="pi_kategori_pemohon">Pengajuan Keberatan
                                            Dilakukan Atas</label>
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
                                    <div>
                                        <button type="button" class="lanjut-btn btn-primary w-100 mt-3"
                                            :disabled="!kategori" @click="step = 2">
                                            Lanjut
                                        </button>
                                    </div>

                                </form>
                            </div>

                            <!-- STEP 2: Pertanyaan & Informasi -->
                            <div x-show="step === 2" x-cloak>
                                <form
                                    action="{{ url('SistemInformasi/EForm/PernyataanKeberatan/storePernyataanKeberatan') }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label class="label-form mb-3" for="pi_alasan_pengajuan">
                                            Alasan Pengajuan Keberatan <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="pi_alasan_pengajuan"
                                            name="pi_alasan_pengajuan" required>
                                            <option value="">- Pilih Alasan Pengajuan -</option>
                                            <option value="Permohonan Informasi Ditolak">Permohonan Informasi Ditolak
                                            </option>
                                            <option value="Form Informasi Berkala Tidak Tersedia">Form Informasi
                                                Berkala Tidak Tersedia</option>
                                            <option value="Permintaan Informasi Tidak Dipenuhi">Permintaan Informasi
                                                Tidak Dipenuhi</option>
                                            <option
                                                value="Permintaan Informasi Ditanggapi Tidak Sebagaimana Yang Diminta">
                                                Permintaan Informasi Ditanggapi Tidak Sebagaimana Yang Diminta</option>
                                            <option value="Biaya Yang Dikenakan Tidak Wajar">Biaya Yang Dikenakan Tidak
                                                Wajar</option>
                                            <option
                                                value="Informasi Yang Disampaikan Melebihi Jangka Waktu Yang Ditentukan">
                                                Informasi Yang Disampaikan Melebihi Jangka Waktu Yang Ditentukan
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="label-form">Kasus Posisi <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" name="pi_alasan_permohonan_informasi" required rows="4"></textarea>
                                    </div>
                                </form>

                                <!-- Tombol Back & Submit -->
                                <div class="mt-4 d-flex justify-content-between gap-2">
                                    <button type="button" class="btn btn-secondary w-auto px-4"
                                        @click="step = 1">Kembali</button>
                                    <button type="submit" class="btn btn-success w-auto px-4"
                                        >Kirim Permohonan</button>
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
