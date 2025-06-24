@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $managementUserUrl = WebMenuModel::getDynamicMenuUrl('management-user');
@endphp
<div class="modal-header">
  <h5 class="modal-title">Ubah Pengguna</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">
  <form id="formUpdateUser" action="{{ url($managementUserUrl . '/updateData/' . $user->user_id) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
      <label for="nama_pengguna">Nama Pengguna <span class="text-danger">*</span></label>
      <input type="text" class="form-control" id="nama_pengguna" name="m_user[nama_pengguna]" maxlength="50" value="{{ $user->nama_pengguna }}">
      <div class="invalid-feedback" id="nama_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="email_pengguna">Email <span class="text-danger">*</span></label>
      <input type="email" class="form-control" id="email_pengguna" name="m_user[email_pengguna]" maxlength="255" value="{{ $user->email_pengguna }}">
      <div class="invalid-feedback" id="email_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="no_hp_pengguna">Nomor HP <span class="text-danger">*</span></label>
      <input type="text" class="form-control" id="no_hp_pengguna" name="m_user[no_hp_pengguna]" maxlength="15" value="{{ $user->no_hp_pengguna }}" pattern="[0-9]+">
      <small class="form-text text-muted">Contoh: 081234567890 (4-15 digit)</small>
      <div class="invalid-feedback" id="no_hp_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="nik_pengguna">NIK <span class="text-danger">*</span></label>
      <input type="text" class="form-control" id="nik_pengguna" name="m_user[nik_pengguna]" maxlength="16" value="{{ $user->nik_pengguna }}" pattern="[0-9]{16}">
      <small class="form-text text-muted">NIK harus 16 digit</small>
      <div class="invalid-feedback" id="nik_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="alamat_pengguna">Alamat <span class="text-danger">*</span></label>
      <textarea class="form-control" id="alamat_pengguna" name="m_user[alamat_pengguna]" rows="3" maxlength="500">{{ $user->alamat_pengguna }}</textarea>
      <div class="invalid-feedback" id="alamat_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="pekerjaan_pengguna">Pekerjaan <span class="text-danger">*</span></label>
      <input type="text" class="form-control" id="pekerjaan_pengguna" name="m_user[pekerjaan_pengguna]" maxlength="100" value="{{ $user->pekerjaan_pengguna }}">
      <div class="invalid-feedback" id="pekerjaan_pengguna_error"></div>
    </div>

    <div class="form-group">
      <label for="upload_nik_pengguna">Upload KTP <small>(biarkan kosong jika tidak ingin mengubah)</small></label>
      @if($user->upload_nik_pengguna)
        <div class="mb-2">
          <a href="{{ asset('storage/' . $user->upload_nik_pengguna) }}" target="_blank" class="btn btn-sm btn-info">
            <i class="fas fa-eye"></i> Lihat KTP Saat Ini
          </a>
        </div>
      @endif
      <div class="custom-file">
        <input type="file" class="custom-file-input" id="upload_nik_pengguna" name="upload_nik_pengguna" accept="image/jpeg,image/png,image/jpg">
        <label class="custom-file-label" for="upload_nik_pengguna">Pilih file gambar KTP baru</label>
        <div class="invalid-feedback" id="upload_nik_pengguna_error"></div>
        <small class="form-text text-muted">
          * Format: JPG, PNG, JPEG
          <br>
          * Maksimal 2MB
        </small>
      </div>
    </div>

    <div class="form-group">
      <label for="password">Password <small>(biarkan kosong jika tidak ingin mengubah)</small></label>
      <input type="password" class="form-control" id="password" name="password" minlength="5">
      <small class="form-text text-muted">Password minimal 5 karakter</small>
      <div class="invalid-feedback" id="password_error"></div>
    </div>

    <div class="form-group">
      <label for="password_confirmation">Konfirmasi Password</label>
      <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" minlength="5">
      <div class="invalid-feedback" id="password_confirmation_error"></div>
    </div>

    <div class="card mt-4">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Hak Akses</h5>
      </div>
      <div class="card-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Nama Level</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($user->hakAkses as $hakAkses)
              <tr>
                <td>{{ $hakAkses->hak_akses_kode }}</td>
                <td>{{ $hakAkses->hak_akses_nama }}</td>
                <td>
                  @if(count($user->hakAkses) > 1)
                    @if(Auth::user()->level->hak_akses_kode === 'SAR' || $hakAkses->hak_akses_kode !== 'SAR')
                    <button type="button" class="btn btn-sm btn-danger hapus-hak-akses" data-id="{{ $hakAkses->hak_akses_id }}">
                      <i class="fas fa-trash"></i> Hapus
                    </button>
                    @endif
                  @else
                    <span class="text-muted">Minimal 1 hak akses</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center">Tidak ada hak akses</td>
              </tr>
            @endforelse
          </tbody>
        </table>

        @if(count($availableHakAkses) > 0)
          <div class="form-group mt-3">
            <label for="add_hak_akses">Tambah Hak Akses</label>
            <div class="input-group">
              <select class="form-control" id="add_hak_akses">
                <option value="">-- Pilih Hak Akses --</option>
                @foreach($availableHakAkses as $hakAkses)
                  <option value="{{ $hakAkses->hak_akses_id }}">{{ $hakAkses->hak_akses_nama }}</option>
                @endforeach
              </select>
              <div class="input-group-append">
                <button class="btn btn-success" type="button" id="btnAddHakAkses">
                  <i class="fas fa-plus"></i> Tambah
                </button>
              </div>
            </div>
          </div>
        @endif
      </div>
    </div>
  </form>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
  <button type="button" class="btn btn-primary" id="btnSubmitForm">
    <i class="fas fa-save mr-1"></i> Simpan Perubahan
  </button>
</div>

