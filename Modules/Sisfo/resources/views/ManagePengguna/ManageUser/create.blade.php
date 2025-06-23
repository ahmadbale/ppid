@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $managementUserUrl = WebMenuModel::getDynamicMenuUrl('management-user');
@endphp
<div class="modal-header">
  <h5 class="modal-title">Tambah Pengguna Baru</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">
  <form id="formCreateUser" action="{{ url($managementUserUrl . '/createData') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
      <label for="nama_pengguna">Nama Pengguna <span class="text-danger">*</span></label>
      <input type="text" class="form-control" id="nama_pengguna" name="m_user[nama_pengguna]" maxlength="50">
      <div class="invalid-feedback" id="nama_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="email_pengguna">Email <span class="text-danger">*</span></label>
      <input type="email" class="form-control" id="email_pengguna" name="m_user[email_pengguna]" maxlength="255">
      <div class="invalid-feedback" id="email_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="no_hp_pengguna">Nomor HP <span class="text-danger">*</span></label>
      <input type="text" class="form-control" id="no_hp_pengguna" name="m_user[no_hp_pengguna]" maxlength="15" pattern="[0-9]+">
      <small class="form-text text-muted">Contoh: 081234567890 (4-15 digit)</small>
      <div class="invalid-feedback" id="no_hp_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="nik_pengguna">NIK <span class="text-danger">*</span></label>
      <input type="text" class="form-control" id="nik_pengguna" name="m_user[nik_pengguna]" maxlength="16" pattern="[0-9]{16}">
      <small class="form-text text-muted">NIK harus 16 digit</small>
      <div class="invalid-feedback" id="nik_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="alamat_pengguna">Alamat <span class="text-danger">*</span></label>
      <textarea class="form-control" id="alamat_pengguna" name="m_user[alamat_pengguna]" rows="3" maxlength="500"></textarea>
      <div class="invalid-feedback" id="alamat_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="pekerjaan_pengguna">Pekerjaan <span class="text-danger">*</span></label>
      <input type="text" class="form-control" id="pekerjaan_pengguna" name="m_user[pekerjaan_pengguna]" maxlength="100">
      <div class="invalid-feedback" id="pekerjaan_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="upload_nik_pengguna">Upload KTP <span class="text-danger">*</span></label>
      <div class="custom-file">
        <input type="file" class="custom-file-input" id="upload_nik_pengguna" name="upload_nik_pengguna" accept="image/jpeg,image/png,image/jpg">
        <label class="custom-file-label" for="upload_nik_pengguna">Pilih file gambar KTP</label>
        <div class="invalid-feedback" id="upload_nik_pengguna_error"></div>
        <small class="form-text text-muted">
          * Format: JPG, PNG, JPEG
          <br>
          * Maksimal 2MB
        </small>
      </div>
    </div>

    <div class="form-group">
      <label for="hak_akses_id">Level Pengguna <span class="text-danger">*</span></label>
      <select class="form-control" id="hak_akses_id" name="hak_akses_id">
        <option value="">-- Pilih Level --</option>
        @foreach($hakAkses as $hak)
          <option value="{{ $hak->hak_akses_id }}" {{ isset($selectedLevel) && $selectedLevel->hak_akses_id == $hak->hak_akses_id ? 'selected' : '' }}>
            {{ $hak->hak_akses_nama }}
          </option>
        @endforeach
      </select>
      <div class="invalid-feedback" id="hak_akses_id_error"></div>
    </div>

    <div class="form-group">
      <label for="password">Password <span class="text-danger">*</span></label>
      <input type="password" class="form-control" id="password" name="password" minlength="5">
      <small class="form-text text-muted">Password minimal 5 karakter</small>
      <div class="invalid-feedback" id="password_error"></div>
    </div>

    <div class="form-group">
      <label for="password_confirmation">Konfirmasi Password <span class="text-danger">*</span></label>
      <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" minlength="5">
      <div class="invalid-feedback" id="password_confirmation_error"></div>
    </div>
  </form>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
  <button type="button" class="btn btn-success" id="btnSubmitForm">
    <i class="fas fa-save mr-1"></i> Simpan
  </button>
