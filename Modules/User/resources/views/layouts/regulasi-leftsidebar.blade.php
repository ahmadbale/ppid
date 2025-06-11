@extends('user::layouts.mainpage')

@section('content')
    <div class="layout-container">
        <!-- Sidebar Kiri -->
        <aside class="w-1/4 p-4 ">
            <nav class="sidebar left">
                <h4 class="title-sidebar-section">Regulasi</h4>
                {{-- <ul>
                <li><a href="{{ route('regulasi-DHSOP') }}">Dasar Hukum Standart Operating Procedure (SOP) PPID Politeknik Negeri Malang</a></li>
                <li><a href="{{ route('regulasi-DHKIP') }}">Dasar Hukum Keterbukaan Informasi Publik PPID Politeknik Negeri Malang</a></li>
                <li><a href="{{ route('regulasi-DHLIP') }}">Dasar Hukum Layanan Informasi Publik PPID Politeknik Negeri Malang</a></li>
            </ul>
                </ul> --}}
                <ul class="sidebar-list">
    <li>
        <a href="{{ route('regulasi-DHSOP') }}"
           class="sidebar-link {{ Route::currentRouteName() == 'regulasi-DHSOP' ? 'sidebar-on' : '' }}">
            Dasar Hukum Standart Operating Procedure (SOP) PPID Politeknik Negeri Malang
        </a>
    </li>
    <li>
        <a href="{{ route('regulasi-DHKIP') }}"
           class="sidebar-link {{ Route::currentRouteName() == 'regulasi-DHKIP' ? 'sidebar-on' : '' }}">
            Dasar Hukum Keterbukaan Informasi Publik PPID Politeknik Negeri Malang
        </a>
    </li>
    <li>
        <a href="{{ route('regulasi-DHLIP') }}"
           class="sidebar-link {{ Route::currentRouteName() == 'regulasi-DHLIP' ? 'sidebar-on' : '' }}">
            Dasar Hukum Layanan Informasi Publik PPID Politeknik Negeri Malang
        </a>
    </li>
</ul>
            </nav>
        </aside>

        <!-- Konten Utama -->
        <main class="content-side">
            @yield('content-side')
        </main>
    </div>
@endsection