<script>
  $(document).ready(function () {
    // Tampilkan nama file yang dipilih
    $('.custom-file-input').on('change', function () {
      var fileName = $(this).val().split('\\').pop();
      $(this).siblings('.custom-file-label').addClass('selected').html(fileName || 'Pilih file gambar KTP baru');
    });

    // Hapus error ketika input berubah
    $(document).on('input change', 'input, select, textarea', function() {
      $(this).removeClass('is-invalid');
      const errorId = `#${$(this).attr('id')}_error`;
      $(errorId).html('');
    });

    // Fungsi validasi form untuk update
    function validateUpdateForm() {
      let isValid = true;

      // Ambil nilai dari form
      const nama = $('#nama_pengguna').val().trim();
      const email = $('#email_pengguna').val().trim();
      const noHp = $('#no_hp_pengguna').val().trim();
      const nik = $('#nik_pengguna').val().trim();
      const alamat = $('#alamat_pengguna').val().trim();
      const pekerjaan = $('#pekerjaan_pengguna').val().trim();
      const password = $('#password').val();
      const passwordConfirmation = $('#password_confirmation').val();
      const file = $('#upload_nik_pengguna')[0].files[0];

      // Reset semua error
      $('.is-invalid').removeClass('is-invalid');
      $('.invalid-feedback').html('');

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

      // Validasi Password (opsional untuk update)
      if (password !== '') {
        if (password.length < 5) {
          $('#password').addClass('is-invalid');
          $('#password_error').html('Password minimal 5 karakter.');
          isValid = false;
        }

        // Validasi Konfirmasi Password jika password diisi
        if (passwordConfirmation === '') {
          $('#password_confirmation').addClass('is-invalid');
          $('#password_confirmation_error').html('Konfirmasi password wajib diisi jika password diubah.');
          isValid = false;
        } else if (password !== passwordConfirmation) {
          $('#password_confirmation').addClass('is-invalid');
          $('#password_confirmation_error').html('Konfirmasi password tidak sesuai.');
          isValid = false;
        }
      } else {
        // Jika password kosong, pastikan konfirmasi juga kosong
        if (passwordConfirmation !== '') {
          $('#password_confirmation').addClass('is-invalid');
          $('#password_confirmation_error').html('Konfirmasi password harus kosong jika password tidak diubah.');
          isValid = false;
        }
      }

      // Validasi File Upload KTP (opsional untuk update)
      if (file) {
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
      // Validasi form sebelum submit
      if (!validateUpdateForm()) {
        Swal.fire({
          icon: 'error',
          title: 'Validasi Gagal',
          text: 'Mohon periksa kembali input Anda.'
        });
        return;
      }
      
      const form = $('#formUpdateUser');
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
          button.html('<i class="fas fa-save mr-1"></i> Simpan Perubahan').attr('disabled', false);
        }
      });
    });

    // Handle hapus hak akses
    $(document).on('click', '.hapus-hak-akses', function() {
      const hakAksesId = $(this).data('id');
      const isCurrentUser = {{ Auth::id() == $user->user_id ? 'true' : 'false' }};
      const activeHakAksesId = {{ session('active_hak_akses_id') ? session('active_hak_akses_id') : 0 }};
      const isActiveHakAkses = isCurrentUser && (hakAksesId == activeHakAksesId);
      
      let confirmText = 'Apakah Anda yakin ingin menghapus hak akses ini?';
      if (isActiveHakAkses) {
        confirmText = 'Jika Anda menghapus hak akses ini, Anda akan terlogout dari sistem. Lanjutkan?';
      }
      
      Swal.fire({
        title: 'Konfirmasi',
        text: confirmText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '{{ url($managementUserUrl . "/removeHakAkses/" . $user->user_id) }}',
            type: 'POST',
            data: {
              hak_akses_id: hakAksesId,
              _method: 'DELETE'
            },
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
              if (response.success) {
                // Cek berdasarkan response dari server apakah hak akses yang dihapus adalah yang aktif
                if (response.data && response.data.is_active_hak_akses) {
                  Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Hak akses berhasil dihapus. Anda akan dialihkan ke halaman logout.',
                    allowOutsideClick: false
                  }).then(() => {
                    window.location.href = '{{ url("logout") }}';
                  });
                } else {
                  // Muat ulang modal
                  modalAction('{{ url($managementUserUrl . "/editData/" . $user->user_id) }}');
                  
                  Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message
                  });
                }
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Gagal',
                  text: response.message || 'Terjadi kesalahan saat menghapus hak akses'
                });
              }
            },
            error: function(xhr) {
              let errorMessage = 'Terjadi kesalahan saat menghapus hak akses. Silakan coba lagi.';
              if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
              }
              
              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: errorMessage
              });
            }
          });
        }
      });
    });

    // Handle tambah hak akses
    $('#btnAddHakAkses').on('click', function() {
      const hakAksesId = $('#add_hak_akses').val();
      if (!hakAksesId) {
        Swal.fire({
          icon: 'warning',
          title: 'Perhatian',
          text: 'Pilih hak akses terlebih dahulu'
        });
        return;
      }
      
      $.ajax({
        url: '{{ url($managementUserUrl . "/addHakAkses/" . $user->user_id) }}',
        type: 'POST',
        data: {
          hak_akses_id: hakAksesId
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          if (response.success) {
            // Muat ulang modal
            modalAction('{{ url($managementUserUrl . "/editData/" . $user->user_id) }}');
            
            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: response.message
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Gagal',
              text: response.message || 'Terjadi kesalahan saat menambahkan hak akses'
            });
          }
        },
        error: function(xhr) {
          let errorMessage = 'Terjadi kesalahan saat menambahkan hak akses. Silakan coba lagi.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }
          
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: errorMessage
          });
        }
      });
    });
  });
</script>