<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>eform ppid</title>
    @vite(['resources/css/app.css', 'resources/js/timeline.js'])
</head>

<body>
    @include('layouts.header')
    @include('layouts.navbar')


    <section class="hero-section-ef"
        style="background: url('{{ asset('img/hero-grapol.svg') }}') no-repeat center center/cover; color: #fff; text-align: left; height: 40vh; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 0 20px;">
        <div class="container">
            <h1 class="display-4">Formulir Pernyataan Keberatan</h1>
            <p class="lead">E-Form Pengajuan Keberatan atas Permohonan Informasi di Lingkungan Politeknik Negeri
                Malang
                <br>Pengajuan Keberatan Dapat Dilakukan oleh Diri Sendiri atau Atas Permohonan Orang Lain
            </p>
        </div>
    </section>
    <section class="content-section my-5 px-4">
        <div class="container">
            <h2 class="text-center mb-2 fw-bold">Mekanisme Pengajuan Keberatan Informasi</h2>
            <section class="container-fluid">
                <div class="timeline position-relative mb-5" x-data="timeline">
                    <template x-for="(step, index) in steps" :key="index">
                        <div class="timeline-item" :class="[step.position, show ? 'show' : '']" x-data="{ show: false }"
                            x-intersect.once="setTimeout(() => show = true, index * 200)">
                            <div class="timeline-content">
                                <div class="timeline-number" x-text="step.number"></div>
                                <p x-text="step.text"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            <section class="document-section my-4">
                <a href="{{ asset('files/SOP_Pengajuan_Informasi.pdf') }}" target="_blank"
                    class="d-flex align-items-center text-dark text-decoration-none">
                    <i class="bi-book fs-4 me-2"></i>
                    <span class="fw-bold link-text">Dokumen SOP Pengajuan Permohonan Informasi</span>
                </a>
            </section>

            <h2 class="text-center mb-2 fw-bold">Ketentuan Pengajuan Pertanyaan Keberatan Informasi</h2>

            <div class="row align-items-center mb-5">
                <div class="col-md-8">
                    <div class="ketentuan-list">
                        <ol>
                            <li class="mb-3">Pemohon wajib masuk menggunakan akun yang terdaftar.</li>

                            <li class="mb-3">
                                Pemohon mengajukan pernyataan keberatan secara tertulis paling lambat 30 hari kerja
                                setelah ditemukannya alasan-alasan sebagai berikut:
                                <ul class="mt-2">
                                    <li>penolakan atas permintaan informasi berdasarkan alasan pengecualian</li>
                                    <li>tidak disediakannya informasi berkala</li>
                                    <li>tidak ditanggapinya permintaan informasi</li>
                                    <li>permintaan informasi ditanggapi tidak sebagaimana yang diminta</li>
                                    <li>tidak dipenuhinya permintaan informasi</li>
                                    <li>pengenaan biaya yang tidak wajar</li>
                                    <li>penyampaian informasi yang melebihi waktu yang ditentukan oleh Undang-Undang
                                    </li>
                                </ul>
                            </li>

                            <li class="mb-3">Permohonan keberatan dapat dilakukan di meja layanan informasi secara
                                lisan
                                maupun tertulis atau formulir online berikut.</li>
                        </ol>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="illustration-placeholder">
                        <img src="img/ilustrasi.png" alt="Ilustrasi Pengajuan Keberatan" class="img-fluid">
                    </div>
                </div>
            </div>

            <!-- Ajukan Pengaduan Section -->
            <div class="ajukan-pengaduan">
                <h3 class="mb-0">Ajukan Pengaduan</h3>
                <button class="masuk-button" onclick="window.location.href='{{ route('e-form') }}'">Klik form
                    ini</button>
            </div>
        </div>
        </div>
    </section>


    @include('layouts.footer')
</body>
<script src="https://cdn.lordicon.com/lordicon.js"></script>

</html>
