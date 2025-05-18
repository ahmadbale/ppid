<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\MenuManagement\WebMenuUrl\create.blade.php -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $webMenuUrlPath = WebMenuModel::getDynamicMenuUrl('management-menu-url');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Tambah URL Menu Baru</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  
  <div class="modal-body">
    <form id="formCreateWebMenuUrl" action="{{ url($webMenuUrlPath . '/createData') }}" method="POST">
      @csrf
  
      <div class="form-group">
        <label for="fk_m_application">Aplikasi <span class="text-danger">*</span></label>
        <select class="form-control" id="fk_m_application" name="web_menu_url[fk_m_application]">
          <option value="">Pilih Aplikasi</option>
          @foreach($applications as $app)
            <option value="{{ $app->application_id }}">{{ $app->app_nama }}</option>
          @endforeach
        </select>
        <div class="invalid-feedback" id="fk_m_application_error"></div>
      </div>

      <div class="form-group">
        <label for="wmu_nama">URL Menu <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="wmu_nama" name="web_menu_url[wmu_nama]" maxlength="255">
        <small class="text-muted">Contoh: management-aplikasi, management-user, E-Form/permohonan-informasi</small>
        <div class="invalid-feedback" id="wmu_nama_error"></div>
      </div>
      
      <div class="form-group">
        <label for="wmu_keterangan">Deskripsi URL Menu</label>
        <textarea class="form-control" id="wmu_keterangan" name="web_menu_url[wmu_keterangan]" rows="3"></textarea>
        <div class="invalid-feedback" id="wmu_keterangan_error"></div>
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
  
      // Handle submit form
      $('#btnSubmitForm').on('click', function() {
        // Reset semua error
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');
        
        const form = $('#formCreateWebMenuUrl');
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
                  // Untuk web_menu_url fields
                  if (key.startsWith('web_menu_url.')) {
                    const fieldName = key.replace('web_menu_url.', '');
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