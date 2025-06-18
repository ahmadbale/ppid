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
    @include('user::layouts.header')
    @include('user::layouts.navbar')

    <div id="header"></div>
    <div id="nav-bar"></div>
    <!-- Hero Section -->
    <section class="hero-section mt-5" x-data="heroSlider()" x-init="startSlider()">
        <div class="custom-slider">
            @if (!empty($heroSectionMenus))
                @php
                    // Urutkan array media berdasarkan ID, dengan ID 1 sebagai prioritas pertama
                    $sortedMedia = collect($heroSectionMenus[0]['media'])
                        ->sortBy(function ($media) {
                            return $media['id'] == 1 ? 0 : $media['id'];
                        })
                        ->values()
                        ->toArray();
                @endphp

                @foreach ($sortedMedia as $index => $media)
                    <div class="custom-slide {{ $index == 0 ? 'active' : '' }}">
                        <img src="{{ $media['url'] }}" alt="Hero Slide {{ $index + 1 }}">

                        @if ($index == 0)
                            <div class="hero-content">
                                <h1>Selamat Datang di Laman PPID<br>Politeknik Negeri Malang</h1>
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="custom-slide active">
                    <div class="overlay"></div>
                    <img src="{{ asset('img/default-hero.webp') }}" alt="Default Hero Image">
                    <div class="hero-content">
                        <h1>Selamat Datang di Laman PPID<br>Politeknik Negeri Malang</h1>
                    </div>
                </div>
            @endif
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
                        {{-- {{ $pengantar['content'] }} --}}
                        Politeknik Negeri Malang (Polinema) berkomitmen untuk mewujudkan transparansi dan akuntabilitas publik sesuai dengan amanat Undang-Undang Nomor 14 Tahun 2008. Melalui Pejabat Pengelola Informasi dan Dokumentasi (PPID), Polinema menyediakan akases mudah bagi masyarakat terhadap berbagai informasi terkait kegiatan akademik, penelitian, keuangan dan pengelolaan kampus. Selain itu PPID Polinema siap membantu anda dalam mengajukan permohonan informasi, menyampaikan pengaduan, atau sekadar mencari tahu lebih lanjut tentang Polinema.
                    </p>
                </div>
                <div class="col-md-6 text-center"> 
                    <img src="{{ asset('img/direktur-polinema-bendera.webp') }}" alt="gambar-pengantar" class="pengantar-img">
                    {{-- <img src="{{ $pengantar['image'] }}" alt="gambar-pengantar" class="pengantar-img img-fluid"> --}}
                </div>
            </div>
        </div>
    </section>

     <!-- Akses Menu Cepat -->
     <section class="akses-menu-cepat" style="background-color: #ffffff; margin: 100px 0">
        <div class="container text-center" style="margin: 50px auto;">
            <h3 class="title-section">
                {{ $aksesCepatMenus[0]['title'] ?? 'Akses Menu Cepat' }}
            </h3>
            <div class="mt-4 border-top border-1 pt-3 mb-4 w-65 mx-auto"></div>
            <div class="row justify-content-center mt-4">
                <div class="menu-container">
                    <div class="menu-row">
                        @foreach ($aksesCepatMenus as $aksesCepat)
                            @foreach ($aksesCepat['menu'] as $menu)
                                <div class="menu-item"
                                    @if (isset($menu['route'])) onclick="window.location.href='{{ $menu['route'] }}'"
                                @endif>
                                    <div class="icon-wrapper">
                                        <img src="{{ $menu['static_icon'] }}" class="icon static" alt="{{ $menu['name'] }}">
                                        <img src="{{ $menu['animation_icon'] }}" class="icon animated" alt="{{ $menu['name'] }}">
                                    </div>
                                    <p class="menu-label">{{ $menu['name'] }}</p>
                                </div>
                            @endforeach
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
                <button class="masuk-button" onclick="window.location.href='{{ route('login-ppid') }}'">Ajukan
                    Permohonan</button>
            </div>
            <div class="masuktamu-tagline">
                <h2>KARENA SEMUA BERHAK UNTUK TAHU</h2>
                <p>#KETERBUKAANINFORMASIPUBLIK</p>
            </div>
        </div>
    </section>

    {{-- Stats section --}}
{{-- Stats section --}}
<section class="statistik-section py-5" x-data="statistikCounter">
    <div class="container">
        <h3 class="title-section" style="color: white;">Statistik Pelayanan PPID Polinema</h3>

        @if(!empty($statisticData['periode']))
            <p class="info-text">Dalam periode <strong>{{ $statisticData['periode']['tahun'] }}</strong> telah melayani sebanyak:</p>
            <div class="statistik-row">
                <div class="statistik-item">
                    <h3 class="counter" x-text="counts[0]" x-init="targets[0] = {{ $statisticData['periode']['pengajuan_total'] }}">0</h3>
                    <p>Total Pengajuan</p>
                </div>
                <div class="statistik-item">
                    <h3 class="counter" x-text="counts[1]" x-init="targets[1] = {{ $statisticData['periode']['pengajuan_diterima'] }}">0</h3>
                    <p>Pengajuan Diterima</p>
                </div>
                <div class="statistik-item">
                    <h3 class="counter" x-text="counts[2]" x-init="targets[2] = {{ $statisticData['periode']['pengajuan_ditolak'] }}">0</h3>
                    <p>Pengajuan Ditolak</p>
                </div>
            </div>

            @if(!empty($statisticData['jenis_kasus']))
                <p class="info-text">Dengan kasus sebanyak:</p>
                <div class="statistik-row">
                    <div class="statistik-item">
                        <h3 class="counter" x-text="counts[3]" x-init="targets[3] = {{ $statisticData['jenis_kasus']['permohonan_informasi'] }}">0</h3>
                        <p>Permohonan Informasi</p>
                    </div>
                    <div class="statistik-item">
                        <h3 class="counter" x-text="counts[4]" x-init="targets[4] = {{ $statisticData['jenis_kasus']['aduan_masyarakat'] }}">0</h3>
                        <p>Aduan Masyarakat</p>
                    </div>
                    <div class="statistik-item">
                        <h3 class="counter" x-text="counts[5]" x-init="targets[5] = {{ $statisticData['jenis_kasus']['pernyataan_keberatan'] }}">0</h3>
                        <p>Pernyataan Keberatan</p>
                    </div>
                    <div class="statistik-item">
                        <h3 class="counter" x-text="counts[6]" x-init="targets[6] = {{ $statisticData['jenis_kasus']['whistle_blowing_system'] }}">0</h3>
                        <p>Whistle Blowing System (WBS)</p>
                    </div>
                    <div class="statistik-item">
                        <h3 class="counter" x-text="counts[7]" x-init="targets[7] = {{ $statisticData['jenis_kasus']['permohonan_pemeliharaan'] }}">0</h3>
                        <p>Pemeliharaan Sarana Prasarana</p>
                    </div>
                </div>
            @endif
        @else
            <div class="text-center text-white">
                <p>Data statistik belum tersedia</p>
            </div>
        @endif
    </div>
