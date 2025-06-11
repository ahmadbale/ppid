<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\layouts\template.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'test') }}</title>
    <link rel="icon" href="{{ sisfo_asset('modules/sisfo/logo.png') }}" type="image/png">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Untuk mengirimkan token laravel CSRF pada setiap request ajax -->
    <!--tambahan-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link href="https://fonts.googleapis.com/css2?family=K2D:wght@700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ sisfo_asset('modules/sisfo/adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- DataTables -->
    <link rel="stylesheet"
        href="{{ sisfo_asset('modules/sisfo/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ sisfo_asset('modules/sisfo/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ sisfo_asset('modules/sisfo/adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet"
        href="{{ sisfo_asset('modules/sisfo/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ sisfo_asset('modules/sisfo/adminlte/dist/css/style.css') }}">
    <link rel="stylesheet" href="{{ sisfo_asset('modules/sisfo/adminlte/dist/css/addstyle.css') }}">
    <link rel="stylesheet" href="{{ sisfo_asset('modules/sisfo/adminlte/dist/css/info-box.css') }}">

    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <!-- Custom Summernote CSS (dari Controller) -->
    <link href="{{ url('/css/summernote.css') }}" rel="stylesheet">
    
    <!-- Flatpicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        .info-message {
            position: relative;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid #bee5eb;
            border-radius: 0.25rem;
            color: #fffff;
        }

        .info-message .fa-info-circle {
            margin-right: 8px;
        }

        .info-content {
            line-height: 1.5;
        }
    </style>
    
    @stack('css') <!-- Digunakan untuk memanggil custom css dari perintah push('css') pada masing-masing view -->
</head>

<body class="hold-transition sidebar-mini">
    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- Navbar -->
        @include('sisfo::layouts.header')
        <!-- /.navbar -->
        @include('sisfo::layouts.sidebar')
        <div class="content-wrapper">
            <!-- Flash Messages (Tambahkan di sini) -->
            <div class="container-fluid pt-3">
                @include('sisfo::layouts.flash-message')
            </div>
            @include('sisfo::layouts.breadcrumb')

            <section class="content">
                @yield('content')
            </section>
        </div>
        @include('sisfo::layouts.footer')
    </div>

    <!-- jQuery (hanya dimuat sekali) -->
    <script src="{{ sisfo_asset('/modules/sisfo/adminlte/plugins/jquery/jquery.min.js') }}"></script>
    
    <!-- Bootstrap 4 (hanya dimuat sekali) -->
    <script src="{{ sisfo_asset('/modules/sisfo/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- DataTables & Plugins -->
    <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    
    <!-- jquery-validation -->
    <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    
    <!-- SweetAlert2 -->
    <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    
    <!-- AdminLTE App -->
    <script src="{{ sisfo_asset('/modules/sisfo/adminlte/dist/js/adminlte.min.js') }}"></script>
    
    <!-- Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
    
    <!-- Custom Summernote JS (dari Controller) -->
    <script src="{{ url('/js/summernote.js') }}"></script>

    <script>
        // Untuk Mengirimkan token laravel CSRF pada setiap request ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Konfigurasi toastr global
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "showDuration": "100",
            "hideDuration": "300",
            "timeOut": "3000",
            "extendedTimeOut": "500"
        };
    </script>

    <!-- Script untuk memperbaiki dropdown yang tidak berfungsi -->
    <script>
        $(document).ready(function() {
            // Inisialisasi dropdown dengan cara yang lebih robust
            try {
                // Hapus event listener sebelumnya untuk mencegah duplikasi
                $('.dropdown-toggle').off('click');
                
                // Perbaiki inisialisasi dropdown untuk halaman Menu Management
                $('.dropdown-toggle').each(function() {
                    $(this).on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        // Toggle dropdown
                        var dropdownMenu = $(this).next('.dropdown-menu');
                        
                        // Tutup dropdown lain yang terbuka
                        $('.dropdown-menu').not(dropdownMenu).removeClass('show');
                        
                        // Toggle dropdown saat ini
                        dropdownMenu.toggleClass('show');
                        
                        console.log('Dropdown diklik manual');
                    });
                });
                
                // Tutup dropdown saat klik di luar
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.dropdown').length) {
                        $('.dropdown-menu').removeClass('show');
                    }
                });
                
                console.log('Dropdown berhasil diinisialisasi dengan pengaturan manual');
            } catch (error) {
                console.error('Terjadi kesalahan saat mengatur dropdown:', error);
                
                // Fallback jika terjadi error: coba gunakan Bootstrap dropdown
                try {
                    $('.dropdown-toggle').dropdown();
                    console.log('Menggunakan Bootstrap dropdown sebagai fallback');
                } catch(err) {
                    console.error('Bootstrap dropdown juga error:', err);
                }
            }
            
            // Pastikan dropdown memiliki class yang dibutuhkan
            if (!$('.nav-item.dropdown').hasClass('dropdown')) {
                $('.nav-item.dropdown').addClass('dropdown');
            }
        });
    </script>
    @stack('js')
</body>
</html>