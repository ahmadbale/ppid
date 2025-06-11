<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Pengguna</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ sisfo_asset('modules/sisfo/adminlte/plugins/fontawesome-free/css/all.min.css') }}">

  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{ sisfo_asset('modules/sisfo/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">

  <!-- SweetAlert2 -->
  <link rel="stylesheet"
    href="{{ sisfo_asset('modules/sisfo/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">

  <!-- Theme style -->
  <link rel="stylesheet" href="{{ sisfo_asset('modules/sisfo/adminlte/dist/css/adminlte.min.css') }}">
</head>

<body class="hold-transition login-page" style="background: url('{{ asset("img/loginadmin.webp") }}') no-repeat center center fixed; background-size: cover;">
{{-- <body class="hold-transition login-page"> --}}
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7);"></div>
    <div class="login-box">
    <!-- /.login-logo -->
    <div class="card">
      <div class="card-header text-center">
        <a href="{{ url('/') }}">
            <img src="{{ asset('img/PPIDlogo.svg') }}" alt="PPID Logo" class="logo">
        </a>
      </div>
      <div class="card-body">
        <h2 class="text-center fw-bold pb-4">Manajemen</br>PPID Polinema</h2>
        <p class="login-box-msg">Masukkan akun anda</p>
        <form action="{{ url('login') }}" method="POST" id="form-login">
          @csrf
          <div class="input-group mb-3">
            <input type="text" id="username" name="username" class="form-control" placeholder="NIK / Email / No HP">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
            <small id="error-username" class="error-text text-danger"></small>
          </div>

          <div class="input-group mb-3">
            <input type="password" id="password" name="password" class="form-control" placeholder="Password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
            <small id="error-password" class="error-text text-danger"></small>
          </div>

          <!-- Register link -->
          {{-- <div class="text-center mb-3">
            <a href="{{ url('register') }}">Tidak Punya Akun? Register</a>
          </div> --}}

          <div class="row pt-4">
            <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember Me</label>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block">Log In</button>
            </div>
            <!-- /.col -->
          </div>
        </form>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/jquery/jquery.min.js') }}"></script>

  <!-- Bootstrap 4 -->
  <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

  <!-- jquery-validation -->
  <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
  <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/jquery-validation/additional-methods.min.js') }}"></script>

  <!-- SweetAlert2 -->
  <script src="{{ sisfo_asset('modules/sisfo/adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

  <!-- AdminLTE App -->
  <script src="{{ sisfo_asset('modules/sisfo/adminlte/dist/js/adminlte.min.js') }}"></script>

  <script>
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $(document).ready(function () {
      $("#form-login").validate({
        rules: {
          username: { required: true },
          password: { required: true, minlength: 5, maxlength: 20 }
        },
        submitHandler: function (form) {
          $.ajax({
            url: form.action,
            type: form.method,
            data: $(form).serialize(),
            success: function (response) {
              console.log('Response:', response); // Tambahkan log untuk debugging

              // Periksa format respons dan ambil data yang benar
              let result = response;
              if (response.data && response.data.original) {
                result = response.data.original;
              }

              if (result.success) {
                Swal.fire({
                  icon: 'success',
                  title: 'Berhasil',
                  text: result.message,
                  showConfirmButton: false,
                  timer: 1500
                }).then(function () {
                  // Gunakan window.location.href dan tambahkan timeout
                  console.log('Redirecting to:', result.redirect);
                  setTimeout(function () {
                    window.location.href = result.redirect;
                  }, 100);
                });
              } else {
                $('.error-text').text('');
                if (result.msgField) {
                  $.each(result.msgField, function (prefix, val) {
                    $('#error-' + prefix).text(val[0]);
                  });
                }
                Swal.fire({
                  icon: 'error',
                  title: 'Terjadi Kesalahan',
                  text: result.message || 'Gagal login'
                });
              }
            },
            error: function (xhr, status, error) {
              console.error("Error:", error, xhr.responseText);
              Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: 'Gagal terhubung ke server. Silakan coba lagi.'
              });
            }
          });
          return false;
        }
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.addClass('invalid-feedback');
          element.closest('.input-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
          $(element).removeClass('is-invalid');
        }
      });
    });

    document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    console.log('Form action:', document.getElementById('form-login').action);
});
  </script>
</body>

</html>
