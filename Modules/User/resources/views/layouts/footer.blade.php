<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Footer Polinema</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="border-top border-warning border-5"></div>
    <footer class="footer pt-5 pb-4">
        <div class="container footer-content">
            <div class="row gy-5 justify-content-center">
                <!-- Kantor PPID -->
                <div
                    class="col-md-3 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
                    <div class="footer-logos d-flex mb-3">
                        @if (!empty($headerData['items']))
                            @foreach ($headerData['items'] as $index => $item)
                                @if (isset($item['icon']))
                                    <img src="{{ $item['icon'] }}" alt="Logo {{ $index }}" width="300"
                                        height="50">
                                @endif
                            @endforeach
                        @endif

                    </div>
                    <h5>{{ $headerData['kategori_nama'] ?? '' }}</h5>
                    <p class="text small">
                        {{ $headerData['items'][0]['judul'] ?? '' }}
                    </p>
                </div>

                <!-- Pusat Unit Layanan -->
                <div
                    class="col-md-3 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
                    <h5>{{ $links['title'] ?? 'Pusat Unit Layanan' }}</h5>
                    <ul class="list-unstyled">
                        @if (!empty($links['menu']))
                            @foreach ($links['menu'] as $menu)
                                <li class="mb-3">
                                    <a href="{{ $menu['route'] ?? '#' }}" target="_blank">
                                        {{ $menu['name'] }}
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li class="text-muted">Tidak ada data.</li>
                        @endif
                    </ul>

                </div>


                <!-- Layanan Informasi Offline -->
                <div
                    class="col-md-4 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
                    <h5>Layanan Informasi Offline</h5>
                    @if (!empty($offlineInfo))
                        @foreach ($offlineInfo as $info)
                            <p class="text small">{{ $info['title'] ?? '' }}</p>
                        @endforeach
                    @else
                        <p class="text-muted">Tidak ada informasi offline.</p>
                    @endif

                    <div class="social-icons d-flex">
                        @if (!empty($socialIcons))
                            @foreach ($socialIcons as $icon)
                                <a href="{{ $icon['route'] ?? '#' }}" target="_blank" class="me-2">
                                    <img src="{{ $icon['logo'] ?? '' }}"
                                        alt="{{ $icon['title'] ?? 'Social Media Icon' }}" width="10"
                                        height="10">
                                </a>
                            @endforeach
                        @else
                            <p class="text-muted">Tidak ada ikon media sosial.</p>
                        @endif
                    </div>

                </div>

                <!-- Hubungi Kami -->
                <div
                    class="col mb-2 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
                    <h5>Hubungi Kami</h5>
                    <ul class="list-unstyled">
                        @if (!empty($contactInfo))
                            @foreach ($contactInfo as $contact)
                                <li class="mb-3">
                                    @if (strpos($contact['info'] ?? '', '@') !== false)
                                        <i class="bi bi-envelope-fill me-2"></i>
                                    @else
                                        <i class="bi bi-telephone-fill me-2"></i>
                                    @endif
                                    <a style="font-size:12px;"> {{ $contact['info'] ?? '' }} </a>
                                </li>
                            @endforeach
                        @else
                            <li class="text-muted">Tidak ada data.</li>
                        @endif
                    </ul>
                    <div class="map-container mt-3 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
                        <iframe class="rounded-3 shadow-sm border"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2349.577195214027!2d112.61583328047732!3d-7.946953672167347!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e78827687d272e7%3A0x789ce9a636cd3aa2!2sPoliteknik%20Negeri%20Malang!5e0!3m2!1sid!2sid!4v1739450215046!5m2!1sid!2sid"
                            width="160" height="150" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </iframe>
                    </div>
                </div>



                <!-- Copyright -->
                <div class="row border-top mt-4 pt-3 w-65">
                    <div class="col text-center">
                        <p class="text small">
                            2025 | Politeknik Negeri Malang copyright all rights reserved
                        </p>
                    </div>
                </div>
            </div>
    </footer>
</body>

</html>
