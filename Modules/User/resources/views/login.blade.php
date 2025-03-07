<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login PPID</title>
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
                <h5>Lebih Transparan</h5>
            </div>
        </div>

        <div class="form-container">
            <div class="form-content">
                <img src="{{ asset('img/PPIDlogo.svg') }}" alt="PPID Logo" class="logo">
                <h4>Selamat datang di</h4>
                <h3>PPID Polinema</h3>
                <p class="input-label">Masukan akun Anda</p>

                <form id="login-form">
                    <input type="text"  id="username" placeholder="Email / No HP / NIK" class="input-field">
                    <div class="password-container position-relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'"  id="password" placeholder="Password" class="form-control" id="password">
                        <button type="button" class="btn  position-absolute end-0  translate-middle-y me-2 " style="position: absolute; right: 10px; top: 35%; transform: translateY(-35%);"
                                @click="show = !show">
                            <i :class="show ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                        </button>
                    </div>

                    <button type="submit" class="masuk-button">Sign In</button>
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
