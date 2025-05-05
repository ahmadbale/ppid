<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'Permohonan Informasi' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('user::layouts.header')
    @include('user::layouts.navbar')

    {{-- Hero Section --}}
    <section class="hero-section-ef"
        style="background: url('{{ asset('img/hero-grapol.svg') }}') no-repeat center center/cover; color: #fff; text-align: left; height: 40vh; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 0 20px;">
        <div class="container">
            <h1 class="display-4 fw-bold">{{ $title }}</h1>
            <p class="lead">{!! $description !!}</p>
        </div>
    </section>

    {{-- Content Section --}}
    <section class="content-section my-5 px-4">
        <div class="container">
            @if(!empty($titlemekanisme))
                <h2 class="text-center mb-2 fw-bold">{{ $titlemekanisme }}</h2>
            @endif

            {{-- Timeline Section --}}
            <section class="container-fluid">
                <div class="timeline position-relative mb-5" x-data="{ showItems: [] }">
                    @if(!empty($steps))
                        @foreach ($steps as $index => $step)
                            <div class="timeline-item {{ $step['position'] }}"
                                x-data="{ show: false }"
                                x-init="setTimeout(() => show = true, {{ $index }} * 200)"
                                x-intersect.once="show = true"
                                :class="{ 'show': show }">
                                <div class="timeline-content">
                                    <div class="timeline-number">{{ $step['number'] }}</div>
                                    <p>{{ $step['text'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <p>Data timeline tidak tersedia</p>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Document Section --}}
            <section class="document-section my-4">
                @if(!empty($timeline['file']))
                    <a href="{{ asset('storage/' . $timeline['file']) }}" target="_blank"
                        class="d-flex align-items-center text-dark text-decoration-none">
                        <i class="bi-book fs-4 me-2"></i>
                        <span class="fw-bold link-text">Dokumen SOP {{ $title }}</span>
                    </a>
                @endif
            </section>

            {{-- Ketentuan Section --}}
            <h2 class="text-center mb-2 fw-bold">Ketentuan Pengajuan Pertanyaan Keberatan Informasi</h2>
            <div class="row align-items-center mb-5">
                <div class="col-md-8">
                    <div class="ketentuan-list">
                        <ol>
                            <li class="mb-3">Pemohon wajib masuk menggunakan akun yang terdaftar.</li>
                            <li class="mb-3">
                                Pemohon mengajukan pernyataan keberatan secara tertulis paling lambat 30 hari kerja
                                setelah ditemukannya alasan-alasan sebagai berikut:
                                <ul class="mt-2">
                                    <li>penolakan atas permintaan informasi berdasarkan alasan pengecualian</li>
                                    <li>tidak disediakannya informasi berkala</li>
                                    <li>tidak ditanggapinya permintaan informasi</li>
                                    <li>permintaan informasi ditanggapi tidak sebagaimana yang diminta</li>
                                    <li>tidak dipenuhinya permintaan informasi</li>
                                    <li>pengenaan biaya yang tidak wajar</li>
                                    <li>penyampaian informasi yang melebihi waktu yang ditentukan oleh Undang-Undang</li>
                                </ul>
                            </li>
                            <li class="mb-3">Permohonan keberatan dapat dilakukan di meja layanan informasi secara lisan
                                maupun tertulis atau formulir online berikut.</li>
                        </ol>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="illustration-placeholder">
                        <img src="{{ asset('img/ilustrasi.svg') }}" alt="Ilustrasi {{ $title }}" class="img-fluid">
                    </div>
                </div>
            </div>

            {{-- Ajukan Pengaduan Section --}}
            <div class="ajukan-pengaduan">
                <h3 class="mb-0">Ajukan {{ $title }}</h3>
                <button class="masuk-button" onclick="window.location.href='{{ route('form-informasi-publik') }}'">
                    Klik form ini
                </button>
            </div>
        </div>
    </section>

    @include('user::layouts.footer')
</body>
</html>
