<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login PPID</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="login-container">
        <div class="image-container">
            <div class="overlay"></div>
            <img src="{{ asset('img/login-pic.webp') }}" alt="Background Image">
            <div class="overlay-text">
                <h3>Satu Langkah dalam</h3>
                <h1>Keterbukaan Informasi</h1>
                <h3>Lebih Transparan</h3>
            </div>
        </div>

        <div class="form-container">
            <div class="form-content">
                <img src="{{ asset('img/PPIDlogo.svg') }}" alt="PPID Logo" class="logo">
                <h4>Selamat datang di</h4>
                <h3>PPID Polinema</h3>
                <p class="input-label text-center">Masukan akun Anda</p>

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" id="login-form">
                    @csrf

                    <!-- Error Alert if any -->
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <input type="text" name='username' id='username' placeholder="Email / No HP / NIK" class="input-field">
                    <div class="password-container">
                        <input type="password" name='password' id="password" placeholder="Password">
                        <span class="toggle-password"></span>
                    </div>

                    <button type="submit" class="signin">Sign In</button>
                </form>
                <div class="signup-container">
                    <div class="divider"></div>
                    <span class="signup-link" onclick="window.location.href='{{ route('register') }}'">  Sign Up  </span>
                    <div class="divider"></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
