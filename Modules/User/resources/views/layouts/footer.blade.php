<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer PPID Polinema</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .footer {
            background-color: #f8f9fa;
            padding: 2rem 0;
            border-top: 5px solid #ffc107;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer h5 {
            color: #333;
            margin-bottom: 1rem;
            font-weight: bold;
        }

        .footer a {
            color: #666;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: #007bff;
        }

        .social-icons a {
            margin-right: 10px;
            color: #007bff;
        }

        .footer-logos img {
            max-width: 50px;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <footer class="footer">
        <div class="container footer-content">
            @php
                // Inisialisasi default values
                $headerData = $headerData ?? null;
                $links = $links ?? [];
                $offlineInfo = $offlineInfo ?? null;
                $contactInfo = $contactInfo ?? [];
                $socialIcons = $socialIcons ?? [];
            @endphp
    
            <div class="row g-4">
                <!-- Kantor PPID -->
                <div class="col-md-3">
                    <div class="footer-logos d-flex mb-3">
                        @if (!empty($headerData) && isset($headerData['items'][0]['icon']))
                            <img src="{{ $headerData['items'][0]['icon'] }}" alt="Logo PPID" width="50">
                        @endif
                    </div>
                    <h5>{{ $headerData['kategori_nama'] ?? 'Kantor PPID Politeknik Negeri Malang' }}</h5>
                    <p class="text-muted small">
                        {{ $headerData['items'][0]['judul'] ?? 'Jl. Soekarno Hatta No.9, Jatimulyo, Lowokwaru, Kota Malang, Jawa Timur 65141' }}
                    </p>
                </div>

                <!-- Pusat Unit Layanan -->
                @foreach ($links as $link)
                    <div class="col-md-3">
                        <h5>{{ $link['title'] }}</h5>
                        <ul class="list-unstyled">
                            @foreach ($link['menu'] as $menu)
                                <li class="mb-2">
                                    <a href="{{ $menu['route'] }}" target="_blank">
                                        {{ $menu['name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach

                <!-- Layanan Informasi Offline -->
                <div class="col-md-3">
                    <h5>Layanan Informasi Offline</h5>
                    <p class="text-muted small">
                        Gedung Unit Layanan Terpadu <br>
                        (Gedung AW) lantai 1, <br>
                        Politeknik Negeri Malang
                    </p>
                    <div class="social-icons d-flex">
                        @forelse ($iconsosmed as $icon)
                            <a href="{{ $icon['route'] ?? '#' }}" target="_blank" class="me-2">
                                <img src="{{ $icon['logo'] ?? '' }}" alt="{{ $icon['title'] ?? 'Social Media Icon' }}"
                                    width="30" height="30">
                            </a>
                        @empty
                            <p class="text-muted">Tidak ada ikon media sosial</p>
                        @endforelse
                    </div>
                </div>

                <!-- Hubungi Kami -->
                <div class="col-md-3">
                    <h5>Hubungi Kami</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-envelope-fill me-2"></i>
                            humas@polinema.ac.id
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-telephone-fill me-2"></i>
                            0341 – 404424/404425
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div class="row border-top mt-4 pt-3">
                <div class="col text-center">
                    <p class="text-muted small">
                        2025 | Politeknik Negeri Malang copyright all right reserved
                    </p>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
