<div class="modal-header">
  <h5 class="modal-title">Tambah Kategori Akses</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>

<div class="modal-body">
  <form id="formCreateKategoriAkses" action="{{ url('adminweb/kategori-akses/createData') }}" method="POST">
      @csrf

      <div class="form-group">
          <label for="mka_judul_kategori">Judul Kategori <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="mka_judul_kategori" name="m_kategori_akses[mka_judul_kategori]" maxlength="100">
          <div class="invalid-feedback" id="mka_judul_kategori_error"></div>
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
  // Hapus error ketika input berubah
  $(document).on('input change', 'input, select, textarea', function() {
    $(this).removeClass('is-invalid');
    const errorId = `#${$(this).attr('id')}_error`;
    $(errorId).html('');
  });

  // Fungsi untuk validasi form
  function validateForm() {
    let isValid = true;

    // Validasi Judul Kategori
    const judulKategori = $('#mka_judul_kategori').val().trim();
    if (judulKategori === '') {
      $('#mka_judul_kategori').addClass('is-invalid');
      $('#mka_judul_kategori_error').html('Judul Kategori wajib diisi.');
      isValid = false;
    } else if (judulKategori.length > 100) {
      $('#mka_judul_kategori').addClass('is-invalid');
      $('#mka_judul_kategori_error').html('Maksimal 100 karakter.');
      isValid = false;
    }

    return isValid;
  }

  // Handle submit form
  $('#btnSubmitForm').on('click', function() {
    // Reset semua error
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').html('');

    // Validasi form terlebih dahulu
    if (!validateForm()) {
      Swal.fire({
        icon: 'error',
        title: 'Terjadi Kesalahan',
        text: 'Mohon periksa kembali input Anda.'
      });
      return;
    }

    const form = $('#formCreateKategoriAkses');
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
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: response.message || 'Terjadi kesalahan saat menyimpan data'
          });
        }
      },
      error: function(xhr) {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
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
