<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LHKPN</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('user::layouts.header')
    @include('user::layouts.navbar')

    <div class="container">
        <div class="lhkpn-section">
            <h2 class="fw-bold padding-bottom:15px;">Laporan Harta Kekayaan Penyelenggara Negara</h2>
            <div class="mt-4 border-top border-1 pt-3 w-65 "></div>
            <div class="flex items-center text-gray-500 text-sm mt-2 mb-5">
                <i class="bi bi-clock-fill text-warning me-2"></i>
                <span class="ml-1">Diperbarui pada 12 Maret 2025, 16.10 </span>
            </div>
            <p class="mb-4">
                Dasar Hukum dalam menampilkan Laporan Harta Kekayaan Penyelenggara Negara yang telah diumumkan oleh
                Komisi Pemberantasan Korupsi:
                @foreach ($dasarHukum as $index => $hukum)
                    <br>{{ $index + 1 }}. {{ $hukum }}
                @endforeach
            </p>
            <h5 class="fw-bold text-start mt-4">Dokumen LHKPN</h5>
            <div class="mt-4 border-top border-1 pt-3 w-70 mx-auto"></div>

            <div class="d-flex align-items-center gap-2 ">
                <strong>Pilih tahun:</strong>
                {{-- <button class="btn btn-primary rounded-pill px-3 active">2022</button>
                <button class="btn btn-outline-secondary rounded-pill px-3">2023</button> --}}
                @foreach ($tahunList as $tahun)
                    <a href="{{ route('LHKPN', ['tahun' => $tahun]) }}"
                    class="btn {{ $tahun == $tahunDipilih ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">
                        {{ $tahun }}
                    </a>
                @endforeach
            </div>
            <p class="text-gray-500"><i>Menampilkan data LHKPN tahun {{ $tahunDipilih }}</i></p>
            {{-- <p class="text-gray-500"><i>Menampilkan data LHKPN tahun 2022</i></p> --}}
            <ul class="lhkpn-list">
                {{-- <li><span>1. Aang Afandi</span> <a href="#">Lihat</a></li>
                <li><span>2. Abdul Rasyid</span> <a href="#">Lihat</a></li>
                <li><span>3. Abdullah Helmy</span> <a href="#">Lihat</a></li>
                <li><span>4. Agus Suhardono</span> <a href="#">Lihat</a></li>
                <li><span>5. Ahmad Hermawan</span> <a href="#">Lihat</a></li> --}}
                @foreach ($lhkpnList as $item)
                <li>
                    <span>{{ $loop->iteration }}. {{ $item['nama'] }}</span>
                    <a href="{{ $item['link'] }}" class="btn btn-outline-primary">Lihat</a>
                </li>
            @endforeach

            </ul>
        </div>
    </div>
    @include('user::layouts.footer')
</body>

</html>
