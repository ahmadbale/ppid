<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Berita PPID</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('user::layouts.header')
    @include('user::layouts.navbar')

    <section class="hero-section-ef"
        style="background: url('{{ asset('img/hero-grapol.svg') }}') no-repeat center center/cover; color: #fff; text-align: left; height: 40vh; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 0 20px;">
        <div class="container">
            <header>
                <h6 class="display-6 fw-bold text-center">{{ $title }}</h1>
            </header>
        </div>
    </section>
    <section class="container mt-4 py-5">

    </section>

</div>



</body>
<footer>
    @include('user::layouts.footer')
</footer>
</html>