</div>

<script>
  $(document).ready(function () {
    // Tampilkan nama file yang dipilih
    $('.custom-file-input').on('change', function () {
      var fileName = $(this).val().split('\\').pop();
      $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
    });

    // Hapus error ketika input berubah
    $(document).on('input change', 'input, select, textarea', function() {
      $(this).removeClass('is-invalid');
      const errorId = `#${$(this).attr('id')}_error`;
      $(errorId).html('');
    });

    // Fungsi validasi form
    function validateForm() {
      let isValid = true;

      // Ambil nilai dari form
      const nama = $('#nama_pengguna').val().trim();
      const email = $('#email_pengguna').val().trim();
      const noHp = $('#no_hp_pengguna').val().trim();
      const nik = $('#nik_pengguna').val().trim();
      const alamat = $('#alamat_pengguna').val().trim();
      const pekerjaan = $('#pekerjaan_pengguna').val().trim();
      const hakAkses = $('#hak_akses_id').val();
      const password = $('#password').val();
      const passwordConfirmation = $('#password_confirmation').val();
      const file = $('#upload_nik_pengguna')[0].files[0];

      // Validasi Nama Pengguna
      if (nama === '') {
        $('#nama_pengguna').addClass('is-invalid');
        $('#nama_pengguna_error').html('Nama pengguna wajib diisi.');
        isValid = false;
      } else if (nama.length < 2) {
        $('#nama_pengguna').addClass('is-invalid');
        $('#nama_pengguna_error').html('Nama minimal 2 karakter.');
        isValid = false;
      } else if (nama.length > 50) {
        $('#nama_pengguna').addClass('is-invalid');
        $('#nama_pengguna_error').html('Nama maksimal 50 karakter.');
        isValid = false;
      }

      // Validasi Email
      if (email === '') {
        $('#email_pengguna').addClass('is-invalid');
        $('#email_pengguna_error').html('Email wajib diisi.');
        isValid = false;
      } else {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
          $('#email_pengguna').addClass('is-invalid');
          $('#email_pengguna_error').html('Format email tidak valid.');
          isValid = false;
        } else if (email.length > 255) {
          $('#email_pengguna').addClass('is-invalid');
          $('#email_pengguna_error').html('Email maksimal 255 karakter.');
          isValid = false;
        }
      }

      // Validasi Nomor HP
      if (noHp === '') {
        $('#no_hp_pengguna').addClass('is-invalid');
        $('#no_hp_pengguna_error').html('Nomor HP wajib diisi.');
        isValid = false;
      } else {
        const phonePattern = /^[0-9]+$/;
        if (!phonePattern.test(noHp)) {
          $('#no_hp_pengguna').addClass('is-invalid');
          $('#no_hp_pengguna_error').html('Nomor HP hanya boleh berisi angka.');
          isValid = false;
        } else if (noHp.length < 4 || noHp.length > 15) {
          $('#no_hp_pengguna').addClass('is-invalid');
          $('#no_hp_pengguna_error').html('Nomor HP harus 4-15 digit.');
          isValid = false;
        }
      }

      // Validasi NIK
      if (nik === '') {
        $('#nik_pengguna').addClass('is-invalid');
        $('#nik_pengguna_error').html('NIK wajib diisi.');
        isValid = false;
      } else {
        const nikPattern = /^[0-9]{16}$/;
        if (!nikPattern.test(nik)) {
          $('#nik_pengguna').addClass('is-invalid');
          $('#nik_pengguna_error').html('NIK harus 16 digit angka.');
          isValid = false;
        }
      }

      // Validasi Alamat
      if (alamat === '') {
        $('#alamat_pengguna').addClass('is-invalid');
        $('#alamat_pengguna_error').html('Alamat wajib diisi.');
        isValid = false;
      } else if (alamat.length > 500) {
        $('#alamat_pengguna').addClass('is-invalid');
        $('#alamat_pengguna_error').html('Alamat maksimal 500 karakter.');
        isValid = false;
      }

      // Validasi Pekerjaan
      if (pekerjaan === '') {
        $('#pekerjaan_pengguna').addClass('is-invalid');
        $('#pekerjaan_pengguna_error').html('Pekerjaan wajib diisi.');
        isValid = false;
      } else if (pekerjaan.length > 100) {
        $('#pekerjaan_pengguna').addClass('is-invalid');
        $('#pekerjaan_pengguna_error').html('Pekerjaan maksimal 100 karakter.');
        isValid = false;
      }

      // Validasi Level Pengguna
      if (hakAkses === '') {
        $('#hak_akses_id').addClass('is-invalid');
        $('#hak_akses_id_error').html('Level pengguna wajib dipilih.');
        isValid = false;
      }

      // Validasi Password
      if (password === '') {
        $('#password').addClass('is-invalid');
        $('#password_error').html('Password wajib diisi.');
        isValid = false;
      } else if (password.length < 5) {
        $('#password').addClass('is-invalid');
        $('#password_error').html('Password minimal 5 karakter.');
        isValid = false;
      }

      // Validasi Konfirmasi Password
      if (passwordConfirmation === '') {
        $('#password_confirmation').addClass('is-invalid');
        $('#password_confirmation_error').html('Konfirmasi password wajib diisi.');
        isValid = false;
      } else if (password !== passwordConfirmation) {
        $('#password_confirmation').addClass('is-invalid');
        $('#password_confirmation_error').html('Konfirmasi password tidak sesuai.');
        isValid = false;
      }

      // Validasi File Upload KTP
      if (!file) {
        $('#upload_nik_pengguna').addClass('is-invalid');
        $('#upload_nik_pengguna_error').html('Upload KTP wajib dilakukan.');
        isValid = false;
      } else {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        const maxSize = 2 * 1024 * 1024; // 2MB in bytes

        if (!allowedTypes.includes(file.type)) {
          $('#upload_nik_pengguna').addClass('is-invalid');
          $('#upload_nik_pengguna_error').html('File harus berupa gambar (JPG, PNG, JPEG).');
          isValid = false;
        } else if (file.size > maxSize) {
          $('#upload_nik_pengguna').addClass('is-invalid');
          $('#upload_nik_pengguna_error').html('Ukuran file tidak boleh lebih dari 2MB.');
          isValid = false;
        }
      }

      return isValid;
    }

    // Handle submit form
    $('#btnSubmitForm').on('click', function() {
      // Reset semua error
      $('.is-invalid').removeClass('is-invalid');
      $('.invalid-feedback').html('');
      
      // Validasi form sebelum submit
      if (!validateForm()) {
        Swal.fire({
          icon: 'error',
          title: 'Validasi Gagal',
          text: 'Mohon periksa kembali input Anda.'
        });
        return;
      }
      
      const form = $('#formCreateUser');
      const formData = new FormData(form[0]);
      const button = $(this);
      
      // Tampilkan loading state pada tombol submit
      button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);
      
      $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.success) {
            $('#myModal').modal('hide');
            reloadTable();
            
            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: response.message
            });
          } else {
            if (response.errors) {
              // Tampilkan pesan error pada masing-masing field
              $.each(response.errors, function(key, value) {
                // Untuk m_user fields
                if (key.startsWith('m_user.')) {
                  const fieldName = key.replace('m_user.', '');
                  $(`#${fieldName}`).addClass('is-invalid');
                  $(`#${fieldName}_error`).html(value[0]);
                } else {
                  // Untuk field biasa
                  $(`#${key}`).addClass('is-invalid');
                  $(`#${key}_error`).html(value[0]);
                }
              });
              
              Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Mohon periksa kembali input Anda'
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: response.message || 'Terjadi kesalahan saat menyimpan data'
              });
            }
          }
        },
        error: function(xhr) {
          let errorMessage = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }
          
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: errorMessage
          });
        },
        complete: function() {
          // Kembalikan tombol submit ke keadaan semula
          button.html('<i class="fas fa-save mr-1"></i> Simpan').attr('disabled', false);
        }
      });
    });
  });
</script>