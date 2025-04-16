<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $beritaDetail['judul'] ?? 'Detail Berita' }} - PPID Polinema</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('user::layouts.header')
    @include('user::layouts.navbar')

    <section class="hero-section-ef"
        style="background: url('{{ asset('img/hero-grapol.svg') }}') no-repeat center center/cover; color: #fff; text-align: left; height: 40vh; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 0 20px;">
        <div class="container">
            <header>
                <h6 class="display-6 fw-bold text-center">Detail Berita</h6>
            </header>
        </div>
    </section>

    <section class="container mt-4 py-5">
        @if(isset($beritaDetail))
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <!-- Kategori dan Tanggal -->
                    <div class="d-flex justify-content-between mb-3">
                        <span class="badge bg-primary">{{ $beritaDetail['kategori'] }}</span>
                        <small class="text-muted">{{ $beritaDetail['tanggal'] }}</small>
                    </div>
                    
                    <!-- Judul Berita -->
                    <h1 class="mb-4">{{ $beritaDetail['judul'] }}</h1>
                    
                    <!-- Thumbnail jika ada -->
                    @if(isset($beritaDetail['thumbnail']) && $beritaDetail['thumbnail'])
                        <div class="text-center mb-4">
                            <img src="{{ $beritaDetail['thumbnail'] }}" alt="{{ $beritaDetail['judul'] }}" class="img-fluid rounded">
                            @if(isset($beritaDetail['deskripsiThumbnail']) && $beritaDetail['deskripsiThumbnail'])
                                <figcaption class="figure-caption text-center mt-2">{{ $beritaDetail['deskripsiThumbnail'] }}</figcaption>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Konten Berita -->
                    <div class="berita-content">
                        {!! $beritaDetail['konten'] !!}
                    </div>
                    
                    <!-- Navigasi kembali -->
                    {{-- <div class="mt-5 pt-3 border-top">
                        <a href="{{ route('berita.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Berita
                        </a>
                    </div> --}}
                </div>
            </div>
        @else
            <div class="alert alert-warning text-center">
                <h4>Data berita tidak ditemukan</h4>
                <p>Mohon maaf, detail berita yang Anda cari tidak tersedia.</p>
                <a href="{{ route('berita.index') }}" class="btn btn-primary mt-3">Kembali ke Daftar Berita</a>
            </div>
        @endif
    </section>
</body>
<footer>
    @include('user::layouts.footer')
</footer>
</html>