</section>

     {{-- DOKUMENTASI --}}
     <section class="dokumentasi-section py-3 py-md-5">
        <div class="container py-3 py-md-5">
            <h3 class="title-section-dokumentasi text-white text-center">
                {{ $dokumentasiMenus[0]['title'] ?? 'Dokumentasi PPID' }}
            </h3>
            <div class="mt-4 border-top border-1 pt-3 mb-4 w-75 w-md-50 mx-auto"></div>

            <!-- Carousel untuk Desktop (2 gambar per slide) - Hanya tampil di layar medium ke atas -->
            <div id="carouselDesktop" class="carousel slide carousel-container d-none d-md-block"
                data-bs-ride="carousel">
                <!-- Tombol Navigasi Desktop -->
                 <button class="carousel-control-prev" type="button" data-bs-target="#carouselDesktop"
                    data-bs-slide="prev">
                    <i class="bi bi-caret-left-fill icon-large"></i>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselDesktop"
                    data-bs-slide="next">
                    <i class="bi bi-caret-right-fill icon-large"></i>
                </button>

                <div class="carousel-inner">
                    @if(!empty($dokumentasiMenus[0]['media']))
                        @foreach (array_chunk($dokumentasiMenus[0]['media'], 2) as $index => $chunk)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <div class="row d-flex justify-content-center">
                                    @foreach ($chunk as $item)
                                        <div class="col-6 text-center">
                                            <img src="{{ $item['url'] }}" class="img-fluid" alt="Dokumentasi">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Carousel untuk Mobile (1 gambar per slide) - Hanya tampil di layar kecil -->
            <div id="carouselMobile" class="carousel slide carousel-container d-block d-md-none"
                data-bs-ride="carousel">
                <!-- Tombol Navigasi Mobile -->
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselMobile"
                    data-bs-slide="prev">
                    <i class="bi bi-caret-left-fill icon-large"></i>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselMobile"
                    data-bs-slide="next">
                    <i class="bi bi-caret-right-fill icon-large"></i>
                </button>

                <div class="carousel-inner">
                    @if(!empty($dokumentasiMenus[0]['media']))
                        @foreach ($dokumentasiMenus[0]['media'] as $index => $item)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <div class="row d-flex justify-content-center">
                                    <div class="col-10 text-center">
                                        <img src="{{ $item['url'] }}" class="img-fluid" alt="Dokumentasi">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Pengumuman --}}
    <section class="container mt-4 py-5">
        @php
        $item = $pengumumanMenus[0] ?? null;
        @endphp

        @if($item)
            <h3 class="title-section">{{ $item['kategoriSubmenu'] ?? 'Pengumuman' }}</h3>
        @else
            <h3 class="title-section">Pengumuman</h3>
        @endif

        <div class="mt-4 border-top border-1 pt-3 mb-4 w-65 mx-auto"></div>

        @if(count($pengumumanMenus) > 0)
            <div class="row row-cols-1 row-cols-md-3 g-4">
                @foreach ($pengumumanMenus as $index => $item)
                    <div class="col">
                        <div class="ppid-card-body mb-5">
                            <div class="ppid-card-header">
                                <img src="{{ $item['thumbnail'] ? asset($item['thumbnail']) : asset(env('BASE_URL').'/img/default.webp') }}"
                                    class="ppid-img-cover" alt="{{ $item['judul'] }}">
                            </div>
                            <p class="ppid-date text-muted">{{ \Carbon\Carbon::parse($item['created_at'])->format('d M Y') }}</p>
                            <p class="ppid-card-text">{{ $item['judul'] }}</p>
                            <p class="ppid-card-description px-1">{{ $item['deskripsi'] }}</p>
                            <a class="btn btn-primary" href="{{ url($item['slug']) }}" role="button">Baca Artikel</a>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($item)
            <div class="d-flex flex-wrap justify-content-center">
                <a href="{{ url($item['url_selengkapnya']) }}" class="btn-custom">
                    <i class="bi bi-arrow-right"></i>
                    <span class="ms-2">Pengumuman PPID Lainnya</span>
                </a>
            </div>
            @endif
        @else
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                        <div class="ppid-card-body mb-5">
                            <div class="ppid-card-header">
                                <img src="{{ asset('storage/default_icons/no_image_available.svg') }}"
                                    class="ppid-img-cover" alt="gambar-default">
                            </div>
                            <p class="ppid-date text-muted"></p>
                            <p class="ppid-card-text"></p>
                            <p class="ppid-card-description px-1"></p>
                            <a class="btn btn-primary" href="" role="button">Baca Artikel</a>
                        </div>
                    </div>
                </div>
        @endif
    </section>

    <section class="container mt-4 py-5">
    @php
    $itemBerita = $beritaMenus[0] ?? null;
    @endphp
    <div class="row gy-4">
    <div class="col-md-8">
    @if($itemBerita)
        <h3 class="title-section">{{ $itemBerita['kategori'] ?? 'Berita' }}</h3>
    @else
        <h3 class="title-section">Berita</h3>
    @endif

    <div class="mt-4 border-top border-1 pt-3 w-70 mx-auto"></div>

    @if(count($beritaMenus) > 0)

                @foreach ($beritaMenus as $index => $itemBerita)
                    <div class="news-item">
                        <h5>{{ $itemBerita['judul'] ?? 'Tanpa Judul' }}</h5>
                        <p>{{ $itemBerita['deskripsiThumbnail'] ?? '' }}</p>
                        @php
    // Enkripsi berita_id dan lakukan URL encode untuk menghindari karakter khusus
    $encryptedId = urlencode(\Illuminate\Support\Facades\Crypt::encryptString($itemBerita['berita_id'] ?? ''));
