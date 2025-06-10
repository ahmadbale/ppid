<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'Permohonan Perawatan Sarana Prasarana' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('user::layouts.header')
    @include('user::layouts.navbar')

    <section class="hero-section-ef"
        style="background: url('{{ asset('img/hero-grapol.svg') }}') no-repeat center center/cover; color: #fff; text-align: left; height: 40vh; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 0 20px;">
        <div class="container">
            <h1 class="display-4 fw-bold">{{ $title }}</h1>
            <p class="lead">{!! $description !!}</p>
        </div>
    </section>

    <section class="content-section my-5 px-4">
        <div class="container">
            <h2 class="text-center mb-2 fw-bold">{{ $titlemekanisme }}</h2>
            <section class="container-fluid">
                <div class="timeline position-relative mb-5" x-data="{ showItems: [] }">
                    @forelse ($steps as $index => $step)
                        <div class="timeline-item {{ $step['position'] }}" x-data="{ show: false }"
                            x-init="setTimeout(() => show = true, {{ $index * 200 }})"
                            x-intersect.once="show = true"
                            :class="{ 'show': show }">
                            <div class="timeline-content">
                                <div class="timeline-number">{{ $step['number'] }}</div>
                                <p>{{ $step['text'] }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center">
                            <p>Tidak ada data langkah-langkah yang tersedia</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="document-section my-4">
                <a href="{{ asset('files/SOP_Perawatan_Sarana_Prasarana.pdf') }}" target="_blank"
                    class="d-flex align-items-center text-dark text-decoration-none">
                    <i class="bi-book fs-4 me-2"></i>
                    <span class="fw-bold link-text">Dokumen SOP Perawatan Sarana Prasarana</span>
                </a>
            </section>

            <h2 class="text-center mb-2 fw-bold">Ketentuan Permohonan Perawatan Sarana Prasarana</h2>

            <div class="row align-items-center mb-5">
                <div class="col-md-8">
                    <div class="ketentuan-list">
                        <ol>
                            <li class="mb-3">Pemohon wajib masuk menggunakan akun yang terdaftar.</li>
                            <li class="mb-3">
                                Permohonan dapat diajukan untuk:
                                <ul class="mt-2">
                                    <li>Perbaikan fasilitas fisik</li>
                                    <li>Perawatan peralatan laboratorium</li>
                                    <li>Pemeliharaan ruang kelas dan gedung</li>
                                    <li>Perbaikan sarana teknologi informasi</li>
                                </ul>
                            </li>
                            <li class="mb-3">Permohonan harus disertai dengan detail lokasi dan deskripsi kerusakan.</li>
                            <li class="mb-3">Pemohon akan menerima notifikasi status tindak lanjut permohonan.</li>
                        </ol>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="illustration-placeholder">
                        <img src="{{ asset('img/ilustrasi-sarpras.svg') }}" alt="Ilustrasi Sarana Prasarana" class="img-fluid">
                    </div>
                </div>
            </div>

            <!-- Ajukan Permohonan Section -->
            <div class="ajukan-pengaduan">
                <h3 class="mb-0">Ajukan Permohonan Perawatan</h3>
                <button class="masuk-button" onclick="window.location.href='{{ route('form-sarana-prasarana') }}'">
                    Klik form ini
                </button>
            </div>
        </div>
    </section>
    @include('user::layouts.footer')
</body>

</html>