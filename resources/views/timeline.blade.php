<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('layouts.header')
    @include('layouts.navbar')


    <section class="hero-section-ef" style="background: url('{{ asset('img/hero-grapol.svg') }}') no-repeat center center/cover; color: #fff; text-align: left; height: 40vh; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 0 20px;">
        <div class="container">
            <h1 class="display-4">Formulir Pernyataan Keberatan</h1>
            <p class="lead">E-Form Pengajuan Keberatan atas Permohonan Informasi di Lingkungan Politeknik Negeri Malang
                <br>Pengajuan Keberatan Dapat Dilakukan oleh Diri Sendiri atau Atas Permohonan Orang Lain
            </p>
        </div>
    </section>
    <section class="content-section my-5 px-4">
        <div class="container-fluid">
            <h2 class="text-center mb-5 fw-bold" data-aos="fade-up">Mekanisme Pengajuan Keberatan Informasi</h2>
            <div class="timeline position-relative mb-5">
                <div class="timeline-item right" data-aos="fade-left" data-aos-duration="1000">
                    <div class="timeline-content">
                        <div class="timeline-number">1</div>
                        <p>Pemohon mengajukan keberatan melalui formulir yang tersedia</p>
                    </div>
                </div>

                <div class="timeline-item left" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="200">
                    <div class="timeline-content">
                        <div class="timeline-number">2</div>
                        <p>Petugas PPID Seksi Aduan Masyarakat menerima dan mencatat permohonan keberatan</p>
                    </div>
                </div>

                <div class="timeline-item right" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="400">
                    <div class="timeline-content">
                        <div class="timeline-number">3</div>
                        <p>Seksi Aduan Masyarakat meneruskan permohonan keberatan kepada PPID Pusat untuk ditindaklanjuti</p>
                    </div>
                </div>

                <div class="timeline-item left" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="600">
                    <div class="timeline-content">
                        <div class="timeline-number">4</div>
                        <p>PPID Pusat menyampaikan permohonan keberatan kepada atasan</p>
                    </div>
                </div>

                <div class="timeline-item right" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="800">
                    <div class="timeline-content">
                        <div class="timeline-number">5</div>
                        <p>Atasan PPID pusat menentukan untuk menggugurkan atau menyetujui keputusan PPID</p>
                    </div>
                </div>

                <div class="timeline-item left" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="1000">
                    <div class="timeline-content">
                        <div class="timeline-number">6</div>
                        <p>Keputusan akhir mengenai keberatan disampaikan secara tertulis kepada pemohon</p>
                    </div>
                </div>
            </div>

            <section class="document-section my-4">
                <a href="{{ asset('files/SOP_Pengajuan_Informasi.pdf') }}" target="_blank" class="d-flex align-items-center text-dark text-decoration-none">
                    <i class="bi-book fs-4 me-2"></i>
                    <span class="fw-bold link-text">Dokumen SOP Pengajuan Permohonan Informasi</span>
                </a>
            </section>

            <h2 class="text-center mb-5 fw-bold" data-aos="fade-up">Ketentuan Pengajuan Pertanyaan Keberatan Informasi</h2>

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

                            <li class="mb-3">Permohonan keberatan dapat dilakukan di meja layanan informasi secara lisan
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
                <button class="masuk-button" onclick="window.location.href='{{ route('e-form') }}'">Klik form ini</button>
            </div>
            </div>
        </div>
    </section>

{{-- <lord-icon
    src="https://cdn.lordicon.com/warimioc.json"
    trigger="hover"
    colors="primary:#3080e8,secondary:#f4c89c"
    style="width:250px;height:250px">
</lord-icon> --}}
@include('layouts.footer')
</body>
<script src="https://cdn.lordicon.com/lordicon.js"></script>
</html>
