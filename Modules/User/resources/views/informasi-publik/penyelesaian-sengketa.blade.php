<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $nama ?? 'Permohonan Penyelesaian Sengketa Informasi Publik' }}</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('user::layouts.header')
    @include('user::layouts.navbar')

    <section class="hero-section-ef"
        style="background: url('{{ asset('img/hero-grapol.svg') }}') no-repeat center center/cover; color: #fff; text-align: left; height: 40vh; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 0 20px;">
        <div class="container">
            <header>
                <h2 class="display-4 fw-bold">{{ $nama}}</h2>
            </header>
        </div>
    </section>
      <div class="container py-5">
            {!! $deskripsi !!}
        <div class="pdf-header shadow-sm rounded p-4 mb-4 bg-white">
            <div class="d-flex justify-content-between align-items-center">
                 <div>
                    <h5 class="ml-1"><b>Prosedur :</b> {{ $nama}}</h5>
                 </div>
                <div>
                    @if(count($fileUploads) > 0)
                    @foreach($fileUploads as $file)
                    <a href="{{ asset($file['dokumen']) }}" class="btn btn-success" download>
                        Download File
                    </a>
                    @endforeach
                    @else
            <div class="alert alert-info">
                Tidak ada dokumen tersedia saat ini.
            </div>
              @endif
                </div>
            </div>
        </div>
        
      </div>
        
</body>
@include('user::layouts.footer')
</html>