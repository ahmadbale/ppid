{{-- @extends('user::layouts.left-sidebar') --}}
@extends('user::layouts.right-sidebar')

@section('title', 'Dashboard')

@section('content-side')
    <div class="bg-white p-5 rounded shadow">
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <p>Ini adalah contoh halaman page content dinamis, dengan right-sidebar.
            <br>tapi untuk controllerya belum diatur</p>
        <p>Konten utama di halaman dashboard.</p>
    </div>
@endsection
