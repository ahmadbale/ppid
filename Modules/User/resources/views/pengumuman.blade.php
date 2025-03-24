<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pengumuman</title>
    @vite(['resources/css/app.css'])

</head>
<body>
    @include('user::layouts.header')
    @include('user::layouts.navbar')

    <section class="hero-section-ef"
        style="background: url('{{ asset('img/hero-grapol.svg') }}') no-repeat center center/cover; color: #fff; text-align: left; height: 40vh; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 0 20px;">
        <div class="container">
            <h1 class="display-4 fw-bold">{{ $title }}</h1>
        </div>
    </section>
    <div class="container py-5">
        <div class="row row-cols-1 row-cols-md-3 g-5 ">
            @foreach ($pengumuman as $item)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ asset($item['gambar']) }}" class="card-img-top img-fluid" alt="Pengumuman">
                        <div class="card-body text-center">
                            <p class="text-muted">{{ $item['tanggal'] }}</p>
                            <p class="card-title fw-bold">{{ \Illuminate\Support\Str::words($item['judul'], 8, '...') }}</p>
                            <a href="{{ $item['link'] }}" class="btn btn-dark mt-2 rounded-pill">Baca Pengumuman</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- <div class="pagination_rounded">
        <ul>
            <li>
                <a href="#" class="prev">
                    <i class="fa fa-angle-left" aria-hidden="true"></i> Prev
                </a>
            </li>
            <li><a href="#">1</a></li>
            <li class="hidden-xs"><a href="#">2</a></li>
            <li class="hidden-xs"><a href="#">3</a></li>
            <li class="hidden-xs"><a href="#">4</a></li>
            <li class="hidden-xs"><a href="#">5</a></li>
            <li class="visible-xs"><a href="#">...</a></li>
            <li><a href="#">6</a></li>
            <li>
                <a href="#" class="next">
                    Next <i class="fa fa-angle-right" aria-hidden="true"></i>
                </a>
            </li>
        </ul>
    </div> --}}
    @include('user::layouts.footer')
</body>
</html>
