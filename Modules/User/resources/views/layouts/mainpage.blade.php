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
<body class="bg-light">
    <div class="container mx-auto p-6">
        @yield('content')
    </div>
    <footer>
        {{-- @include('user::layouts.footer') --}}
    </footer>
</body>
</html>
