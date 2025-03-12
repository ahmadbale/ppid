<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css', 'resources/css/sidebar.css'])

</head>
@include('user::layouts.header')
@include('user::layouts.navbar')
<body class="d-flex flex-column min-vh-100">
    <div class="container-fluid px-6 flex-grow-1">
        @yield('content')
    </div>
    {{-- <footer class="mt-auto">
        @include('user::layouts.footer')
    </footer> --}}
</body>

</html>
