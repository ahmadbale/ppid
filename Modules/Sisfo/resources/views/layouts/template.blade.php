<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name', 'PWL Laravel Starter Code')}}</title>
  <link rel="icon" href="{{ asset('modules/sisfo/logo.png') }}" type="image/png">

  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Untuk mengirimkan token laravel CSRF pada setiap request ajax -->
  <!--tambahan-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('modules/sisfo/adminlte/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- DataTables -->
  <link rel="stylesheet"
    href="{{ asset('modules/sisfo/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet"
    href="{{ asset('modules/sisfo/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet"
    href="{{ asset('modules/sisfo/adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="{{ asset('modules/sisfo/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">

  <link rel="stylesheet"
    href="{{ asset('modules/sisfo/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">

  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('modules/sisfo/adminlte/dist/css/adminlte.min.css')}}">

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

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('/profile') }}" class="brand-link">
      <img src="{{ Auth::user()->foto_profil ? asset('storage/' . Auth::user()->foto_profil) : asset('modules/sisfo/user.png') }}"
           alt="User Profile Picture"
           class="brand-image img-circle elevation-3"
           style="width: 32px; height: 40px; object-fit: cover; opacity: .8;">
      <span class="brand-text font-weight-light">{{ Auth::user()->nama_pengguna }}</span>
    </a>

    <!-- Sidebar -->
    @include("sisfo::layouts.sidebar")
    <!-- /.sidebar -->
  </aside>
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="{{ url('/profile') }}" class="brand-link">
        <img
          src="{{ Auth::user()->foto_profil ? asset('storage/' . Auth::user()->foto_profil) : asset('modules/sisfo/user.png') }}"
          alt="User Profile Picture" class="brand-image img-circle elevation-3"
          style="width: 32px; height: 40px; object-fit: cover; opacity: .8;">
        <span class="brand-text font-weight-light">{{ Auth::user()->nama_pengguna }}</span>
      </a>

      <!-- Sidebar -->
      @include('sisfo::layouts.sidebar')
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      @include('sisfo::layouts.breadcrumb')

      <!-- Main content -->
      <section class="content">
        @yield('content')
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    @include('sisfo::layouts.footer')
  </div>
  <!-- ./wrapper -->

<!-- jQuery -->
<script src="{{ asset('/modules/sisfo/adminlte/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('/modules/sisfo/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- DataTables  & Plugins -->
<script src="{{ asset('modules/sisfo/adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('modules/sisfo/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('modules/sisfo/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('modules/sisfo/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('modules/sisfo/adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('modules/sisfo/adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
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
<script src="{{ asset('/modules/sisfo/adminlte/dist/js/adminlte.min.js')}}"></script>
<script>
  <!-- jQuery -->
  <script src="{{ asset('modules/sisfo/adminlte/plugins/jquery/jquery.min.js')}}"></script>
  <!-- Bootstrap 4 -->
  <script src="{{ asset('modules/sisfo/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <!-- DataTables  & Plugins -->
  <script src="{{ asset('modules/sisfo/adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('modules/sisfo/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
  <script
    src="{{ asset('modules/sisfo/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
  <script
    src="{{ asset('modules/sisfo/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('modules/sisfo/adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
  <script src="{{ asset('modules/sisfo/adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
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
  <!-- AdminLTE App -->
  <script src="{{ asset('modules/sisfo/adminlte/dist/js/adminlte.min.js')}}"></script>
  <script>
    //Untuk Mengirimkan token laravel CSRF pada setiap request ajax
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

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
@stack('js')
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

  <!-- Summernote JS -->
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
  <!-- Custom Summernote JS (dari Controller) -->
  <script src="{{ url('/js/summernote.js') }}"></script>
  @stack('js')
</body>

</html>
