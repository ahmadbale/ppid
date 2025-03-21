<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Berita PPID</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('user::layouts.header')
    @include('user::layouts.navbar')

    <section class="hero-section-ef"
        style="background: url('{{ asset('img/hero-grapol.svg') }}') no-repeat center center/cover; color: #fff; text-align: left; height: 40vh; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 0 20px;">
        <div class="container">
            <header>
                <h1 class="display-4 fw-bold">{{ $title }}</h1>
            </header>
        </div>
    </section>
    <section class="container mt-4 py-5">
        <div class="news-list">
            <article class="d-flex align-items-start m-5" style="gap: 20px;">
                <figure class="me-3" style="flex: 0 0 50%;">
                    <img src="{{ asset('img/berita-1.png') }}" class="img-berita" alt="Gambar Berita" style="width: 95%; height: auto;">
                </figure>
                <div>
                    <h4 class="fw-bold pt-0 pb-2">Polinema Tingkatkan Daya Saing UMKM Desa Duwet dengan Inovasi Teknologi Produksi dan Branding</h4>
                    <time class="text-muted pt-2 pb-2" datetime="2024-12-10">10 Desember 2024</time>
                    <p class="pt-2">Politeknik Negeri Malang (Polinema) melalui (PKM) melaksanakan kegiatan Peningkatan Daya Saing UMKM Desa Duwet dengan Inovasi ...</p>
                    <a class="read-moree" href="{{route ('berita-1')}}">Baca selengkapnya</a>
                </div>
            </article>
            <article class="d-flex align-items-start m-5" style="gap: 20px;">
                <figure class="me-3" style="flex: 0 0 50%;">
                    <img src="{{ asset('img/berita-1.png') }}" class="img-berita" alt="Gambar Berita" style="width: 95%; height: auto;">
                </figure>
                <div>
                    <h4 class="fw-bold pt-0 pb-2">Polinema Tingkatkan Daya Saing UMKM Desa Duwet dengan Inovasi Teknologi Produksi dan Branding</h4>
                    <time class="text-muted pt-2 pb-2" datetime="2024-12-10">10 Desember 2024</time>
                    <p class="pt-2">Politeknik Negeri Malang (Polinema) melalui  (PKM) melaksanakan kegiatan Peningkatan Daya Saing UMKM Desa Duwet dengan Inovasi ...</p>
                    <a class="read-moree" href="#">Baca selengkapnya</a>
                </div>
            </article>
            <article class="d-flex align-items-start m-5" style="gap: 20px;">
                <figure class="me-3" style="flex: 0 0 50%;">
                    <img src="{{ asset('img/berita-1.png') }}" class="img-berita" alt="Gambar Berita" style="width: 95%; height: auto;">
                </figure>
                <div>
                    <h4 class="fw-bold pt-0 pb-2">Polinema Tingkatkan Daya Saing UMKM Desa Duwet dengan Inovasi Teknologi Produksi dan Branding</h4>
                    <time class="text-muted pt-2 pb-2" datetime="2024-12-10">10 Desember 2024</time>
                    <p class="pt-2">Politeknik Negeri Malang (Polinema) melalui  (PKM) melaksanakan kegiatan Peningkatan Daya Saing UMKM Desa Duwet dengan Inovasi ...</p>
                    <a class="read-moree" href="#">Baca selengkapnya</a>
                </div>
            </article>
            <article class="d-flex align-items-start m-5" style="gap: 20px;">
                <figure class="me-3" style="flex: 0 0 50%;">
                    <img src="{{ asset('img/berita-1.png') }}" class="img-berita" alt="Gambar Berita" style="width: 95%; height: auto;">
                </figure>
                <div>
                    <h4 class="fw-bold pt-0 pb-2">Polinema Tingkatkan Daya Saing UMKM Desa Duwet dengan Inovasi Teknologi Produksi dan Branding</h4>
                    <time class="text-muted pt-2 pb-2" datetime="2024-12-10">10 Desember 2024</time>
                    <p class="pt-2">Politeknik Negeri Malang (Polinema) melalui  (PKM) melaksanakan kegiatan Peningkatan Daya Saing UMKM Desa Duwet dengan Inovasi ...</p>
                    <a class="read-moree" href="#">Baca selengkapnya</a>
                </div>
            </article>
        </div>
    </section>

        <div class="Pagination mb-4">
            <ul class="pagination pagination-rounded justify-content-center gap-2">
                <li class="page-item">
                    <a class="page-link" href="#" aria-label="Previous">
                        <span aria-hidden="true">&laquo; Prev</span>
                    </a>
                </li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item d-none d-md-inline"><a class="page-link" href="#">2</a></li>
                <li class="page-item d-none d-md-inline"><a class="page-link" href="#">3</a></li>
                <li class="page-item d-none d-md-inline"><a class="page-link" href="#">4</a></li>
                <li class="page-item d-none d-md-inline"><a class="page-link" href="#">5</a></li>
                <li class="page-item d-inline d-md-none"><a class="page-link" href="#">...</a></li>
                <li class="page-item"><a class="page-link" href="#">6</a></li>
                <li class="page-item">
                    <a class="page-link" href="#" aria-label="Next">
                        <span aria-hidden="true">Next &raquo;</span>
                    </a>
                </li>
            </ul>
        </div>

</div>



</body>
<footer>
    @include('user::layouts.footer')
</footer>
</html>
