<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PPID Polinema</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>
    @include('layouts.header')
    @include('layouts.navbar')

    <div id="header"></div>
    <div id="nav-bar"></div>
    <!-- Hero Section -->
    <section class="hero-section" x-data="heroSlider()" x-init="startSlider()">
        <div class="custom-slider">
            @foreach($heroSlides as $index => $slide)
            <div class="custom-slide {{ $index == 0 ? 'active' : '' }}">
                @if($index == 0)
                    <div class="overlay"></div>
                @endif
            {{-- <div class="custom-slide active"> --}}
                {{-- <div class="overlay"></div> --}}
                <img src="{{ $slide['image'] }}" alt="Hero Slide {{ $index + 1 }}">
                <img src="{{ asset('img/grapol.webp') }}" alt="Politeknik Negeri Malang 1">
                {{-- <div class="hero-content">
                    <h1>Selamat Datang di Laman PPID<br>Politeknik Negeri Malang</h1>
                </div> --}}
                @if($slide['title'])
                    <div class="hero-content">
                        <h1>{!! $slide['title'] !!}</h1>
                    </div>
                @endif
            {{-- </div> --}}
            {{-- <div class="custom-slide">
                <img src="{{ asset('img/maklumat-ppid.webp') }}" alt="Maklumat Pelayanan Publik">
            </div>
            <div class="custom-slide">
                <img src="{{ asset('img/jadwal-pelayanan-informasi-publik.webp') }}" alt="Jadwal Pelayanan Informasi Publik">
            </div> --}}
            </div>
            @endforeach
        </div>
    </section>

    <!-- Pengantar Section -->
    <section class="pengantar-section">
        <h3 class="title-section">PPID Politeknik Negeri Malang</h3>
        <div class="mt-4 border-top border-1 pt-3 mb-4 w-65 mx-auto"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p>
                        {{-- Politeknik Negeri Malang (Polinema) berkomitmen untuk mewujudkan transparansi dan akuntabilitas
                        publik sesuai dengan amanat Undang-Undang Nomor 14 Tahun 2008. Melalui Pejabat Pengelola
                        Informasi dan Dokumentasi (PPID), Polinema menyediakan akses mudah bagi masyarakat terhadap
                        berbagai informasi terkait kegiatan akademik, penelitian, keuangan, dan pengelolaan kampus.
                        Selain itu, PPID Polinema siap membantu Anda dalam mengajukan permohonan informasi, menyampaikan
                        pengaduan, atau sekadar mencari tahu lebih lanjut tentang Polinema. --}}
                        {{ $pengantar['content'] }}
                    </p>
                </div>
                <div class="col-md-6 text-center">
                    {{-- <img src="{{ asset('img/direktur-polinema-bendera.webp') }}" alt="gambar-pengantar" class="pengantar-img"> --}}
                    <img src="{{ $pengantar['image'] }}" alt="gambar-pengantar" class="pengantar-img img-fluid" >
                </div>
            </div>
        </div>
    </section>

    <!-- Akses Menu Cepat -->
    <section class="akses-menu-cepat" style="background-color: #ffffff; margin: 100px 0">
        <div class="container text-center" style=" margin: 50px auto; ">
            <h3 class="title-section">Akses Menu Cepat</h3>
            <div class="mt-4 border-top border-1 pt-3 mb-4 w-65 mx-auto"></div>
            <div class="row justify-content-center mt-4">
                <div class="menu-container">
                    <div class="menu-row">
                        <div class="menu-item"
                            onclick="window.location.href='{{ route('informasi-publik.setiap-saat') }}'">
                            <div class="icon-wrapper">
                                <img src="{{ asset('img/24-hours.svg') }}" class="icon static"
                                    alt="Informasi Setiap Saat">
                                <img src="{{ asset('img/24-hours.gif') }}" class="icon animated"
                                    alt="Informasi Setiap Saat">
                            </div>
                            <p class="menu-label">Informasi Setiap Saat</p>
                        </div>
                        <div class="menu-item" onclick="window.location.href='{{ route('informasi-publik.berkala') }}'">
                            <div class="icon-wrapper">
                                <img src="{{ asset('img/callendar.svg') }}" class="icon static" alt="Informasi Berkala">
                                <img src="{{ asset('img/calendar-ez.gif') }}" class="icon animated"
                                    alt="Informasi Berkala">
                            </div>
                            <p class="menu-label">Informasi</br>Berkala</p>
                        </div>
                        <div class="menu-item"
                            onclick="window.location.href='{{ route('informasi-publik.serta-merta') }}'">
                            <div class="icon-wrapper">
                                <img src="{{ asset('img/website.svg') }}" class="icon static"
                                    alt="Informasi Serta Merta">
                                <img src="{{ asset('img/website.gif') }}" class="icon animated"
                                    alt="Informasi Serta Merta">
                            </div>
                            <p class="menu-label">Informasi Serta Merta</p>
                        </div>
                        <div class="menu-item" onclick="window.location.href='{{ route('permohonan.lacak') }}'">
                            <div class="icon-wrapper">
                                <img src="{{ asset('img/email.svg') }}" class="icon static" alt="Lacak Permohonan">
                                <img src="{{ asset('img/email.gif') }}" class="icon animated" alt="Lacak Permohonan">
                            </div>
                            <p class="menu-label">Lacak Permohonan</p>
                        </div>
                        <div class="menu-item" onclick="window.open('https://helpakademik.polinema.ac.id/', '_blank')">
                            <div class="icon-wrapper">
                                <img src="{{ asset('img/mba-helpdesk.svg') }}" class="icon static"
                                    alt="HELPDESK Akademik">
                                <img src="{{ asset('img/helpdesk.gif') }}" class="icon animated"
                                    alt="HELPDESK Akademik">
                            </div>
                            <p class="menu-label">HELPDESK Akademik</p>
                        </div>
                        <div class="menu-item" onclick="window.open('https://www.lapor.go.id/', '_blank')">
                            <div class="icon-wrapper">
                                <img src="{{ asset('img/laporPANRB.svg') }}" class="icon static" alt="Lapor PAN RB">
                                <img src="{{ asset('img/laporPANRB.svg') }}" class="icon animated" alt="Lapor PAN RB">
                            </div>
                            <p class="menu-label">Lapor PAN RB</p>
                        </div>
                        <div class="menu-item" onclick="window.open('https://kemdikbud.lapor.go.id/', '_blank')">
                            <div class="icon-wrapper">
                                <img src="{{ asset('img/laporKemdikbud.svg') }}" class="icon static"
                                    alt="Lapor KEMDIKBUD">
                                <img src="{{ asset('img/laporKemdikbud.svg') }}" class="icon animated"
                                    alt="Lapor KEMDIKBUD">
                            </div>
                            <p class="menu-label">Lapor KEMDIKBUD</p>
                        </div>
                    </div>
                </div>
    </section>

    {{-- CTA Login/regist --}}
    <section class="masuktamu-section" style="background: url('img/gedung-sipil.webp') center/cover no-repeat;">
        <div class="overlay"></div>
        <div class="masuktamu-container">
            <div class="masuktamu-text">
                <h2>MASUK BUKU TAMU</h2>
                <p>Akses lebih mudah dan menyeluruh dalam permohonan serta pelaporan pada PPID Polinema</p>
                <button class="masuk-button" onclick="window.location.href='{{ route('login') }}'">Ajukan
                    Permohonan</button>
            </div>
            <div class="masuktamu-tagline">
                <h2>KARENA SEMUA BERHAK UNTUK TAHU</h2>
                <p>#KETERBUKAANINFORMASIPUBLIK</p>
            </div>
        </div>
    </section>

    {{-- Stats section --}}
    <section class="statistik-section py-5" x-data="statistikCounter">
        <div class="container">
            <h3 class="title-section" style="color: white;">Statistik Pelayanan PPID Polinema</h3>
            <p class="info-text">Dalam periode <strong>2023/2024</strong> telah melayani sebanyak:</p>
            <div class="statistik-row">
                <div class="statistik-item">
                    <h3 class="counter" x-text="counts[0]"></h3>
                    <p>Pengajuan Permohonan</p>
                </div>
                <div class="statistik-item">
                    <h3 class="counter" x-text="counts[1]"></h3>
                    <p>Permohonan Diterima</p>
                </div>
                <div class="statistik-item">
                    <h3 class="counter" x-text="counts[2]"></h3>
                    <p>Permohonan Ditolak</p>
                </div>
            </div>
            <p class="info-text">Dengan kasus sebanyak:</p>
            <div class="statistik-row">
                <div class="statistik-item">
                    <h3 class="counter" x-text="counts[3]"></h3>
                    <p>Permohonan Informasi</p>
                </div>
                <div class="statistik-item">
                    <h3 class="counter" x-text="counts[4]"></h3>
                    <p>Aduan Masyarakat</p>
                </div>
                <div class="statistik-item">
                    <h3 class="counter" x-text="counts[5]"></h3>
                    <p>Pernyataan Keberatan</p>
                </div>
                <div class="statistik-item">
                    <h3 class="counter" x-text="counts[6]"></h3>
                    <p>Whistle Blowing System (WBS)</p>
                </div>
                <div class="statistik-item">
                    <h3 class="counter" x-text="counts[7]"></h3>
                    <p>Pemeliharaan Sarana Prasarana</p>
                </div>
            </div>
        </div>
    </section>

    {{-- DOKUMENTASI --}}
    <section class="dokumentasi-section py-5">
        <div class="container py-5">
        <h3 class="title-section-dokumentasi text-white text-center">Dokumentasi PPID</h3>
        <div class="mt-4 border-top border-1 pt-3 mb-4 w-50 mx-auto"></div>


        <div id="carouselExample" class="carousel slide carousel-container" data-bs-ride="carousel">
            <!-- Tombol Navigasi -->
            <button class="carousel-control-prev " type="button" data-bs-target="#carouselExample"
                data-bs-slide="prev">
                <i class="bi bi-caret-left-fill icon-large"></i>
            </button>
            <button class="carousel-control-next " type="button" data-bs-target="#carouselExample"
                data-bs-slide="next">
                <i class="bi bi-caret-right-fill icon-large"></i>
            </button>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="row d-flex justify-content-center">
                        <div class="col-6 text-center">
                            <img src="{{ asset('img/dokumentasi-1.webp') }}" class="img-fluid" alt="Gambar 1">
                        </div>
                        <div class="col-6 text-center">
                            <img src="{{ asset('img/dokumentasi-2.webp') }}" class="img-fluid" alt="Gambar 2">
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="row d-flex justify-content-center">
                        <div class="col-6 text-center">
                            <img src="{{ asset('img/dokumentasi-3.webp') }}" class="img-fluid" alt="Gambar 3">
                        </div>
                        <div class="col-6 text-center">
                            <img src="{{ asset('img/dokumentasi-1.svg') }}" class="img-fluid" alt="Gambar 4">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>


    {{-- pengumuman ppid --}}
    <section class="container mt-4 py-5">
        <h3 class="title-section">Pengumuman PPID</h3>
        <div class="mt-4 border-top border-1 pt-3 mb-4 w-65 mx-auto"></div>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="ppid-card-body">
                    <div class="ppid-card-header">
                        <img src="{{ asset('img/dokumentasi-1.webp') }}" class="ppid-img-cover" alt="Gambar 1">
                    </div>
                    <p class="ppid-date">1 Februari 2025</p>
                    <p class="ppid-card-text">Deskripsi/isi pengumuman</p>
                    <a class="btn btn-primary" href="#" role="button">Baca Artikel</a>
                </div>
            </div>
            <div class="col">
                <div class="ppid-card-body">
                    <div class="ppid-card-header">
                        <img src="{{ asset('img/dokumentasi-2.webp') }}" class="ppid-img-cover" alt="Gambar 2">
                    </div>
                    <p class="ppid-date">30 Januari 2025</p>
                    <p class="ppid-card-text">Deskripsi/isi pengumuman</p>
                    <a class="btn btn-primary" href="#" role="button">Baca Artikel</a>
                </div>
            </div>
            <div class="col">
                <div class="ppid-card-body">
                    <div class="ppid-card-header">
                        <img src="{{ asset('img/dokumentasi-3.webp') }}" class="ppid-img-cover" alt="Gambar 3">
                    </div>
                    <p class="ppid-date">25 Januari 2025</p>
                    <p class="ppid-card-text">Deskripsi/isi pengumuman</p>
                    <a class="btn btn-primary" href="#" role="button">Baca Artikel</a>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-center mt-3">
            <a href="#" class="btn-custom">
                <i class="bi bi-arrow-right"></i>
                <span class="ms-2">Pengumuman PPID Lainnya</span>
            </a>
        </div>
    </section>


    <section class="container mt-4 py-5">
        <div class="row">
            <!-- Bagian Berita PPID -->
            <div class="col-md-8">
                <h3 class="title-section">Berita PPID</h3>
                <div class="mt-4 border-top border-1 pt-3 w-70 mx-auto"></div>

                <div class="news-item">
                    <h5>Sosialisasi Layanan Informasi Publik Polinema, Meningkatkan Keterbukaan dan Aksesibilitas
                        Informasi untuk Semua</h5>
                    <p>Malang, 17 Oktober 2024 - Politeknik Negeri Malang (Polinema) hari ini menggelar kegiatan
                        “Sosialisasi Layanan Informasi Publik Polinema Pejabat Pengelola Informasi dan Dokumentasi”.
                        Acara yang berlangsung di Ruang Rapim Gedung AA lantai 2 ini diadakan secara hybrid ..</p>
                    <a href="#" class="read-more d-flex flex-wrap justify-content-end">selengkapnya →</a>
                </div>
                <div class="news-item">
                    <h5>Kegiatan Sosialisasi Pejabat Pengelola Informasi Dan Dokumentasi Oleh Pimpinan Dan Jajarannya
                    </h5>
                    <p>Keterbukaan Informasi sangat diperlukan pada era digital saat ini. Oleh karena itu, melalui
                        Pejabat Pengelola Informasi dan Dokumentasi (PPID) Politeknik Negeri Malang memberikan pelayanan
                        informasi publik yang bersifat terbuka dan dapat diakses oleh masyarakat..</p>
                    <a href="#" class="read-more d-flex flex-wrap justify-content-end">selengkapnya →</a>
                </div>
                <div class="d-flex flex-wrap justify-content-center mt-3">
                    <a href="#" class="btn-custom">
                        <i class="bi bi-arrow-right"></i>
                        <span class="ms-2">Berita Lainnya</span>
                    </a>
                </div>
            </div>

            <!-- Bagian Media I nformasi Publik -->
            <div class="col-md-4">
                <h3 class="title-section">Media Informasi Publik</h3>
                <div class="mt-4 border-top border-1 pt-3 w-30 mx-auto"></div>
                <div class="video-container">
                    <iframe width="100%" height="200" src="https://www.youtube.com/embed/9vlRk9C37JE" frameborder="0" allowfullscreen></iframe>
                    <div class="text-white text-center p-2">Keterbukaan Informasi Publik</div>
                </div>
                <div class="d-flex flex-wrap justify-content-center mt-3">
                    <a href="#" class="btn-custom">
                        <i class="bi bi-arrow-right"></i>
                        <span class="ms-2">Media Lainnya</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- pintasan menu --}}
    <section class="container mt-4 mb-3 py-5 text-center">
        <h3 class="title-section">Pintasan Lainnya</h3>
        <div class="mt-4 border-top border-1 pt-3 mb-4 w-50 mx-auto"></div>

        <div class="d-flex flex-wrap justify-content-center gap-5 mb-4">
            <a class="btn btn-menu-pintasan " href="#" role="button">POLINEMA</a>
            <a class="btn btn-menu-pintasan " href="#" role="button">PORTAL</a>
            <a class="btn btn-menu-pintasan " href="#" role="button">SIAKAD</a>
            <a class="btn btn-menu-pintasan " href="#" role="button">SPMB</a>
        </div>
        <div class="d-flex flex-wrap justify-content-center gap-5">
            <a class="btn btn-menu-pintasan " href="#" role="button">P2M</a>
            <a class="btn btn-menu-pintasan " href="#" role="button">Jaminan Mutu</a>
            <a class="btn btn-menu-pintasan " href="#" role="button">LPSE KEMDIKBUD</a>
            <a class="btn btn-menu-pintasan " href="#" role="button">Alumni</a>
        </div>
    </section>

    @extends('layouts.footer')

</body>

</html>
