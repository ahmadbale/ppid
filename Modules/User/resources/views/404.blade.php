<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css'])
    <title>Halaman Tidak Ditemukan</title>
    <style>
        .error-container {
            background-color: white;
            min-height: 70vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 2rem;
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: 700;
            color: #3b82f6;
            margin: 0;
            line-height: 1;
            position: relative;
        }
        
        .error-code::after {
            content: "";
            position: absolute;
            width: 100%;
            height: 15px;
            bottom: 15px;
            left: 10px;
            background-color: rgba(59, 130, 246, 0.1);
            z-index: -1;
        }
        
        .error-title {
            font-size: 1.5rem;
            color: #1f2937;
            margin: 1.5rem 0;
        }
        
        .error-message {
            font-size: 1rem;
            color: #6b7280;
            max-width: 500px;
            margin-bottom: 2rem;
        }
        
        .back-button {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.25);
        }
        
        .back-button:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 6px 10px rgba(59, 130, 246, 0.3);
        }
        
        .illustration {
            max-width: 300px;
            /* margin-bottom: 2rem; */
        }
        
        @media (max-width: 640px) {
            .error-code {
                font-size: 6rem;
            }
            
            .error-title {
                font-size: 1.25rem;
            }
        }
        
        .animate-float {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body>
    @include('user::layouts.header')
    @include('user::layouts.navbar')
    <div class="row">
    <div class="col-md-5 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
        <div class="error-container">
        <img src="{{ asset('img/404.png') }}" alt="404 Illustration" class="illustration animate-float">
        </div>
    </div>
   
    <div class="col-md-5 d-flex flex-column align-items-center align-items-md-start text-center text-md-start">
    <div class="error-container">
        {{-- <h1 class="error-code">404</h1> --}}
        <h2 class="error-title">Halaman Tidak Ditemukan</h2>
        <p class="error-message">
            Maaf, halaman yang Anda cari tidak dapat ditemukan. Mungkin halaman telah dipindahkan atau dihapus, atau URL yang Anda masukkan salah.
        </p>
        <a href="{{ route('berita') }}" class="back-button">Kembali ke Berita</a>
        </div>
    </div>
</div>
    @include('user::layouts.footer')
</body>
</html>