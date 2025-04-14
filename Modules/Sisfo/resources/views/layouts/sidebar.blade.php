<?php
use Illuminate\Support\Facades\Auth;
// use Modules\Sisfo\App\Models\Log\NotifAdminModel;
// use Modules\Sisfo\App\Models\Log\NotifVerifikatorModel;
use Modules\Sisfo\App\Helpers\MenuHelper;

// Hitung total notifikasi belum dibaca
// $totalNotifikasiADM = NotifAdminModel::where('sudah_dibaca_notif_admin', null)->count();
// $totalNotifikasiVFR = NotifVerifikatorModel::where('sudah_dibaca_notif_verif', null)->count();
?>

<aside class="main-sidebar sidebar-dark-primary pt-4 pb-4" style="position: fixed; top: 0; left: 0; height: 100vh; overflow-y: auto; z-index: 1030; background-color: #0E1F43">
    <!-- Brand Logo -->
    <a class="sidebar-brand text-center pb-4" style="font-family: 'K2D', sans-serif; font-weight: 700;">
        <img src="{{ asset('img/logo-polinema.svg') }}" alt="logo PPID"
            style= "display: block; margin: 0 auto; height: 110px; width: auto; "
            class="brand-image opacity-75 shadow">
        <h2 style="color: #FFC030">PPID</h2>
        <h6 style="color: white">POLITEKNIK NEGERI</br>MALANG</h6>
    </a>
    <!-- /.sidebar -->

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

            <!-- Menu untuk setiap level_kode -->
            @if (Auth::user()->level->level_kode == 'ADM')
                <li class="nav-header">Menu Umum</li>
                <li class="nav-item">
                    <a href="{{ url('/dashboardADM') }}" class="nav-link {{ $activeMenu == 'dashboard' ? 'active' : '' }} ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a href="{{ url('/Notifikasi/NotifAdmin') }}"
                        class="nav-link {{ $activeMenu == 'notifikasi' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bell"></i>
                        <p>Notifikasi</p>
                        @if ($totalNotifikasiADM > 0)
                            <span class="badge badge-danger notification-badge">{{ $totalNotifikasiADM }}</span>
                        @endif
                    </a>
                </li> --}}
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p> Manage Pengguna
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('/level') }}" class="nav-link {{ $activeMenu == 'level' ? 'active' : '' }} ">
                                <i class="nav-icon fas fa-layer-group"></i>
                                <p>Level User</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/user') }}" class="nav-link {{ $activeMenu == 'user' ? 'active' : '' }}">
                                <i class="nav-icon far fa-user"></i>
                                <p>Data User</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-header">Sistem Informasi</li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-file-signature"></i>
                        <p> E-Form
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('SistemInformasi/EForm/ADM/PermohonanInformasi') }}"
                                class="nav-link {{ $activeMenu == 'PermohonanInformasi' ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Permohonan Informasi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('SistemInformasi/EForm/ADM/PernyataanKeberatan') }}"
                                class="nav-link {{ $activeMenu == 'PernyataanKeberatan' ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Pernyataan Keberatan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('SistemInformasi/EForm/ADM/PengaduanMasyarakat') }}"
                                class="nav-link {{ $activeMenu == 'PengaduanMasyarakat' ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Pengaduan Masyarakat</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('SistemInformasi/EForm/ADM/WBS') }}"
                                class="nav-link {{ $activeMenu == 'WBS' ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Whistle Blowing System</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('SistemInformasi/EForm/ADM/PermohonanPerawatan') }}"
                                class="nav-link {{ $activeMenu == 'PermohonanPerawatan' ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Permohonan Perawatan SarPras</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-question-circle"></i>
                        <p> Petunjuk E-Form
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('SistemInformasi/KategoriForm') }}"
                                class="nav-link {{ $activeMenu == 'KategoriForm' ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Pengaturan Kategori Form</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('SistemInformasi/Timeline') }}"
                                class="nav-link {{ $activeMenu == 'Timeline' ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Pengaturan Timeline</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('SistemInformasi/KetentuanPelaporan') }}"
                                class="nav-link {{ $activeMenu == 'KetentuanPelaporan' ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Pengaturan Ketentuan Pelaporan</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-header">Manajemen Website</li>
                <!-- Menu Utama -->
                    <li class="nav-item">
                        @if(MenuHelper::shouldShowInSidebar('adminweb/menu-management'))
                            <li class="nav-item">
                                <a href="{{ url('/adminweb/menu-management') }}"
                                    class="nav-link {{ $activeMenu == 'menumanagement' ? 'active' : '' }}">
                                    <i class="fas fa-tasks nav-icon"></i>
                                    <p>Menu Management</p>
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-columns"></i>
                                <p> Footer
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ url('/adminweb/kategori-footer') }}"
                                        class="nav-link {{ $activeMenu == 'kategori-footer' ? 'active' : '' }}">
                                        <i class="fas fa-list-alt nav-icon"></i>
                                        <p>Kategori-Footer</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/adminweb/footer') }}"
                                        class="nav-link {{ $activeMenu == 'footer' ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Footer</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-bolt"></i>
                                <p> Pintasan & AksesCepat
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ url('/adminweb/kategori-akses') }}"
                                        class="nav-link {{ $activeMenu == 'kategori-akses' ? 'active' : '' }}">
                                        <i class="fas fa-tasks nav-icon"></i>
                                        <p>Kategori-Akses</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/adminweb/akses-cepat') }}"
                                        class="nav-link {{ $activeMenu == 'akses-cepat' ? 'active' : '' }}">
                                        <i class="fas fa-tasks nav-icon"></i>
                                        <p>Akses Cepat</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-newspaper"></i>
                                <p> Berita
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ url('/adminweb/berita-dinamis') }}"
                                        class="nav-link {{ $activeMenu == 'berita-dinamis' ? 'active' : '' }}">
                                        <i class="fas fa-tasks nav-icon"></i>
                                        <p>Kategori-Berita</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/adminweb/berita') }}"
                                        class="nav-link {{ $activeMenu == 'berita' ? 'active' : '' }}">
                                        <i class="fas fa-tasks nav-icon"></i>
                                        <p>Berita</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-bullhorn"></i>
                                <p> Pengumuman
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ url('/AdminWeb/PengumumanDinamis') }}"
                                        class="nav-link {{ $activeMenu == 'PengumumanDinamis' ? 'active' : '' }}">
                                        <i class="fas fa-tasks nav-icon"></i>
                                        <p>Kategori Pengumuman</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/AdminWeb/Pengumuman') }}"
                                        class="nav-link {{ $activeMenu == 'Pengumuman' ? 'active' : '' }}">
                                        <i class="fas fa-newspaper nav-icon"></i>
                                        <p>Pengumuman</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-photo-video"></i>
                                <p> Media Dinamis
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ url('/adminweb/media-dinamis') }}"
                                        class="nav-link {{ $activeMenu == 'media-dinamis' ? 'active' : '' }}">
                                        <i class="fas fa-tasks nav-icon"></i>
                                        <p>Kategori-Media</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/adminweb/media-detail') }}"
                                        class="nav-link {{ $activeMenu == 'media-detail' ? 'active' : '' }}">
                                        <i class="fas fa-tasks nav-icon"></i>
                                        <p>Data Media Dinamis</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-cog"></i>
                                <p> Data LHKPN
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ url('/adminweb/informasipublik/lhkpn-tahun') }}"
                                        class="nav-link {{ $activeMenu == 'Lhkpn Tahun' ? 'active' : '' }}">
                                        <i class="fas fa-tasks nav-icon"></i>
                                        <p>Tahun Lhkpn</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/adminweb/informasipublik/detail-lhkpn') }}"
                                        class="nav-link {{ $activeMenu == 'detail-lhkpn' ? 'active' : '' }}">
                                        <i class="fas fa-tasks nav-icon"></i>
                                        <p>Detail Lhkpn</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-cog"></i>
                                <p> Data Regulasi
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ url('/adminweb/informasipublik/regulasi-dinamis') }}"
                                        class="nav-link {{ $activeMenu == 'regulasi-dinamis' ? 'active' : '' }}">
                                        <i class="fas fa-tasks nav-icon"></i>
                                        <p>Regulasi Dinamis</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('/adminweb/informasipublik/kategori-regulasi') }}"
                                        class="nav-link {{ $activeMenu == 'kategori-regulasi' ? 'active' : '' }}">
                                        <i class="fas fa-tasks nav-icon"></i>
                                        <p>Kategori Regulasi</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </li>
            @elseif (Auth::user()->level->level_kode == 'SAR')
                <li class="nav-item">
                    <a href="{{ url('/dashboardSAR') }}" class="nav-link {{ $activeMenu == 'dashboard' ? 'active' : '' }} ">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/profile') }}" class="nav-link {{ $activeMenu == 'profile' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/HakAkses') }}" class="nav-link {{ $activeMenu == 'HakAkses' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-key"></i>
                        <p>Hak Akses</p>
                    </a>
                </li>
            @elseif (Auth::user()->level->level_kode == 'MPU')
                <li class="nav-item">
                    <a href="{{ url('/dashboardMPU') }}" class="nav-link {{ $activeMenu == 'dashboard' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/profile') }}" class="nav-link {{ $activeMenu == 'profile' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </li>
                <li class="nav-item" style="position: relative;">
                    <a href="{{ url('/notifMPU') }}" class="nav-link {{ $activeMenu == 'notifikasi' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bell"></i>
                        <p>Notifikasi</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/pengajuanPermohonan') }}"
                        class="nav-link {{ $activeMenu == 'pengajuan_permohonan' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>Daftar Permohonan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/pengajuanPertanyaan') }}"
                        class="nav-link {{ $activeMenu == 'pengajuan_pertanyaan' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-question-circle"></i>
                        <p>Daftar Pertanyaan</p>
                    </a>
                </li>
            @elseif (Auth::user()->level->level_kode == 'VFR')
                <li class="nav-item">
                    <a href="{{ url('/dashboardVFR') }}" class="nav-link {{ $activeMenu == 'dashboard' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/profile') }}" class="nav-link {{ $activeMenu == 'profile' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </li>
                <li class="nav-item" style="position: relative;">
                    <a href="{{ url('/notifikasi') }}" class="nav-link {{ $activeMenu == 'notifikasi' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bell"></i>
                        <p>Notifikasi</p>
                        @if ($totalNotifikasiVFR > 0)
                            <span class="badge badge-danger notification-badge">{{ $totalNotifikasiVFR }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/daftarPermohonan') }}"
                        class="nav-link {{ $activeMenu == 'daftar_permohonan' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>Daftar Permohonan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/daftarPertanyaan') }}"
                        class="nav-link {{ $activeMenu == 'daftar_pertanyaan' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-question-circle"></i>
                        <p>Daftar Pertanyaan</p>
                    </a>
                </li>
            @elseif (Auth::user()->level->level_kode == 'RPN')
                <li class="nav-item">
                    <a href="{{ url('/dashboardRPN') }}" class="nav-link {{ $activeMenu == 'dashboard' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/profile') }}" class="nav-link {{ $activeMenu == 'profile' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-folder-open"></i>
                        <p> E-Form
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('SistemInformasi/EForm/RPN/PermohonanInformasi') }}"
                                class="nav-link {{ $activeMenu == 'PermohonanInformasi' ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Permohonan Informasi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('SistemInformasi/EForm/RPN/PernyataanKeberatan') }}"
                                class="nav-link {{ $activeMenu == 'PernyataanKeberatan' ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Pernyataan Keberatan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('SistemInformasi/EForm/RPN/PengaduanMasyarakat') }}"
                                class="nav-link {{ $activeMenu == 'PengaduanMasyarakat' ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Pengaduan Masyarakat </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('SistemInformasi/EForm/RPN/WBS') }}"
                                class="nav-link {{ $activeMenu == 'WBS' ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Whistle Blowing System</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('SistemInformasi/EForm/RPN/PermohonanPerawatan') }}"
                                class="nav-link {{ $activeMenu == 'PermohonanPerawatan' ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon"></i>
                                <p>Permohonan Perawatan SarPras</p>
                            </a>
                        </li>
                    </ul>
                <li class="nav-item">
                    <a href="{{ url('/permohonan') }}" class="nav-link {{ $activeMenu == 'permohonan' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-envelope"></i>
                        <p>Pengajuan Permohonan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/pertanyaan') }}" class="nav-link {{ $activeMenu == 'pertanyaan' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-comments"></i>
                        <p>Pengajuan Pertanyaan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/hasilPermohonan') }}"
                        class="nav-link {{ $activeMenu == 'hasil_permohonan' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-scroll"></i>
                        <p>Hasil Permohonan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/hasilPertanyaan') }}"
                        class="nav-link {{ $activeMenu == 'hasil_pertanyaan' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-scroll"></i>
                        <p>Hasil Pertanyaan</p>
                    </a>
                </li>
            @endif
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
