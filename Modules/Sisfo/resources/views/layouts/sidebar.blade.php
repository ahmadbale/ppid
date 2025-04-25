<?php
use Illuminate\Support\Facades\Auth;
use Modules\Sisfo\App\Helpers\MenuHelper;
?>

<aside class="main-sidebar sidebar-dark-primary pt-4 pb-4" style="position: fixed !important; top: 0 !important; left: 0 !important; height: 100vh !important; overflow-y: auto !important; z-index: 1030 !important; background-color: #0E1F43 !important">

    <!-- Brand Logo -->
    <div class="sidebar-brand text-center pb-4" style="font-family: 'K2D', sans-serif; font-weight: 700;">
        <img src="{{ asset('img/logo-polinema.svg') }}" alt="logo PPID"
            style= "display: block; margin: 0 auto; height: 110px; width: auto; "
            class="brand-image opacity-75 shadow">
        <h2 style="color: #FFC030">PPID</h2>
        <h6 style="color: white">POLITEKNIK NEGERI</br>MALANG</h6>
    </div>

<div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-4">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Navbar Search -->
            <div class="input-group" data-widget="sidebar-search" style="margin-bottom: 1rem;">
                <input class="form-control" type="search" placeholder="Cari Menu" aria-label="Search"
                    style="background-color: transparent; border: 1px solid #fff; color: #fff; border-radius: 30px 0 0 30px; padding-left: 15px; font-size: 0.9rem;">
                <div class="input-group-append">
                    <button class="btn" style="background-color: transparent; border: 1px solid #fff; border-left: none; border-radius: 0 30px 30px 0; color: #fff;">
                        <i class="fas fa-search"></i>
                    </button>

                </div>
            </div>

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    {!! MenuHelper::renderSidebarMenus(Auth::user()->level->level_kode, $activeMenu) !!}
                </ul>
            </nav>
            <li class="nav-header">Logout</li>
            <li class="nav-item">
                <a class="nav-link active bg-danger" data-widget="logout" id="logout-sidebar" role="button">
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
</style>