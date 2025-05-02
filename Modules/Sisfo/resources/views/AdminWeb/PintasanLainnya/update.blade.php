@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $kategoriPintasanLainnyaUrl = WebMenuModel::getDynamicMenuUrl('kategori-pintasan-lainnya');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Edit Pintasan Lainnya</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  
  <div class="modal-body">
    <form id="formUpdatePintasanLainnya" action="{{ url($kategoriPintasanLainnyaUrl . '/updateData/' . $pintasanLainnya->pintasan_lainnya_id) }}" method="POST">
      @csrf
      <div class="form-group">
        <label for="fk_m_kategori_akses">Kategori Akses</label>
        <input type="hidden" id="fk_m_kategori_akses" name="t_pintasan_lainnya[fk_m_kategori_akses]" 
               value="{{ $pintasanLainnya->fk_m_kategori_akses }}">
        <input type="text" class="form-control" value="{{ $pintasanLainnya->kategoriAkses->mka_judul_kategori }}" readonly>
        <div class="invalid-feedback" id="fk_m_kategori_akses_error"></div>
      </div>
  
      <div class="form-group">
        <label for="tpl_nama_kategori">Nama Pintasan <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="tpl_nama_kategori" 
               name="t_pintasan_lainnya[tpl_nama_kategori]" 
               maxlength="255" 
               value="{{ $pintasanLainnya->tpl_nama_kategori }}">
        <div class="invalid-feedback" id="tpl_nama_kategori_error"></div>
      </div>
    </form>
  </div>
  
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-success" id="btnUpdateForm">
      <i class="fas fa-save mr-1"></i> Perbarui
    </button>
  </div>
  
  <script>
  $(document).ready(function () {
    // Clear validation errors on input change
    $(document).on('input change', 'input, select, textarea', function() {
      $(this).removeClass('is-invalid');
      const errorId = `#${$(this).attr('id')}_error`;
      $(errorId).html('');
    });
  
    // Validate form function
    function validateEditForm() {
      // Reset previous validation errors
      $('.is-invalid').removeClass('is-invalid');
      $('.invalid-feedback').html('');
      
      // Flag to track validation status
      let isValid = true;
      
      // Validate Nama Pintasan
      const namaPintasan = $('#tpl_nama_kategori').val().trim();
      
      if (!namaPintasan) {
        $('#tpl_nama_kategori').addClass('is-invalid');
        $('#tpl_nama_kategori_error').html('Nama Pintasan wajib diisi');
        isValid = false;
      } else if (namaPintasan.length < 3) { 
        $('#tpl_nama_kategori').addClass('is-invalid');
        $('#tpl_nama_kategori_error').html('Nama Pintasan minimal 3 karakter');
        isValid = false;
      } else if (namaPintasan.length > 255) {
        $('#tpl_nama_kategori').addClass('is-invalid');
        $('#tpl_nama_kategori_error').html('Nama Pintasan maksimal 255 karakter');
        isValid = false;
      }
      
      // Check for hidden fk_m_kategori_akses
      const kategoriAkses = $('#fk_m_kategori_akses').val();
      if (!kategoriAkses) {
        $('#fk_m_kategori_akses').addClass('is-invalid');
        $('#fk_m_kategori_akses_error').html('Kategori Akses wajib diisi');
        isValid = false;
      }
  
      return isValid;
    }
  
    // Form submission handler
    $('#btnUpdateForm').on('click', function() {
      // Do client-side validation
      if (!validateEditForm()) {
        Swal.fire({
          icon: 'error',
          title: 'Validasi Gagal',
          text: 'Mohon periksa kembali input Anda.'
        });
        return;
      }
  
      // Prepare form data
      const form = $('#formUpdatePintasanLainnya');
      const formData = new FormData(form[0]);
      const button = $(this);
      
      // Disable button and show loading state
      button.html('<i class="fas fa-spinner fa-spin"></i> Memperbarui...').attr('disabled', true);
      
      // AJAX form submission
      $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response.success) {
            // Close modal and reload table on success
            $('#myModal').modal('hide');
            
            // Assuming reloadTable() is defined elsewhere
            if (typeof reloadTable === 'function') {
              reloadTable();
            }
            
            // Success notification
            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: response.message || 'Data berhasil diperbarui'
            });
          } else {
            // Handle error response
            Swal.fire({
              icon: 'error',
              title: 'Gagal',
              text: response.message || 'Terjadi kesalahan saat memperbarui data'
            });
          }
        },
        error: function(xhr) {
          // Detailed error logging
          console.error('Submission Error:', xhr);
          
          // Error notification
          Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Terjadi kesalahan saat mengirim data. Silakan coba lagi.'
          });
        },
        complete: function() {
          // Restore button state
          button.html('<i class="fas fa-save mr-1"></i> Perbarui').attr('disabled', false);
        }
      });
    });
  });
  </script>