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
            @foreach ($heroSlides as $index => $slide)
                <div class="custom-slide {{ $index == 0 ? 'active' : '' }}">
                    @if ($index == 0)
                        <div class="overlay"></div>
                    @endif
                    {{-- <div class="custom-slide active"> --}}
                    {{-- <div class="overlay"></div> --}}
                    <img src="{{ $slide['image'] }}" alt="Hero Slide {{ $index + 1 }}">
                    <img src="{{ asset('img/grapol.webp') }}" alt="Politeknik Negeri Malang 1">
                    {{-- <div class="hero-content">
                    <h1>Selamat Datang di Laman PPID<br>Politeknik Negeri Malang</h1>
                </div> --}}
                    @if ($slide['title'])
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
                    <img src="{{ $pengantar['image'] }}" alt="gambar-pengantar" class="pengantar-img img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Akses Menu Cepat -->
    {{-- <section class="akses-menu-cepat" style="background-color: #ffffff; margin: 100px 0">
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
    </section> --}}

    <!-- Akses Menu Cepat -->
    <section class="akses-menu-cepat" style="background-color: #ffffff; margin: 100px 0">
        <div class="container text-center" style="margin: 50px auto;">
            <h3 class="title-section">Akses Menu Cepat</h3>
            <div class="mt-4 border-top border-1 pt-3 mb-4 w-65 mx-auto"></div>
            <div class="row justify-content-center mt-4">
                <div class="menu-container">
                    <div class="menu-row">
                        @foreach ($quickAccessMenus as $menu)
                            <div class="menu-item"
                                @if (isset($menu['route'])) onclick="window.location.href='{{ $menu['route'] }}'"
                            @elseif(isset($menu['url'])) onclick="window.open('{{ $menu['url'] }}', '_blank')" @endif>
                                <div class="icon-wrapper">
                                    <img src="{{ $menu['static_icon'] }}" class="icon static" alt="{{ $menu['name'] }}">
                                    <img src="{{ $menu['animated_icon'] }}" class="icon animated"
                                        alt="{{ $menu['name'] }}">
                                </div>
                                <p class="menu-label">{{ $menu['name'] }}</p>
                            </div>
                        @endforeach
                    </div>
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
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                    <i class="bi bi-caret-left-fill icon-large"></i>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                    <i class="bi bi-caret-right-fill icon-large"></i>
                </button>
    
                <div class="carousel-inner">
                    @foreach(array_chunk($dokumentasi, 2) as $index => $chunk)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="row d-flex justify-content-center">
                                @foreach($chunk as $item)
                                    <div class="col-6 text-center">
                                        <img src="{{ $item['dokumentasi'] }}" class="img-fluid" alt="Dokumentasi">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    


    {{-- Pengumuman --}}
    <section class="container mt-4 py-5">
        <h3 class="title-section">Pengumuman PPID</h3>
        <div class="mt-4 border-top border-1 pt-3 mb-4 w-65 mx-auto"></div>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            @foreach($pengumuman as $index => $item)
                <div class="col">
                    <div class="ppid-card-body mb-5">
                        <div class="ppid-card-header">
                            <img src="{{ asset('img/dokumentasi-' . ($index + 1) . '.webp') }}" class="ppid-img-cover" alt="Gambar {{ $index + 1 }}">
                        </div>
                        <p class="ppid-date">{{ $item['tanggal'] }}</p>
                        <p class="ppid-card-text">{{ $item['deskripsi'] }}</p>
                        <a class="btn btn-primary" href="{{ $item['route'] }}" role="button">Baca Artikel</a>
                    </div>
                </div>
            @endforeach
        </div>
        

        <div class="d-flex flex-wrap justify-content-center">
            <a href="#" class="btn-custom">
                <i class="bi bi-arrow-right"></i>
                <span class="ms-2">Pengumuman PPID Lainnya</span>
            </a>
        </div>
    </section>


    <section class="container mt-4 py-5">
        <div class="row gy-4">
            <!-- Bagian Berita PPID -->
            <div class="col-md-8">
                <h3 class="title-section">Berita PPID</h3>
                <div class="mt-4 border-top border-1 pt-3 w-70 mx-auto"></div>

                @foreach ($berita as $item)
                    <div class="news-item">
                        <h5>{{ $item['title'] }}</h5>
                        <p>{{ $item['deskripsi'] }}</p>
                        <a href="{{ $item['route'] }}" 
                            class="read-more d-flex flex-wrap justify-content-end">Berita selengkapnya →</a>
                    </div>
                @endforeach

                <div class="d-flex flex-wrap justify-content-center mt-3 mb-3">
                    <a href="#" class="btn-custom">
                        <i class="bi bi-arrow-right"></i>
                        <span class="ms-2">Berita Lainnya</span>
                    </a>
                </div>

            </div>

            <!-- Bagian Media I nformasi Publik -->
            <div class="col-md-4 ">
                <h3 class="title-section">Media Informasi Publik</h3>
                <div class="mt-4 border-top border-1 pt-3 w-30 mx-auto"></div>
                
                @foreach($media as $item)
                    <div class="video-container">
                        <iframe width="100%" height="200" src="{{ $item['link'] }}" frameborder="0" allowfullscreen></iframe>
                        <div class="text-white text-center p-2">{{ $item['title'] }}</div>
                    </div>
                @endforeach
            
                <div class="d-flex flex-wrap justify-content-center mt-3">
                    <a href="#" class="btn-custom">
                        <i class="bi bi-arrow-right"></i>
                        <span class="ms-2">Media Lainnya</span>
                    </a>
                </div>
            </div>
            
        </div>
    </section>

    {{-- Pintasan Lainnya --}}
    <section class="pintasan py-5">
        <h3 class="title-section fw-bold text-white text-center">
            Pintasan Lainnya <i class="bi bi-link-45deg"></i>
        </h3>
        <div class="row mt-4 text-center justify-content-center">
            @foreach (array_chunk($pintasanMenus, 4) as $column)
                <div class="col-md-3 d-flex justify-content-center">
                    <ul class="list-unstyled mb-3 text-center">
                        @foreach ($column as $menu)
                            <li>
                                <a href="{{ $menu['route'] }}" 
                                   class="text-white text-decoration-none d-block link-custom">
                                    {{ $menu['name'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
        
    </section>


    @extends('layouts.footer')

</body>

</html>
