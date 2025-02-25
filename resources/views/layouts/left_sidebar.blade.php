<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPID Polinema</title>
    @vite(['resources/css/sidebar.css'])
</head>
<body>

    <!-- Sidebar -->
    <nav class="sidebar">
        <ul>
            <li><a href="#">Beranda</a></li>
            <li class="dropdown">
                <a href="#">Profil</a>
                <ul class="dropdown-menu">
                    <li><a href="#">Profil Polinema</a></li>
                    <li><a href="#">Profil PPID</a></li>
                    <li><a href="#">Struktur Organisasi</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#">E-Form</a>
                <ul class="dropdown-menu">
                    <li><a href="{{ route('eform') }}">Permohonan Informasi</a></li>
                    <li><a href="#">Pernyataan Keberatan</a></li>
                    <li><a href="#">Pengaduan Masyarakat</a></li>
                    <li><a href="#">Whistle Blowing System (WBS)</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#">Informasi Publik</a>
                <ul class="dropdown-menu">
                    <li><a href="#">Regulasi</a></li>
                    <li><a href="#">Daftar Informasi Publik</a></li>
                    <li><a href="#">Daftar Informasi Dikecualikan</a></li>
                    <li><a href="#">Informasi Berkala</a></li>
                    <li><a href="#">Informasi Setiap Saat</a></li>
                    <li><a href="#">Informasi Serta-merta</a></li>
                    <li><a href="#">LHKPN</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#">Layanan Informasi</a>
                <ul class="dropdown-menu">
                    <li><a href="#">Pedoman Umum Pengelolaan Layanan</a></li>
                    <li><a href="#">Pedoman Layanan Kerjasama</a></li>
                    <li><a href="#">Prosedur Pelayanan Informasi</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <!-- Konten Utama -->
    <main class="content">
        <h1>Selamat Datang di PPID Polinema</h1>
        <p>this is page content with fixed Sidebar.</p>
    </main>
</body>
</html>
