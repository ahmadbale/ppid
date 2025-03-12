<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer PPID Polinema</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
       
    </style>
</head>

<body>
    <footer class="footer">
        <div class="container footer-content">
            @php
                // Inisialisasi default values
                $headerData = $headerData ?? null;
                $links = $links ?? [];
                dd($links);
                $offlineInfo = $offlineInfo ?? null;
                $contactInfo = $contactInfo ?? [];
                $socialIcons = $socialIcons ?? [];
            @endphp
    
            <div class="row g-4">
                <!-- Kantor PPID -->
                <div class="col-md-3 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
                    <div class="footer-logos d-flex mb-3">
                        @if (!empty($headerData) && isset($headerData['items'][0]['icon']))
                            <img src="{{ $headerData['items'][0]['icon'] }}" alt="Logo PPID" width="50">
                        @endif
                    </div>
                    <h5>{{ $headerData['kategori_nama'] ?? '' }}</h5>
                    <p class="text small">
                        {{ $headerData['items'][0]['judul'] ?? 'Jl. Soekarno Hatta No.9, Jatimulyo, Lowokwaru, Kota Malang, Jawa Timur 65141' }}
                    </p>
                </div>

                <!-- Pusat Unit Layanan -->
                @foreach ($links as $link)
                
                    <div class="col-md-3 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
                        <h5>{{ $link['title'] }}</h5>
                        <ul class="list-unstyled">
                            @foreach ($link['menu'] as $menu)
                                <li class="mb-3">
                                    <a href="{{ $menu['route'] }}" target="_blank">
                                        {{ $menu['name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach

                <!-- Layanan Informasi Offline -->
                <div class="col-md-3 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
                    <h5>Layanan Informasi Offline</h5>
                    <p class="text small">
                        Gedung Unit Layanan Terpadu <br>
                        (Gedung AW) lantai 1, <br>
                        Politeknik Negeri Malang
                    </p>
                    <div class="social-icons d-flex">
                        @forelse ($socialIcons as $icon)
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
                <div class="col-md-3 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
                    <h5>Hubungi Kami</h5>
                    <ul class="list-unstyled">
                        <li class="3">
                            <i class="bi bi-envelope-fill me-2"></i>
                            humas@polinema.ac.id
                        </li>
                        <li class="3">
                            <i class="bi bi-telephone-fill me-2"></i>
                            0341 – 404424/404425
                        </li>
                    </ul>
                    <div class="map-container mt-3 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
                        <iframe class="rounded-3 shadow-sm border"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2349.577195214027!2d112.61583328047732!3d-7.946953672167347!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e78827687d272e7%3A0x789ce9a636cd3aa2!2sPoliteknik%20Negeri%20Malang!5e0!3m2!1sid!2sid!4v1739450215046!5m2!1sid!2sid"
                            width="160" height="150" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </iframe>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="row border-top mt-4 pt-3">
                <div class="col text-center">
                    <p class="text small">
                        2025 | Politeknik Negeri Malang copyright all right reserved
                    </p>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
