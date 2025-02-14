<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Landing Page</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="bg-primary">
        <div class="container py-5">
            <h3 class="title-dokumentasi">Dokumentasi PPID</h3>
            <div class="mt-4 border-top border-1 pt-3 mb-4 w-65 mx-auto">
             </div>
            <!-- Tombol Navigasi -->
            <button class="carousel-control-prev " type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                <i class="bi bi-caret-left-fill icon-large"></i>
            </button>
            <button class="carousel-control-next " type="button" data-bs-target="#carouselExample"
                data-bs-slide="next">
                <i class="bi bi-caret-right-fill icon-large"></i>
            </button>

            <div id="carouselExample" class="carousel slide carousel-container" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row d-flex justify-content-center">
                            <div class="col-6 text-center">
                                <img src="{{ asset('dokumentasi-1.svg') }}" class="img-fluid" alt="Gambar 1">
                            </div>
                            <div class="col-6 text-center">
                                <img src="{{ asset('dokumentasi-2.svg') }}" class="img-fluid" alt="Gambar 2">
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="row d-flex justify-content-center">
                            <div class="col-6 text-center">
                                <img src="{{ asset('dokumentasi-3.svg') }}" class="img-fluid" alt="Gambar 3">
                            </div>
                            <div class="col-6 text-center">
                                <img src="{{ asset('dokumentasi-4.svg') }}" class="img-fluid" alt="Gambar 4">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- pengumuman ppid --}}
    <div class="container mt-4 py-5">
        <h3 class="title-pengumuman">Pengumuman PPID</h3>
        <div class="mt-4 border-top border-1 pt-3 mb-4 w-65 mx-auto">
         </div>
         <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="card h-100">   
                    <div class="card-header">
                        <img src="{{ asset('dokumentasi-1.svg') }}" class="img-cover" alt="Gambar 1">
                    </div>
                    <div class="card-body">
                        <p class="date">1 Februari 2025</p>
                        <p class="card-text">Deskripsi/isi pengumuman</p>
                        <a class="btn btn-primary" href="#" role="button">Baca Artikel</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <div class="card-header">
                         <img src="{{ asset('dokumentasi-2.svg') }}" class="img-cover" alt="Gambar 2">
                    </div>
                    <div class="card-body">
                        <p class="date">30 Januari 2025</p>
                        <p class="card-text">Deskripsi/isi pengumuman</p>
                        <a class="btn btn-primary" href="#" role="button">Baca Artikel</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <div class="card-header">
                        <img src="{{ asset('dokumentasi-3.svg') }}" class="img-cover" alt="Gambar 3">
                    </div>
                    <div class="card-body">
                        <p class="date">25 Januari 2025</p>
                        <p class="card-text">Deskripsi/isi pengumuman</p>
                        <a class="btn btn-primary" href="#" role="button">Baca Artikel</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex flex-wrap justify-content-center mt-3">
            <a href="#" class="btn-custom">
                <i class="bi bi-arrow-right"></i>
                <span class="ms-2">Pengumuman PPID Lainnya</span>
            </a>
        </div>
    </div>

    <div class="container mt-4 py-5">
        <div class="row">
            <!-- Bagian Berita PPID -->
            <div class="col-md-8">
                <h3 class="section-title-berita">Berita PPID</h3>
                <div class="mt-4 border-top border-1 pt-3 w-70 mx-auto">
                </div>
                
                <div class="news-item">
                    <h5>Sosialisasi Layanan Informasi Publik Polinema, Meningkatkan Keterbukaan dan Aksesibilitas Informasi untuk Semua</h5>
                    <p>Malang, 17 Oktober 2024 - Politeknik Negeri Malang (Polinema) hari ini menggelar kegiatan “Sosialisasi Layanan Informasi Publik Polinema Pejabat Pengelola Informasi dan Dokumentasi”. Acara yang berlangsung di Ruang Rapim Gedung AA lantai 2 ini diadakan secara hybrid ..</p>
                    <a href="#" class="read-more d-flex flex-wrap justify-content-end">selengkapnya →</a>
                </div>
                
                <div class="news-item">
                    <h5>Kegiatan Sosialisasi Pejabat Pengelola Informasi Dan Dokumentasi Oleh Pimpinan Dan Jajarannya</h5>
                    <p>Keterbukaan Informasi sangat diperlukan pada era digital saat ini. Oleh karena itu, melalui Pejabat Pengelola Informasi dan Dokumentasi (PPID) Politeknik Negeri Malang memberikan pelayanan informasi publik yang bersifat terbuka dan dapat diakses oleh masyarakat..</p>
                    <a href="#" class="read-more d-flex flex-wrap justify-content-end">selengkapnya →</a>
                </div>
                
                <div class="d-flex flex-wrap justify-content-center mt-3">
                    <a href="#" class="btn-custom">
                        <i class="bi bi-arrow-right"></i>
                        <span class="ms-2">Berita Lainnya</span>
                    </a>
                </div>
            </div>
    
            <!-- Bagian Media I nformasi Publik -->
            <div class="col-md-4">
                <h3 class="section-title-media">Media Informasi Publik</h3>
                <div class="mt-4 border-top border-1 pt-3 w-30 mx-auto">
                </div>
                <div class="video-container">
                    <iframe width="100%" height="200" src="https://www.youtube.com/embed/VIDEO_ID" frameborder="0" allowfullscreen></iframe>
                    <div class="text-white text-center p-2">Digitalisasi Ijazah</div>
                </div>
                
                <div class="d-flex flex-wrap justify-content-center mt-3">
                    <a href="#" class="btn-custom">
                        <i class="bi bi-arrow-right"></i>
                        <span class="ms-2">Media Lainnya</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- pintasan menu --}}
    <div class="container mt-4 mb-3 py-5 text-center">
        <h2 class="text-black">Pintasan Lainnya</h2>
        <div class="mt-4 border-top border-1 pt-3 mb-4 w-50 mx-auto"></div>
        
        <div class="d-flex flex-wrap justify-content-center gap-5 mb-4">
            <a class="btn btn-menu " href="#" role="button">POLINEMA</a>
            <a class="btn btn-menu " href="#" role="button">PORTAL</a>
            <a class="btn btn-menu " href="#" role="button">SIAKAD</a>
            <a class="btn btn-menu " href="#" role="button">SPMB</a>
        </div>
        
        <div class="d-flex flex-wrap justify-content-center gap-5">
            <a class="btn btn-menu " href="#" role="button">P2M</a>
            <a class="btn btn-menu " href="#" role="button">Jaminan Mutu</a>
            <a class="btn btn-menu " href="#" role="button">LPSE KEMDIKBUD</a>
            <a class="btn btn-menu " href="#" role="button">Alumni</a>
        </div>
    </div>
    

</body>
@extends('layouts.footer')

</html>
