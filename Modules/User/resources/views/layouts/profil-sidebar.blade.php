@extends('user::layouts.mainpage')

@section('content')
<div class="layout-container">
    <!-- Konten Utama -->
    <main class="content-side">
        @yield('content-side')
    </main>

    <!-- Sidebar kanan -->
    <aside class="sk col-lg-3 col-md-5 col-12 p-4 ">
        <nav class="sidebar right">
            <h4 class="title-sidebar-section">Tentang PPID</h4>
            <ul>
                <li><a href="{{ route('profil') }}"
                    class="sidebar-link {{ Route::currentRouteName() == 'profil' ? 'sidebar-on' : '' }}">Profil</a></li>
                <li><a href="{{ route('tugas_fungsi') }}"
                    class="sidebar-link {{ Route::currentRouteName() == 'tugas_fungsi' ? 'sidebar-on' : '' }}">Tugas dan Fungsi</a></li>
                <li><a href="{{ route('struktur_organisasi') }}"
                    class="sidebar-link {{ Route::currentRouteName() == 'struktur_organisasi' ? 'sidebar-on' : '' }}">Struktur Organisasi</a></li>
                <li><a href="{{ route('dasar_hukum') }}"
                    class="sidebar-link {{ Route::currentRouteName() == 'dasar_hukum' ? 'sidebar-on' : '' }}">Dasar Hukum</a></li>
                <li><a href="https://www.polinema.ac.id/profil/pelayanan-publik/">Maklumat Pelayanan Publik</a></li>
                <li><a href="{{ route('maklumat_ppid') }}"
                    class="sidebar-link {{ Route::currentRouteName() == 'maklumat_ppid' ? 'sidebar-on' : '' }}">Maklumat PPID</a></li>
            </ul>
        </nav>
    </aside>
</div>
@endsection
