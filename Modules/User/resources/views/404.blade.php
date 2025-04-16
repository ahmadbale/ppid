<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css'])
    <title>Halaman Tidak Ditemukan</title>
</head>
<body>
    @include('user::layouts.header')
    @include('user::layouts.navbar')
    <div class="row">
    <div class="col-md-5 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
        <div class="error-container">
        <img src="{{ asset('img/404.png') }}" alt="404 Illustration" class="illustration animate-float">
        </div>
    </div>
   
    <div class="col-md-5 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
    <div class="error-container">
        {{-- <h1 class="error-code">404</h1> --}}
        <h2 class="error-title">Halaman Tidak Ditemukan</h2>
        <p class="error-message">
            Maaf, halaman yang Anda cari tidak dapat ditemukan. Mungkin halaman telah dipindahkan atau dihapus, atau URL yang Anda masukkan salah.
        </p>
        <a href="{{ route('berita') }}" class="back-button">Kembali ke Berita</a>
        </div>
    </div>
</div>
    @include('user::layouts.footer')
</body>
</html>