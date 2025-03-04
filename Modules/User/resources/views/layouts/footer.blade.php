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

    <!-- Footer -->
    <div class="border-top border-warning border-5"></div>
    <footer class="footer pt-5 pb-4">
        <div class="container footer-content">
            <div class="row gy-5 justify-content-center">
                <!-- Informasi PPID -->
                <div class="col-md-3 mb-4 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ asset('img/logo-polinema.svg') }}" alt="Logo Polinema" width="50">
                        <img src="{{ asset('img/logo-blu.svg') }}" alt="Logo Blu" width="50">
                    </div>
                    <h5 class="fw-bold">Kantor PPID <br>
                        Politeknik Negeri
                        Malang</h5>
                    <p class="text-footer text-footer small">
                        Jl. Soekarno Hatta No.9, Jatimulyo, <br>
                        Lowokwaru, Kota Malang, <br>Jawa Timur 65141
                    </p>
                </div>

                {{-- @foreach ($icons as $icon)
                <div class="col-md-3 mb-4 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ $icon['logo-polinema'] }}" alt="Logo Polinema" width="50">
                        <img src="{{ $icon['logo-blu'] }}" alt="Logo Blu" width="50">
                    </div>
                    <h5 class="fw-bold">Kantor PPID <br>
                        Politeknik Negeri Malang</h5>
                    <p class="text-footer small">
                        Jl. Soekarno Hatta No.9, Jatimulyo, <br>
                        Lowokwaru, Kota Malang, <br>Jawa Timur 65141
                    </p>
                </div>
                @endforeach --}}



                <!-- Pusat Unit Layanan -->
                {{-- <div class="col-md-3 mb-4 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
                <h5 class="fw-bold mb-4">Pusat Unit Layanan</h5>
                <ul class="list-unstyled">
                    <li class="mb-4"><a href="#" class="text-footer text-decoration-none ">Jaminan Mutu</a></li>
                    <li class="mb-4"><a href="https://library.polinema.ac.id/" class="text-footer text-decoration-none">Perpustakaan</a></li>
                    <li class="mb-4"><a href="https://sipuskom.polinema.ac.id/" class="text-footer text-decoration-none">UPA TIK</a></li>
                    <li class="mb-4"><a href="#" class="text-footer text-decoration-none">P2M</a></li>
                </ul>
            </div> --}}

                @foreach ($links as $link)
                    <div class="col-md-3 mb-4 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
                        <h5 class="fw-bold mb-4">{{ $link['title'] }}</h5>
                        <ul class="list-unstyled">
                            @foreach ($link['menu'] as $menu)
                                <li class="mb-4">
                                    <a href="{{ $menu['route'] }}"
                                        class="text-footer text-decoration-none">{{ $menu['name'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach


                <!-- Layanan Informasi Offline -->
                <div class="col-md-4 mb-4 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
                    <h5 class="fw-bold mb-4">Layanan Informasi Offline</h5>
                    <p class="text-footer small">
                        Gedung Unit Layanan Terpadu <br>(Gedung AW) lantai 1, <br> Politeknik Negeri Malang
                    </p>
                    <div class="social-icons d-flex">
                        @foreach ($iconsosmed as $icon)
                            <a href="{{ $icon['route'] }}">
                                <img src="{{ $icon['logo'] }}" alt="Social Media">
                            </a>
                        @endforeach
                    </div>
                </div>


                <!-- Hubungi Kami -->
                <div class="col mb-4 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
                    <h5 class="fw-bold mb-4">Hubungi Kami</h5>
                    <ul class="list-unstyled">
                        <p class="text-footer small"> <i class="bi bi-envelope-fill"></i>
                            humas@polinema.ac.id
                        </p>
                        <p class="text-footer small"> <i class="bi bi-telephone-fill"></i>
                            0341 – 404424/404425
                        </p>
                    </ul>
                    <!-- Google Maps -->
                    <div class="map-container mt-3 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
                        <iframe class="rounded-3 shadow-sm border"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2349.577195214027!2d112.61583328047732!3d-7.946953672167347!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e78827687d272e7%3A0x789ce9a636cd3aa2!2sPoliteknik%20Negeri%20Malang!5e0!3m2!1sid!2sid!4v1739450215046!5m2!1sid!2sid"
                            width="160" height="150" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
        <!-- Copyright -->
        <div class="mt-4 border-top border-1 pt-3 text-footer small w-65 mx-auto">
            <div class="text-center py-2"> 2025 | Politeknik Negeri Malang copyright all right reserved
            </div>
        </div>
    </footer>

</body>

</html>
