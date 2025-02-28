<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPID Polinema</title>
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <style>
        .navbar {
            background-color: #0F2E5A !important;
            padding: 10px 15px;
        }

        .navbar-nav .nav-item .nav-link {
            color: white !important;
            font-weight: 400;
            padding: 10px 15px;
            transition: color 0.3s ease;
        }

        .navbar-toggler {
            border: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,..."); /* Ikon default Bootstrap */
        }

        .dropdown-menu {
            background: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            min-width: 200px;
            padding: 10px 0;
            border-radius: 5px;
        }

        .dropdown-menu .dropdown-item {
            color: #0F2C56 !important;
            padding: 10px 15px;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .dropdown-menu .dropdown-item:hover {
            background-color: #F9B234;
            color: white !important;
        }

        /* Mobile adjustments */
        @media (max-width: 991px) {
            .navbar-nav {
                text-align: center;
            }

            .navbar-nav .nav-item {
                margin-bottom: 5px;
            }

            .navbar-nav .nav-item .nav-link {
                display: block;
                padding: 10px;
            }

            .dropdown-menu {
                text-align: center;
                width: 100%;
            }

            .dropdown-menu .dropdown-item {
                display: block;
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('beranda') }}">Beranda</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link text-white dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Profil
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Profil Polinema</a></li>
                            <li><a class="dropdown-item" href="#">Profil PPID</a></li>
                            <li><a class="dropdown-item" href="#">Struktur Organisasi</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link text-white dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            E-Form
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('eform') }}">Permohonan Informasi</a></li>
                            <li><a class="dropdown-item" href="#">Pernyataan Keberatan</a></li>
                            <li><a class="dropdown-item" href="#">Pengaduan Masyarakat</a></li>
                            <li><a class="dropdown-item" href="#">Whistle Blowing System (WBS)</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link text-white dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Informasi Publik
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Regulasi</a></li>
                            <li><a class="dropdown-item" href="#">Daftar Informasi Publik</a></li>
                            <li><a class="dropdown-item" href="#">Daftar Informasi Dikecualikan</a></li>
                            <li><a class="dropdown-item" href="#">Informasi Berkala</a></li>
                            <li><a class="dropdown-item" href="#">Informasi Setiap Saat</a></li>
                            <li><a class="dropdown-item" href="#">Informasi Serta-merta</a></li>
                            <li><a class="dropdown-item" href="#">LHKPN</a></li>
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

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const dropdowns = document.querySelectorAll(".dropdown-toggle");
            dropdowns.forEach(dropdown => {
                dropdown.addEventListener("click", function (e) {
                    if (window.innerWidth < 992) {
                        let nextEl = this.nextElementSibling;
                        if (nextEl && nextEl.classList.contains("dropdown-menu")) {
                            e.preventDefault();
                            if (nextEl.style.display === "block") {
                                nextEl.style.display = "none";
                            } else {
                                document.querySelectorAll(".dropdown-menu").forEach(menu => {
                                    menu.style.display = "none";
                                });
                                nextEl.style.display = "block";
                            }
                        }
                    }
                });
            });

            document.addEventListener("click", function (e) {
                if (!e.target.matches(".dropdown-toggle")) {
                    document.querySelectorAll(".dropdown-menu").forEach(menu => {
                        menu.style.display = "none";
                    });
                }
            });
        });
    </script>

</body>
</html>
