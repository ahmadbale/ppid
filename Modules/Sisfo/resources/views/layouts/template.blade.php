<!-- resources/views/sisfo/layouts/template.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'test') }}</title>
    <link rel="icon" href="{{ asset('modules/sisfo/logo.png') }}" type="image/png">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Untuk mengirimkan token laravel CSRF pada setiap request ajax -->
    <!--tambahan-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link href="https://fonts.googleapis.com/css2?family=K2D:wght@700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('modules/sisfo/adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- DataTables -->
    <link rel="stylesheet"
        href="{{ asset('modules/sisfo/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('modules/sisfo/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('modules/sisfo/adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet"
        href="{{ asset('modules/sisfo/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('modules/sisfo/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('modules/sisfo/adminlte/dist/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/sisfo/adminlte/dist/css/addstyle.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/sisfo/adminlte/dist/css/info-box.css') }}">

    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <!-- Custom Summernote CSS (dari Controller) -->
    <link href="{{ url('/css/summernote.css') }}" rel="stylesheet">
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

    {{-- chart --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- jQuery -->
    <script src="{{ asset('/modules/sisfo/adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('/modules/sisfo/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('modules/sisfo/adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('modules/sisfo/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('modules/sisfo/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}">
    </script>
    <script src="{{ asset('modules/sisfo/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}">
    </script>
    <script
        src="{{ asset('modules/sisfo/adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script
        src="{{ asset('modules/sisfo/adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('modules/sisfo/adminlte/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('modules/sisfo/adminlte/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('modules/sisfo/adminlte/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('modules/sisfo/adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('modules/sisfo/adminlte/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('modules/sisfo/adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- jquery-validation -->
    <script src="{{ asset('modules/sisfo/adminlte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('modules/sisfo/adminlte/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('modules/sisfo/adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- modules/sisfo/AdminLTE App -->
    <script src="{{ asset('/modules/sisfo/adminlte/dist/js/adminlte.min.js') }}"></script>
    {{-- flatpicker --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- jQuery -->
    <script src="{{ asset('modules/sisfo/adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('modules/sisfo/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('modules/sisfo/adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('modules/sisfo/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('modules/sisfo/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}">
    </script>
    <script src="{{ asset('modules/sisfo/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}">
    </script>
    <script
        src="{{ asset('modules/sisfo/adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script
        src="{{ asset('modules/sisfo/adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('modules/sisfo/adminlte/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('modules/sisfo/adminlte/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('modules/sisfo/adminlte/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('modules/sisfo/adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('modules/sisfo/adminlte/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('modules/sisfo/adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- jquery-validation -->
    <script src="{{ asset('modules/sisfo/adminlte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('modules/sisfo/adminlte/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('modules/sisfo/adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        //Untuk Mengirimkan token laravel CSRF pada setiap request ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Tambahkan script ini di bagian bawah file template.blade.php sebelum tag body ditutup -->
    <script>
        // Script untuk memastikan dropdown berfungsi dengan benar
        $(document).ready(function () {
            // Cek apakah Bootstrap dropdown sudah terinitisasi dengan benar
            if (typeof $.fn.dropdown !== 'undefined') {
                console.log('Bootstrap Dropdown terdeteksi');

                // Aktifkan secara manual dropdown di navbar
                $('.dropdown-toggle').dropdown();

                // Handler dropdown click untuk debugging
                $('.dropdown-toggle').on('click', function (e) {
                    console.log('Dropdown diklik');
                });
            } else {
                console.error('Bootstrap Dropdown tidak terdeteksi. Pastikan Bootstrap JS sudah dimuat dengan benar.');
            }

            // Tambahkan class bootstrap dropdown jika belum ada
            if (!$('.nav-item.dropdown').hasClass('dropdown')) {
                $('.nav-item.dropdown').addClass('dropdown');
            }
        });
    </script>

    <script>
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
        }
    </script>
    @stack('js'){}
    </script>


    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
    <!-- Custom Summernote JS (dari Controller) -->
    <script src="{{ url('/js/summernote.js') }}"></script>
    @stack('js')
</body>

</html>