@extends('user::layouts.mainpage')

@section('content')
<div class="layout-container">
    <!-- Sidebar Kiri -->
    <aside class="w-1/4 p-4 ">
        <nav class="sidebar left">
            <h4 class="title-sidebar-section">Regulasi</h4>
            <ul>
                <li><a href="#">Dasar Hukum Standart Operating Procedure (SOP) PPID Politeknik Negeri Malang</a></li>
                <li><a href="#">Dasar Hukum Keterbukaan Informasi Publik PPID Politeknik Negeri Malang</a></li>
                <li><a href="#">Dasar Hukum Layanan Informasi Publik PPID Politeknik Negeri Malang</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Konten Utama -->
    <main class="content-side">
        @yield('content-side')
    </main>
</div>
@endsection
