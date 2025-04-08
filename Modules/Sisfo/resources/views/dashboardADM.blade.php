@extends('sisfo::layouts.template')

@section('content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Halo Pengguna</h3>
            <div class="card-tools"></div>
        </div>
        <div class="card-body">
            Selamat Datang Admin di PPID Polinema

            <div class="card-footer">
                <p>Nama Pengguna: {{ session('user_data.nama_pengguna') }}</p>
                <p>Email: {{ session('user_data.email_pengguna') }}</p>
                <p>Alamat: {{ session('user_data.alamat_pengguna')}}
                <p>No HP: {{ session('user_data.no_hp_pengguna') }}</p>
                <p>Pekerjaan: {{ session('user_data.pekerjaan_pengguna') }}</p>
                <p>NIK: {{ session('user_data.nik_pengguna')}}
                <p>Foto NIk: {{ session('user_data.upload_nik_pengguna')}}
                <p>Alias: {{ session('user_data.alias') }}</p>
            </div>

            @if(app()->environment('local'))
            <hr>
            <h4>Debug Session Data:</h4>
            <pre>{{ print_r(session()->all(), true) }}</pre>
            @endif

        </div>
    </div>

@endsection
