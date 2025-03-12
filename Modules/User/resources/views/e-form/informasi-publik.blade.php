<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Form | Permohonan Informasi</title>
    @vite(['resources/css/app.css', 'resources/js/upload-ktp.js'])
</head>

<body class="eform-bg" style="background: url('{{ asset('img/bgwavy.webp') }}') repeat; background-size: contain;">
    @include('user::layouts.header')
    <section>
        <div class="title-page text-black text-center">
            <h2 class="fw-bold"> Formulir Permohonan Informasi Publik</h2>
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
                                        <label class="label-form" for="pi_kategori_pemohon">Permohonan Informasi
                                            Dilakukan
                                            Atas</label>
                                        <select x-model="kategori" class="form-select" id="pi_kategori_pemohon"
                                            name="pi_kategori_pemohon" required>
                                            <option value="">- Pilih Kategori Pemohon -</option>
                                            @foreach ($kategori as $item)
                                                <option value="{{ $item['nama'] }}">{{ $item['nama'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="formOrganisasi" x-show="kategori === 'Organisasi'" x-cloak>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="label-form">Nama Organisasi <span
                                                            class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" name="pi_nama_organisasi"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="label-form">No Telepon Organisasi <span
                                                            class="text-danger">*</span> </label>
                                                    <input type="text" class="form-control"
                                                        name="pi_no_telp_organisasi" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="label-form">Email dan Website / Media Sosial Organisasi <span
                                                    class="text-danger">*</span> </label>
                                            <p class="text-muted">
                                                Contoh : <br>
                                                * Email: organisasi@google.com, website: organisasi.co <br>
                                                * Email: organisasi@google.com, IG: organisasi_sosial_masyarakat
                                            </p>
                                            <input type="text" class="form-control"
                                                name="pi_email_atau_medsos_organisasi" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="label-form">Nama Narahubung Organisasi <span
                                                            class="text-danger">*</span> </label>
                                                    <input type="text" class="form-control" name="pi_nama_narahubung"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="label-form">No Telepon Narahubung<span
                                                            class="text-danger">*</span> </label>
                                                    <input type="text" class="form-control"
                                                        name="pi_no_telp_narahubung" required>
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
                                                                @change="handleFileSelect">
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
                                            <label class="label-form">Alamat Pemohon <span
                                                    class="text-danger">*</span>
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
                                        {{-- <p><strong>Nama:</strong> {{ auth()->user()->name }}</p>
                                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                                    <p><strong>Nomor HP:</strong> {{ auth()->user()->phone ?? 'Belum diisi' }}</p> --}}
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
                                    action="{{ url('SistemInformasi/EForm/PermohonanInformasi/storePermohonanInformasi') }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    {{-- <div class="form-group mb-3">
                                    <label class="label-form">Informasi yang Dibutuhkan <span
                                            class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" name="pi_informasi_yang_dibutuhkan" required rows="4"></textarea>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="label-form">Alasan Permohonan Informasi <span
                                            class="text-danger">*</span> </label>
                                    <textarea class="form-control" name="pi_alasan_permohonan_informasi" required rows="4"></textarea>
                                </div>
                                <div class="form-group  mb-3">
                                    <label class="label-form">Sumber Informasi <span class="text-danger">*</span>
                                    </label>
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
                                        <label class="form-check-label">Website / Media Sosial Bukan Milik
                                            Polinema</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="label-form">Alamat Sumber Informasi <span
                                            class="text-danger">*</span>
                                    </label>
                                    <label class="text-muted">Isikan tautan / alamat website jika pertanyaan bukan
                                        merupakan pertanyaan langsung pemohon</label>
                                    <input type="text" class="form-control" name="pi_alamat_sumber_informasi"
                                        required>
                                </div> --}}

                                    @foreach ($pertanyaanForm as $pertanyaan)
                                        <div class="form-group mb-3">
                                            <label class="label-form">
                                                {{ $pertanyaan['label'] }}
                                                @if (!empty($pertanyaan['required']))
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>

                                            {{-- Jika input type textarea --}}
                                            @if ($pertanyaan['type'] === 'textarea')
                                                <textarea class="form-control" name="{{ $pertanyaan['name'] }}" @if (!empty($pertanyaan['required'])) required @endif
                                                    rows="4"></textarea>

                                                {{-- Jika input type text --}}
                                            @elseif ($pertanyaan['type'] === 'text')
                                                <input type="text" class="form-control"
                                                    name="{{ $pertanyaan['name'] }}"
                                                    @if (!empty($pertanyaan['required'])) required @endif>

                                                {{-- Jika input type checkbox --}}
                                            @elseif ($pertanyaan['type'] === 'checkbox')
                                                @foreach ($pertanyaan['options'] as $option)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="{{ $pertanyaan['name'] }}[]"
                                                            value="{{ $option }}">
                                                        <label class="form-check-label">{{ $option }}</label>
                                                    </div>
                                                @endforeach

                                                {{-- Jika input type select (dropdown) --}}
                                            @elseif ($pertanyaan['type'] === 'select')
                                                <select class="form-select" name="{{ $pertanyaan['name'] }}"
                                                    @if (!empty($pertanyaan['required'])) required @endif>
                                                    <option value="">- Pilih {{ $pertanyaan['label'] }} -
                                                    </option>
                                                    @foreach ($pertanyaan['options'] as $option)
                                                        <option value="{{ $option }}">{{ $option }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    @endforeach
                                </form>

                                <!-- Tombol Back & Submit -->
                                <div class="mt-4 d-flex justify-content-between gap-2">
                                    <button type="button" class="btn btn-secondary w-auto px-4"
                                        @click="step = 1">Kembali</button>
                                    <button type="submit" class="btn btn-success w-auto px-4">Kirim
                                        Permohonan</button>
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