@endphp
<a href="{{ url('berita-detail'.'/'.$itemBerita['slug'].'/'.$encryptedId) }}" 
                        class="read-more d-flex flex-wrap justify-content-end">
                            Berita selengkapnya →
                        </a>
                    </div>
                @endforeach

                
                    <div class="d-flex flex-wrap justify-content-center mt-3 mb-3">
                        <a href='{{ route('berita') }}' class="btn-custom">
                            <i class="bi bi-arrow-right"></i>
                            <span class="ms-2">Berita Lainnya</span>
                        </a>
                    </div>
            </div>
    @else
        <p class="text-center">Tidak ada berita tersedia.</p>
    @endif
            <!-- Bagian Media I nformasi Publik -->
            <div class="col-md-4">
                <h3 class="title-section">
                    {{ $mediaInformasiPublikMenus[0]['title'] ?? 'Media Informasi Publik' }}
                </h3>
                <div class="mt-4 border-top border-1 pt-3 w-30 mx-auto"></div>

                @if(!empty($mediaInformasiPublikMenus[0]['media']))
                    @foreach ($mediaInformasiPublikMenus[0]['media'] as $item)
                        <div class="video-container">
                            <iframe width="100%" height="200" src="{{ $item['url'] }}" frameborder="0" allowfullscreen></iframe>
                            <div class="text-white text-center p-2">{{ $item['title'] }}</div>
                        </div>
                    @endforeach
                @endif


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
        <h3 class="title-section-pintasan fw-bold text-white text-left px-5">
            {{ $pintasanMenus[0]['kategori_judul'] ?? 'Pintasan Lainnya' }}  <i class="bi bi-link-45deg"></i>
        </h3>
        <div class="row mt-4 px-5 text-center">
            @if (!empty($pintasanMenus))
                @foreach ($pintasanMenus as $pintasan)
                    <div class="col-md-3 mb-4 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
                        <h5 class="fw-bold mb-4">{{ $pintasan['title'] }}</h5>
                        <ul class="list-unstyled">
                            @foreach ($pintasan['menu'] as $menu)
                                <li class="mb-3">
                                    <a href="{{ $menu['route'] }}" class="text-pintasan text-decoration-none" target="_blank">
                                        {{ $menu['name'] }}
                                        <div class="border-top border-1 mt-1  border-pintasan"></div>
                                    </a>

                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @else
                <p class="text-white text-center">Tidak ada data pintasan yang tersedia.</p>
            @endif
        </div>
    </section>

</body>
<footer>
    @include('user::layouts.footer')
</footer>

</html>
