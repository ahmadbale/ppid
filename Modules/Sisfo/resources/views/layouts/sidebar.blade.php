<?php
use Illuminate\Support\Facades\Auth;
use Modules\Sisfo\App\Helpers\MenuHelper;
use Illuminate\Support\Facades\DB;

// Ambil user dan aktifkan hak akses
$user = Auth::user();
$userId = $user->user_id;

// Ambil hak akses aktif dari session
$activeHakAksesId = session('active_hak_akses_id');

// Jika tidak ada, cari yang pertama
if (!$activeHakAksesId) {
    $hakAkses = DB::table('set_user_hak_akses')
        ->join('m_hak_akses', 'set_user_hak_akses.fk_m_hak_akses', '=', 'm_hak_akses.hak_akses_id')
        ->where('set_user_hak_akses.fk_m_user', $userId)
        ->where('set_user_hak_akses.isDeleted', 0)
        ->where('m_hak_akses.isDeleted', 0)
        ->first();
        
    if ($hakAkses) {
        session(['active_hak_akses_id' => $hakAkses->hak_akses_id]);
        $activeHakAksesId = $hakAkses->hak_akses_id;
    }
}

// Ambil informasi hak akses
$hakAkses = DB::table('m_hak_akses')
    ->where('hak_akses_id', $activeHakAksesId)
    ->where('isDeleted', 0)
    ->first();
    
$hakAksesKode = $hakAkses ? $hakAkses->hak_akses_kode : '';
?>

<aside class="main-sidebar sidebar-dark-primary pt-4"
    style="position: fixed !important; top: 0 !important; left: 0 !important; height: 100vh !important; overflow-y: auto !important; z-index: 1030 !important; background-color: #0E1F43 !important">
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <div class="sidebar-brand text-center pb-4 brand-content" style="font-family: 'K2D', sans-serif; font-weight: 700;">
            <img src="{{ asset('img/logo-polinema.svg') }}" alt="logo PPID"
                style="display: block; margin: 0 auto; height: 110px; width: auto; " class="brand-image opacity-75 shadow">
            <h2 style="color: #FFC030">PPID</h2>
            <h6 style="color: white">POLITEKNIK NEGERI</br>MALANG</h6>
        </div>

        <nav>
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Navbar Search -->
                <div class="input-group sidebar-search" data-widget="sidebar-search" style="margin-bottom: 1rem;">
                    <input class="form-control" type="search" placeholder="Cari Menu" aria-label="Search"
                        style="background-color: transparent; border: 1px solid #fff; color: #fff; border-radius: 30px 0 0 30px; padding-left: 15px; font-size: 0.9rem;">
                    <div class="input-group-append">
                        <button class="btn"
                            style="background-color: transparent; border: 1px solid #fff; border-left: none; border-radius: 0 30px 30px 0; color: #fff;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Dynamic Menu dari MenuHelper -->
                {!! MenuHelper::renderSidebarMenus($hakAksesKode, request()->get('activeMenu', $activeMenu ?? '')) !!}
                
                <!-- Logout Menu (Tetap untuk semua pengguna) -->
                <li class="nav-header">Logout</li>
                <li class="nav-item">
                    <a class="nav-link bg-danger" data-widget="logout" id="logout-sidebar" role="button">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<script>
    document.querySelector('#logout-sidebar').addEventListener('click', function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Apakah yakin ingin keluar?',
            text: "Session anda akan berakhir",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Log Out',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ url('logout/') }}";
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        const activeMenu = document.querySelector('.nav-link.active');
        if (activeMenu) {
            // Scroll container .nav-sidebar biar menu aktif kelihatan di tengah
            const sidebar = document.querySelector('.nav-sidebar');
            if (sidebar && activeMenu.offsetTop > sidebar.offsetHeight) {
                sidebar.scrollTop = activeMenu.offsetTop - sidebar.offsetHeight / 2;
            }
        }
    });
</script>

<style>
    .notification-badge {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        background-color: #dc3545;
        color: white;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: bold;
    }
    
    body.sidebar-collapse .sidebar-search {
        display: none !important;
    }
    
    .sidebar.collapsed .brand-content img {
        height: 40px; /* Atur lebih kecil saat collapsed */
        transition: all 0.3s ease;
    }

    .sidebar.collapsed .brand-content h2,
    .sidebar.collapsed .brand-content h6 {
        display: none; /* Sembunyikan teks */
        transition: all 0.3s ease;
    }
    
    /* Additional styles for menu items */
    .nav-sidebar .nav-item {
        margin-bottom: 2px;
    }
    
    .nav-sidebar .nav-link {
        color: #fff;
        border-radius: 4px;
    }
    
    .nav-sidebar .nav-link.active {
        background-color: #FFC030;
        color: #0E1F43;
    }
    
    .nav-sidebar .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .nav-treeview {
        padding-left: 15px;
    }
    
    .nav-treeview .nav-link {
        padding-left: 20px;
        font-size: 0.9rem;
    }
    
    .nav-header {
        color: #FFC030;
        font-weight: 700;
        padding: 10px 5px 5px 15px;
        font-size: 0.85rem;
        text-transform: uppercase;
        margin-top: 10px;
    }
</style>