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
                <li><a href="#">Profil</a></li>
                <li><a href="#">Tugas dan Fungsi</a></li>
                <li><a href="#">Struktur Organisasi</a></li>
                <li><a href="#">Dasar Hukum</a></li>
                <li><a href="#">Maklumat Pelayanan Publik</a></li>
                <li><a href="#">Maklumat PPID</a></li>
            </ul>
        </nav>
    </aside>
</div>
@endsection
