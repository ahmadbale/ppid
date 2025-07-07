<!-- resources/views/sisfo/layouts/header.blade.php -->
@php
    use Modules\Sisfo\App\Models\Log\NotifAdminModel;
    use Modules\Sisfo\App\Models\Log\NotifVerifikatorModel;

    $totalNotifikasiADM = NotifAdminModel::where('sudah_dibaca_notif_admin', null)->count();
    $totalNotifikasiVFR = NotifVerifikatorModel::where('sudah_dibaca_notif_verif', null)->count();
@endphp

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item d-flex align-items-center">
            <a class="nav-link d-flex align-items-center pr-2" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
            <h3 class="pl-2 card-title greeting-nich text-bold"></h3>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto align-items-center">
        <!-- Notification -->
        <li class="nav-item dropdown d-flex align-items-center mr-3">
            @php
                $currentLevel = Auth::user()->level->hak_akses_kode ?? '';
            @endphp

            @if ($currentLevel == 'SAR')
                <a href="{{ url('/Notifikasi/NotifSarpras') }}" class="nav-link d-flex align-items-center "
                    style="font-size: 1.3rem;">
                    <i class="far fa-bell nav-icon"></i>
                </a>
            @elseif ($currentLevel == 'ADM')
                <a href="{{ url('/Notifikasi/NotifAdmin') }}" class="nav-link d-flex align-items-center"
                    style="font-size: 1.3rem;">
                    <i class="far fa-bell nav-icon"></i>
                    @if ($totalNotifikasiADM > 0)
                        <span class="badge badge-danger navbar-badge" style="font-size: 12px; top: 0; right: 0;">
                            {{ $totalNotifikasiADM }}
                        </span>
                    @endif
                </a>
            @elseif ($currentLevel == 'MPU')
                <a href="{{ url('/Notifikasi/NotifMPU') }}" class="nav-link d-flex align-items-center"
                    style="font-size: 1.3rem;">
                    <i class="far fa-bell nav-icon"></i>
                </a>
            @elseif ($currentLevel == 'VFR')
                <a href="{{ url('/Notifikasi/NotifVFR') }}" class="nav-link d-flex align-items-center"
                    style="font-size: 1.3rem;">
                    <i class="far fa-bell nav-icon"></i>
                </a>
            @elseif ($currentLevel == 'RPN')
                <a href="{{ url('/Notifikasi/NotifRPN') }}" class="nav-link d-flex align-items-center"
                    style="font-size: 1.3rem;">
                    <i class="far fa-bell nav-icon"></i>
                </a>
            @else
                <!-- Untuk level lain, tampilkan notifikasi umum atau tidak ada notifikasi -->
                <a href="#" class="nav-link d-flex align-items-center" style="font-size: 1.3rem;">
                    <i class="far fa-bell nav-icon text-muted"></i>
                </a>
            @endif
        </li>

        <!-- User Profile Dropdown -->
        <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle d-flex align-items-center px-0" id="profileDropdown"
                role="button" data-toggle="dropdown" aria-expanded="false">
                <div class="d-flex align-items-center">
                    <img src="{{ Auth::user()->foto_profil ? asset('storage/' . Auth::user()->foto_profil) : asset('img/userr.png') }}"
                        alt="User Profile Picture" class="img-circle mr-2"
                        style="width: 32px; height: 32px; object-fit: cover; opacity: 0.9;">
                    <div class="d-flex flex-column justify-content-center">
                        <span class="font-weight-bold text-primary" style="font-size: 1.1rem; line-height: 1.1;">
                            {{ Auth::user()->nama_pengguna }}
                        </span>
                        <span class="text-muted" style="font-size: 0.85rem; line-height: 1.1;">
                            {{ Auth::user()->level->hak_akses_nama }}
                        </span>
                    </div>
                    <i class="fas fa-chevron-down ml-2" style="color: #007bff; font-size: 1rem;"></i>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" aria-labelledby="profileDropdown">
                <a href="{{ url('/profile') }}" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> Profil
                </a>

                @php
                    // Ambil semua hak akses yang dimiliki user selain yang aktif saat ini
                    $userHakAkses = Auth::user()->getAvailableRoles();
                @endphp

                @if($userHakAkses->count() > 0)
                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header text-muted">Ganti Level</h6>
                    @foreach($userHakAkses as $hakAkses)
                        <a href="{{ url('/switch-role/' . $hakAkses->hak_akses_id) }}" class="dropdown-item switch-role-link">
                            <i class="fas fa-exchange-alt mr-2 text-primary"></i>
                            <span>{{ $hakAkses->hak_akses_nama }}</span>
                            <small class="text-muted d-block ml-4">Kode: {{ $hakAkses->hak_akses_kode }}</small>
                        </a>
                    @endforeach
                @endif
            </div>
        </li>
    </ul>
</nav>

<style>
    /* Menghilangkan caret bawaan dari Bootstrap dropdown-toggle */
    .dropdown-toggle::after {
        display: none !important;
    }

    /* Style untuk switch role link */
    .switch-role-link:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease;
    }

    .dropdown-header {
        font-size: 0.875rem;
        font-weight: 600;
    }
</style>

<script>
    function getGreeting() {
        const now = new Date();
        const hour = now.getHours();

        if (hour >= 0 && hour < 5) {
            return "🦉 It's super late. Time to get some rest!";
        } else if (hour >= 5 && hour < 11) {
            return "☀️ Good morning! Let's make awesome today.";
        } else if (hour >= 11 && hour < 14) {
            return "🍱 Selamat siang! It's lunch time is calling.";
        } else if (hour >= 14 && hour < 18) {
            return "☕ Good afternoon! Time for a latte break.";
        } else {
            return "🌙 Good evening! Time to chill down.";
        }
    }
    document.querySelector(".greeting-nich").textContent = getGreeting();

    // Handle switch role dengan loading state
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.switch-role-link').forEach(function (link) {
            link.addEventListener('click', function (e) {
                // Tampilkan loading state
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Switching...';
                this.style.pointerEvents = 'none';

                // Set timeout untuk fallback jika redirect tidak bekerja
                setTimeout(() => {
                    this.innerHTML = originalHTML;
                    this.style.pointerEvents = 'auto';
                }, 5000);
            });
        });
    });
</script>