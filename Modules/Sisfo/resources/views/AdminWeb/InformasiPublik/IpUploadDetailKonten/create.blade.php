<div class="modal-header">
     <h5 class="modal-title">Tambah Detail Upload Konten Baru</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 
 <div class="modal-body">
   <form id="formCreateDetailUploadKonten" action="{{ url('adminweb/informasipublik/ipupload-detail-konten/createData') }}" method="POST" enctype="multipart/form-data">
     @csrf
     <div class="form-group">
       <label for="fk_m_ip_dinamis_konten">Kategori Konten Dinamis <span class="text-danger">*</span></label>
       <select class="form-control" id="fk_m_ip_dinamis_konten" name="t_ip_upload_konten[fk_m_ip_dinamis_konten]">
         <option value="">-- Pilih Kategori Konten Dinamis --</option>
         @foreach ($ipDinamisKonten as $item)
           <option value="{{ $item->ip_dinamis_konten_id }}">{{ $item->kd_nama_konten_dinamis }}</option>
         @endforeach
       </select>
       <div class="invalid-feedback" id="fk_m_ip_dinamis_konten_error"></div>
     </div>
 
     <div class="form-group">
       <label for="uk_judul_konten">Judul Konten <span class="text-danger">*</span></label>
       <input type="text" class="form-control" id="uk_judul_konten" name="t_ip_upload_konten[uk_judul_konten]" maxlength="200" placeholder="Masukkan judul konten">
       <div class="invalid-feedback" id="uk_judul_konten_error"></div>
     </div>
 
     <div class="form-group">
       <label for="uk_dokumen_konten">File Dokumen (PDF) <span class="text-danger">*</span></label>
       <div class="custom-file">
           <input type="file"
                  class="custom-file-input"
                  id="uk_dokumen_konten"
                  name="uk_dokumen_konten"
                  accept=".pdf">
           <label class="custom-file-label" for="uk_dokumen_konten">Pilih file (PDF, ukuran maksimal 5MB)</label>
           <div class="invalid-feedback" id="uk_dokumen_konten_error"></div>
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
     $(document).ready(function() {
     // Update label file upload
     $('input[type="file"]').on('change', function() {
         var fileName = $(this).val().split('\\').pop();
         $(this).next('.custom-file-label').html(fileName || 'Pilih file');
     });
 
     // Hapus error saat input berubah
     $(document).on('input change', 'input, select, textarea', function() {
         $(this).removeClass('is-invalid');
         const errorId = `#${$(this).attr('id')}_error`;
         $(errorId).html('');
     });
 
     function validateUploadKontenForm() {
         let isValid = true;
 
         // Reset semua error
         $('.is-invalid').removeClass('is-invalid');
         $('.invalid-feedback').html('');
 
         // Validasi Kategori Konten
         const kategoriKonten = $('#fk_m_ip_dinamis_konten').val();
         if (!kategoriKonten) {
             $('#fk_m_ip_dinamis_konten').addClass('is-invalid');
             $('#fk_m_ip_dinamis_konten_error').html('Kategori konten wajib dipilih');
             isValid = false;
         }
 
         // Validasi Judul Konten
         const judulKonten = $('#uk_judul_konten').val().trim();
         if (!judulKonten) {
             $('#uk_judul_konten').addClass('is-invalid');
             $('#uk_judul_konten_error').html('Judul konten wajib diisi');
             isValid = false;
         } else if (judulKonten.length > 200) {
             $('#uk_judul_konten').addClass('is-invalid');
             $('#uk_judul_konten_error').html('Judul konten maksimal 200 karakter');
             isValid = false;
         }
 
         // Validasi File Dokumen
         const fileDokumen = $('#uk_dokumen_konten')[0].files[0];
         if (!fileDokumen) {
             $('#uk_dokumen_konten').addClass('is-invalid');
             $('#uk_dokumen_konten_error').html('File dokumen wajib diunggah');
             isValid = false;
         } else {
             const fileType = fileDokumen.type;
             if (fileType !== 'application/pdf') {
                 $('#uk_dokumen_konten').addClass('is-invalid');
                 $('#uk_dokumen_konten_error').html('File hanya boleh berupa PDF');
                 isValid = false;
             }
 
             const maxSize = 5 * 1024 * 1024; // 5MB
             if (fileDokumen.size > maxSize) {
                 $('#uk_dokumen_konten').addClass('is-invalid');
                 $('#uk_dokumen_konten_error').html('Ukuran file tidak boleh melebihi 5MB');
                 // Reset file input dan label
                 $('#uk_dokumen_konten').val('');
                 $('#uk_dokumen_konten').next('.custom-file-label').html('Pilih file');
                 isValid = false;
             }
         }
         return isValid;
     }
 
     // Handle Submit Form
     $('#btnSubmitForm').on('click', function() {
         if (!validateUploadKontenForm()) {
             Swal.fire({
                 icon: 'error',
                 title: 'Validasi Gagal',
                 text: 'Mohon periksa kembali input Anda.'
             });
             return;
         }
 
         const form = $('#formCreateDetailUploadKonten');
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
 
                     if (typeof reloadTable === 'function') {
                         reloadTable();
                     }
 
                     Swal.fire({
                         icon: 'success',
                         title: 'Berhasil',
                         text: response.message || 'Data berhasil disimpan'
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
                 console.error('Error:', xhr);
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