<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\InformasiPublik\IpDinamisTabel\update.blade.php -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
   $IpdinamisTabelUrl  = WebMenuModel::getDynamicMenuUrl('kategori-informasi-publik-dinamis-tabel');
@endphp
<div class="modal-header">
     <h5 class="modal-title">Edit IP Dinamis Tabel</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span>
     </button>
   </div>
   
   <div class="modal-body">
     <form id="formUpdateIpDinamisTabel" action="{{ url($IpdinamisTabelUrl . '/updateData/' . $IpDinamisTabel->ip_dinamis_tabel_id) }}" method="POST">
       @csrf
   
       <div class="form-group">
         <label for="m_ip_dinamis_tabel_ip_nama_submenu">Nama Submenu <span class="text-danger">*</span></label>
         <input type="text" class="form-control" id="m_ip_dinamis_tabel_ip_nama_submenu" name="m_ip_dinamis_tabel[ip_nama_submenu]" 
                value="{{ $IpDinamisTabel->ip_nama_submenu }}" maxlength="100">
         <div class="invalid-feedback" id="m_ip_dinamis_tabel.ip_nama_submenu.required_error"></div>
       </div>
   
       <div class="form-group">
         <label for="m_ip_dinamis_tabel_ip_judul">Judul <span class="text-danger">*</span></label>
         <input type="text" class="form-control" id="m_ip_dinamis_tabel_ip_judul" name="m_ip_dinamis_tabel[ip_judul]" 
                value="{{ $IpDinamisTabel->ip_judul }}" maxlength="100">
         <div class="invalid-feedback" id="m_ip_dinamis_tabel.ip_judul.required_error"></div>
       </div>

       <div class="form-group">
         <label for="m_ip_dinamis_tabel_ip_deskripsi">Deskripsi</label>
         <textarea class="form-control" id="m_ip_dinamis_tabel_ip_deskripsi" name="m_ip_dinamis_tabel[ip_deskripsi]" 
                   rows="4" maxlength="1000" placeholder="Masukkan deskripsi (opsional)">{{ $IpDinamisTabel->ip_deskripsi }}</textarea>
         <small class="form-text text-muted">Maksimal 1000 karakter</small>
         <div class="invalid-feedback" id="m_ip_dinamis_tabel.ip_deskripsi.max_error"></div>
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
       // Menghapus error saat user mengedit input
       $(document).on('input change', 'input, select, textarea', function() {
         $(this).removeClass('is-invalid');
         const errorId = `#${$(this).attr('id').replace(/_/g, '.')}_error`;
         $(errorId).html('');
       });

       // Character counter untuk textarea
       $('#m_ip_dinamis_tabel_ip_deskripsi').on('input', function() {
         const maxLength = 1000;
         const currentLength = $(this).val().length;
         const remaining = maxLength - currentLength;
         
         if (remaining < 0) {
           $(this).addClass('is-invalid');
           $('#m_ip_dinamis_tabel\\.ip_deskripsi\\.max_error').html(`Deskripsi melebihi batas maksimal ${Math.abs(remaining)} karakter.`);
         } else {
           $(this).removeClass('is-invalid');
           $('#m_ip_dinamis_tabel\\.ip_deskripsi\\.max_error').html('');
         }
         
         // Update helper text
         $(this).siblings('.form-text').html(`Maksimal 1000 karakter (sisa: ${remaining})`);
       });

       // Trigger character counter on load
       $('#m_ip_dinamis_tabel_ip_deskripsi').trigger('input');
   
       $('#btnSubmitForm').on('click', function() {
         // Bersihkan error
         $('.is-invalid').removeClass('is-invalid');
         $('.invalid-feedback').html('');
   
         const form = $('#formUpdateIpDinamisTabel');
         const formData = new FormData(form[0]);
         const button = $(this);
         
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
                 $.each(response.errors, function(key, value) {
                   // Replace dot with underscore for ID selector
                   const inputId = key.replace(/\./g, '_');
                   $(`#${inputId}`).addClass('is-invalid');
                   $(`#${key.replace(/\./g, '\\.')}_error`).html(value[0]);
                 });
                 Swal.fire({
                   icon: 'error',
                   title: 'Terjadi Kesalahan',
                   text: response.message || 'Mohon periksa kembali input Anda'
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
             button.html('<i class="fas fa-save mr-1"></i> Simpan').attr('disabled', false);
           }
         });
       });
     });
   </script>