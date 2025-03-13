@extends('user::layouts.mainpage')

@section('content')
<div class="layout-container">
    <!-- Konten Utama -->
    <main class="content-side">
        @yield('content-side')
    </main>

    <!-- Sidebar kanan -->
    <aside class="w-1/4 p-4 ">
        <nav class="sidebar right">
            <h4 class="title-sidebar-section">Tentang PPID</h4>
            <ul>
                <li><a href="{{ route('profil') }}">Profil</a></li>
                <li><a href="{{ route('tugas_fungsi') }}">Tugas dan Fungsi</a></li>
                <li><a href="{{ route('struktur_organisasi') }}">Struktur Organisasi</a></li>
                <li><a href="{{ route('dasar_hukum') }}">Dasar Hukum</a></li>
                <li><a href="https://www.polinema.ac.id/profil/pelayanan-publik/">Maklumat Pelayanan Publik</a></li>
                <li><a href="{{ route('maklumat_ppid') }}">Maklumat PPID</a></li>
            </ul>
        </nav>
    </aside>
</div>
@endsection
