<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Prosedur Informasi Lainnya</title>
    @vite(['resources/css/app.css'])

</head>

<body>
    @include('user::layouts.header')
    @include('user::layouts.navbar')
     <div class="spacer" style="height: 80px;"></div> 
    <div class="container px-2 px-md-5">
        <h2 class="fw-bold mt-4 mb-2 text-center text-md-center" style="font-size:2.5rem;">
            Prosedur Layanan Informasi Lainnya
        </h2>
        <div class="mt-4 border-top border-1 pt-3 w-75 mx-auto"></div>
        <div class="mt-2 mb-4 text-gray-600 text-center" style="max-width: 900px; margin: 0 auto;">
            Halaman ini memuat daftar prosedur layanan informasi lainnya yang dapat diakses oleh masyarakat. Setiap
            prosedur dijelaskan secara rinci dalam dokumen SOP (Standar Operasional Prosedur) yang dapat Anda buka
            melalui tautan di bawah ini. Daftar berikut berisi berbagai prosedur pelayanan publik yang tersedia di
            lingkungan Politeknik Negeri Malang, guna memastikan keterbukaan, kejelasan alur, serta kemudahan akses
            informasi bagi seluruh pemangku kepentingan.
        </div>


        <div class="prosedur-list mx-auto mb-5" style="max-width:900px;">
            <div class="list-group shadow-sm rounded-3">
                @foreach ($sopLainnya as $sop)
                    <a href="{{ $sop['route'] }}"
                        class="list-group-item prosedur-item d-flex align-items-center py-3 px-4 border-0 border-bottom"
                        style="background:#fff; transition: background 0.2s;">
                        <i class="bi bi-gear-fill me-3" style="font-size:1.3rem; color: #212529;"></i>
                        <span class="fw-semibold" style="font-size:1.08rem;">{!! $sop['judul'] !!}</span>
                    </a>
                @endforeach

            </div>
        </div>
    </div>
    @include('user::layouts.footer');
</body>

</html>
