@php 
use Modules\Sisfo\App\Models\Website\WebMenuModel;
$penyelesaianSengketaUrl = WebMenuModel::getDynamicMenuUrl('penyelesaian-sengketa');
@endphp
<div class="modal-header">
    <h5 class="modal-title">Edit Data Penyelesaian Sengketa</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <form id="form-update-sengketa" action="{{ url($penyelesaianSengketaUrl . '/updateData/' . $penyelesaianSengketa->penyelesaian_sengketa_id) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{ $penyelesaianSengketa->id }}">
        <div class="form-group">
            <label for="ps_kode">Kode Penyelesaian Sengketa <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="ps_kode" name="m_penyelesaian_sengketa[ps_kode]" 
                maxlength="20" placeholder="Masukkan kode penyelesaian sengketa" value="{{ $penyelesaianSengketa->ps_kode }}">
            <div class="invalid-feedback" id="ps_kode_error"></div>
            <small class="form-text text-muted">Contoh: PS-001</small>
            </div>
            <div class="form-group">
               <label for="ps_nama">Nama Penyelesaian Sengketa <span class="text-danger">*</span></label>
               <input type="text" class="form-control" id="ps_nama" name="m_penyelesaian_sengketa[ps_nama]" 
                   maxlength="255" placeholder="Masukkan nama penyelesaian sengketa" value="{{ $penyelesaianSengketa->ps_nama }}">
               <div class="invalid-feedback" id="ps_nama_error"></div>
               </div>
               <div class="form-group">
               <label for="ps_deskripsi">Deskripsi Penyelesaian Sengketa <span class="text-danger">*</span></label>
               <textarea class="form-control" id="ps_deskripsi" name="m_penyelesaian_sengketa[ps_deskripsi]" 
                   rows="4" placeholder="Masukkan deskripsi penyelesaian sengketa">{{ $penyelesaianSengketa->ps_deskripsi }}</textarea>     
               <div class="invalid-feedback" id="ps_deskripsi_error"></div>
               </div>
          </form>
     </div>
     
     <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
         <button type="button" class="btn btn-primary" id="btnSubmitForm">
             <i class="fas fa-save mr-1"></i> Simpan
         </button>
     </div>
     
     <script>
     $(document).ready(function () {
         // Initialize Summernote
         $('#ps_deskripsi').summernote({
             placeholder: 'Masukkan deskripsi penyelesaian sengketa...',
             tabsize: 2,
             height: 200,
             toolbar: [
                 ['style', ['style']],
                 ['font', ['bold', 'underline', 'italic', 'clear', 'fontsize', 'fontname']],
                 ['color', ['color']],
                 ['para', ['ul', 'ol', 'paragraph', 'height', 'align']],
                 ['table', ['table']],
                 ['insert', ['link', 'picture']],
                 ['view', ['fullscreen', 'codeview', 'help']]
             ],
             callbacks: {
                 onChange: function(contents) {
                     $(this).next('.note-editor').removeClass('is-invalid');
                     $('#ps_deskripsi_error').html('');
                 }
             }
         });
     
         // Form submission handling
         $('#btnSubmitForm').on('click', function() {
             $('.is-invalid').removeClass('is-invalid');
             $('.invalid-feedback').html('');
             
             const form = $('#form-update-sengketa');
             const formData = new FormData(form[0]);
             const button = $(this);
     
             // Validate required fields
             let isValid = true;
             
             // Validate ps_kode
             const psKode = $('#ps_kode').val();
             if (psKode === '') {
                 isValid = false;
                 $('#ps_kode').addClass('is-invalid');
                 $('#ps_kode_error').html('Kode Penyelesaian Sengketa wajib diisi.');
             }
     
             // Validate ps_nama
             const psNama = $('#ps_nama').val();
             if (psNama === '') {
                 isValid = false;
                 $('#ps_nama').addClass('is-invalid');
                 $('#ps_nama_error').html('Nama Penyelesaian Sengketa wajib diisi.');
             }
     
             // Validate ps_deskripsi
             const psDeskripsi = $('#ps_deskripsi').summernote('code');
             if (psDeskripsi === '' || psDeskripsi === '<p><br></p>') {
                 isValid = false;
                 $('#ps_deskripsi').next('.note-editor').addClass('is-invalid');
                 $('#ps_deskripsi_error').html('Deskripsi wajib diisi.');
             }
     
             if (!isValid) {
                 Swal.fire({
                     icon: 'error',
                     title: 'Validasi Gagal',
                     text: 'Mohon periksa kembali input Anda'
                 });
                 return;
             }
     
             // Update form data with Summernote content
             formData.set('m_penyelesaian_sengketa[ps_deskripsi]', psDeskripsi);
             
             // Show loading state
             button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').attr('disabled', true);
     
             // Submit form
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
                                 if (key.startsWith('m_penyelesaian_sengketa.')) {
                                     const fieldName = key.replace('m_penyelesaian_sengketa.', '');
                                     if (fieldName === 'ps_deskripsi') {
                                         $('#ps_deskripsi').next('.note-editor').addClass('is-invalid');
                                         $('#ps_deskripsi_error').html(value[0]);
                                     } else {
                                         $(`#${fieldName}`).addClass('is-invalid');
                                         $(`#${fieldName}_error`).html(value[0]);
                                     }
                                 }
                             });
                         }
                         Swal.fire({
                             icon: 'error',
                             title: 'Gagal',
                             text: response.message || 'Terjadi kesalahan saat menyimpan data'
                         });
                     }
                 },
                 error: function() {
                     Swal.fire({
                         icon: 'error',
                         title: 'Gagal',
                         text: 'Terjadi kesalahan saat menyimpan data'
                     });
                 },
                 complete: function() {
                     button.html('<i class="fas fa-save mr-1"></i> Simpan').attr('disabled', false);
                 }
             });
         });
     });
     </script>



