<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPID Polinema</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav class="navbar navbar-expand-lg" style="background-color: #0F2C56;">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('beranda')}}">Beranda</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link text-white dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Profil
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('ppolinema')}}">Profil Polinema</a></li>
                            <li><a class="dropdown-item" href="{{ route('profil')}}">Profil PPID</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link text-white dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            E-Form
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('permohonan_informasi')}}">Permohonan Informasi</a></li>
                            <li><a class="dropdown-item" href="{{ route('pernyataan_keberatan')}}">Pernyataan Keberatan</a></li>
                            <li><a class="dropdown-item" href="{{ route('pengaduan_masyarakat')}}">Pengaduan Masyarakat</a></li>
                            <li><a class="dropdown-item" href="{{ route('wbs')}}">Whistle Blowing System (WBS)</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link text-white dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Informasi Publik
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Regulasi</a></li>
                            <li><a class="dropdown-item" href="{{ route('daftar_informasi')}}">Daftar Informasi Publik</a></li>
                            <li><a class="dropdown-item" href="#">Daftar Informasi Dikecualikan</a></li>
                            <li><a class="dropdown-item" href="#">Informasi Berkala</a></li>
                            <li><a class="dropdown-item" href="#">Informasi Setiap Saat</a></li>
                            <li><a class="dropdown-item" href="#">Informasi Serta-merta</a></li>
                            <li><a class="dropdown-item" href="{{ route('LHKPN')}}">LHKPN</a></li>
                            <li><a class="dropdown-item" href="{{ route('berita')}}">Berita PPID</a></li>
                            <li><a class="dropdown-item" href="{{ route('pengumuman')}}">Pengumuman</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link text-white dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Layanan Informasi
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Pedoman Umum Pengelolaan Layanan</a></li>
                            <li><a class="dropdown-item" href="#">Pedoman Layanan Kerjasama</a></li>
                            <li><a class="dropdown-item" href="#">Prosedur Pelayanan Informasi</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</body>
</htm>
