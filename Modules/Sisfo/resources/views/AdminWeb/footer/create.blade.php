<div class="modal-header">
  <h5 class="modal-title">Tambah Footer Baru</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">
  <form id="formCreateFooter" action="{{ url('adminweb/footer/createData') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
      <label for="fk_m_kategori_footer">Kategori Footer <span class="text-danger">*</span></label>
      <select class="form-control" id="fk_m_kategori_footer" name="t_footer[fk_m_kategori_footer]">
        <option value="">Pilih Kategori Footer</option>
      </select>
      <div class="invalid-feedback" id="fk_m_kategori_footer_error"></div>

    </div>

    <div class="form-group">
      <label for="f_judul_footer">Judul Footer <span class="text-danger">*</span></label>
      <input type="text" class="form-control" id="f_judul_footer" name="t_footer[f_judul_footer]" maxlength="100">
      <div class="invalid-feedback" id="f_judul_footer_error"></div>
    </div>

    <div class="form-group">
      <label for="f_url_footer">URL Footer</label>
      <input type="url" class="form-control" id="f_url_footer" name="t_footer[f_url_footer]" maxlength="100">
      <div class="invalid-feedback" id="f_url_footer_error"></div>
    </div>

    <div class="form-group">
      <label for="f_icon_footer">Ikon Footer</label>
      <div class="custom-file">
        <input type="file" class="custom-file-input" id="f_icon_footer" name="f_icon_footer" accept="image/*">
        <label class="custom-file-label" for="f_icon_footer">Pilih file gambar</label>
        <div class="invalid-feedback" id="f_icon_footer_error"></div>
      </div>
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

    $(document).on('input change', 'input, select, textarea', function () {
      $(this).removeClass('is-invalid');
      const errorId = `#${$(this).attr('id')}_error`;
      $(errorId).html('');
    });

    function validateForm() {
  let isValid = true;

  const kategori = $('#fk_m_kategori_footer').val().trim();
  const judul = $('#f_judul_footer').val().trim();
  const url = $('#f_url_footer').val().trim();
  const file = $('#f_icon_footer')[0].files[0];

  // Validasi kategori
  if (kategori === '') {
    $('#fk_m_kategori_footer').addClass('is-invalid');
    $('#fk_m_kategori_footer_error').html('Kategori Footer wajib dipilih.');
    isValid = false;
  }

  // Validasi judul
  if (judul === '') {
    $('#f_judul_footer').addClass('is-invalid');
    $('#f_judul_footer_error').html('Judul Footer wajib diisi.');
    isValid = false;
  } else if (judul.length > 100) {
    $('#f_judul_footer').addClass('is-invalid');
    $('#f_judul_footer_error').html('Maksimal 100 karakter.');
    isValid = false;
  }

  // Validasi URL (wajib diisi)
  if (url === '') {
    $('#f_url_footer').addClass('is-invalid');
    $('#f_url_footer_error').html('URL Footer wajib diisi.');
    isValid = false;
  } else {
    const urlPattern = /^(https?:\/\/)?([\w\d-]+\.)+[\w-]+(\/[\w\d\-._~:/?#[\]@!$&'()*+,;=]*)?$/;
    if (!urlPattern.test(url)) {
      $('#f_url_footer').addClass('is-invalid');
      $('#f_url_footer_error').html('Format URL tidak valid.');
      isValid = false;
    } else if (url.length > 100) {
      $('#f_url_footer').addClass('is-invalid');
      $('#f_url_footer_error').html('Maksimal 100 karakter.');
      isValid = false;
    }
  }

  // Validasi file (wajib diisi)
  if (!file) {
    $('#f_icon_footer').addClass('is-invalid');
    $('#f_icon_footer_error').html('File ikon footer wajib dipilih.');
    isValid = false;
  } else {
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
      $('#f_icon_footer').addClass('is-invalid');
      $('#f_icon_footer_error').html('Hanya file gambar yang diizinkan (JPG, PNG, GIF, SVG, WebP).');
      isValid = false;
    }
  }

  return isValid;
}


    $('#btnSubmitForm').on('click', function () {
      $('.is-invalid').removeClass('is-invalid');
      $('.invalid-feedback').html('');

      if (!validateForm()) {
        Swal.fire({
          icon: 'error',
          title: 'Validasi Gagal',
          text: 'Mohon periksa kembali input Anda.'
        });
        return;
      }

      const form = $('#formCreateFooter');
      const formData = new FormData(form[0]);
      const button = $(this);

      button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);

      $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
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
              $.each(response.errors, function (key, value) {
                $(`#${key}`).addClass('is-invalid');
                $(`#${key}_error`).html(value[0]);
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
        error: function (xhr) {
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
          });
        },
        complete: function () {
          button.html('<i class="fas fa-save mr-1"></i> Simpan').attr('disabled', false);
        }
      });
    });
  });
</script>
