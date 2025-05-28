<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $beritaDetail['judul'] ?? 'Detail Berita' }} - PPID Polinema</title>
    @vite(['resources/css/app.css'])
    <style>
        .raised-content {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: -50px;
            position: relative;
        }
        .gradient-bg {
            background: linear-gradient(to bottom, #FFC030, #FFE4A5);
        }
        .header-section {
            height: 40vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            flex-direction: column; 
            padding: 0 20px;
        }
        
    </style>
</head>
<body>
    @include('user::layouts.header')
    @include('user::layouts.navbar')

    <section class="gradient-bg header-section mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-10 mx-auto">
                    <!-- Judul Berita -->
                    <h1 class="fw-bold text-left mb-3">{{ $beritaDetail['judul'] }}</h1>
                    
                    <!-- Tanggal -->
                    <div class="d-flex justify-content-left mb-4">
                        <div class="me-3">
                            <i class="bi bi-calendar-event"></i> {{ $beritaDetail['tanggal'] ?? '12 Desember 2024' }}
                        </div>
                        <div>
                            <i class="bi bi-user"></i> {{ $beritaDetail['tanggal'] ?? '12 Desember 2024' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container mb-5">
        <div class="row">
            <div class="col-md-10 mx-auto">
                <div class="raised-content">
                    <!-- Thumbnail -->
                    <div class="text-center mb-4">
                        <img src="{{ $beritaDetail['thumbnail'] }}" alt="{{ $beritaDetail['judul'] }}" class="img-fluid rounded">
                    </div>
                    
                    <!-- Konten Berita -->
                    <div class="berita-content mb-4">
                        {!! $beritaDetail['konten'] !!}
                    </div>
                    
                    <!-- Navigasi Previous/Next Post -->
<div class="row mt-5 pt-4 border-top align-items-center justify-content-center">
    <div class="col-md-5 text-start">
        @if(isset($previousPost))
            <a href="{{ route('berita.detail', [$previousPost['slug'], Crypt::encryptString($previousPost['berita_id'])]) }}" class="text-decoration-none text-dark">
                <div class="fw-bold mb-1" style="font-size: 14px;">
                    <i class="bi bi-arrow-left-circle"></i> PREVIOUS POST
                </div>
                <div class="text-muted" style="font-size: 15px;">
                    {{ \Illuminate\Support\Str::limit($previousPost['judul'], 60) }}
                </div>
            </a>
        @endif
    </div>
    <div class="col-md-2 text-center d-none d-md-block">
        <div style="border-left:1px solid #ccc;height:50px;margin:auto;"></div>
    </div>
    <div class="col-md-5 text-end">
        @if(isset($nextPost))
            <a href="{{ route('berita.detail', [$nextPost['slug'], Crypt::encryptString($nextPost['berita_id'])]) }}" class="text-decoration-none text-dark">
                <div class="fw-bold mb-1" style="font-size: 14px;">
                    NEXT POST <i class="bi bi-arrow-right-circle"></i>
                </div>
                <div class="text-muted" style="font-size: 15px;">
                    {{ \Illuminate\Support\Str::limit($nextPost['judul'], 60) }}
                </div>
            </a>
        @endif
    </div>
</div>
                </div>
            </div>
        </div>
    </section>

    @include('user::layouts.footer')
</body>
</html>