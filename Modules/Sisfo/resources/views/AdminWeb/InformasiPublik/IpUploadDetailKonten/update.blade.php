@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
   $IpUploadKontenUrl= WebMenuModel::getDynamicMenuUrl('upload-detail-konten');
@endphp
<div class="modal-header">
     <h5 class="modal-title">Ubah Detail Upload Konten</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
     
 </div>
 
 <div class="modal-body">
     <form id="formUpdateDetailUploadKonten"  action="{{ url($IpUploadKontenUrl . '/updateData/'  . $ipUploadKonten->ip_upload_konten_id) }}" method="POST" enctype="multipart/form-data">
         @csrf
         
         <div class="form-group">
             <label for="fk_m_ip_dinamis_konten">Kategori Konten Dinamis <span class="text-danger">*</span></label>
             <select class="form-control" id="fk_m_ip_dinamis_konten" name="t_ip_upload_konten[fk_m_ip_dinamis_konten]">
                 <option value="">-- Pilih Kategori Konten Dinamis --</option>
                 @foreach ($ipDinamisKonten as $item)
                     <option value="{{ $item->ip_dinamis_konten_id }}" 
                             {{ $ipUploadKonten->fk_m_ip_dinamis_konten == $item->ip_dinamis_konten_id ? 'selected' : '' }}>
                         {{ $item->kd_nama_konten_dinamis }}
                     </option>
                 @endforeach
             </select>
             <div class="invalid-feedback" id="fk_m_ip_dinamis_konten_error"></div>
         </div>
 
         <div class="form-group">
             <label for="uk_judul_konten">Judul Konten <span class="text-danger">*</span></label>
             <input type="text" class="form-control" id="uk_judul_konten" 
                    name="t_ip_upload_konten[uk_judul_konten]" 
                    maxlength="200" 
                    placeholder="Masukkan judul konten"
                    value="{{ $ipUploadKonten->uk_judul_konten }}">
             <div class="invalid-feedback" id="uk_judul_konten_error"></div>
         </div>
 
         <div class="form-group">
             <label for="uk_dokumen_konten">File Dokumen (PDF) <span class="text-danger">*</span></label>
             <div class="custom-file">
                 <input type="file" class="custom-file-input" id="uk_dokumen_konten" name="uk_dokumen_konten" accept=".pdf">
                 <label class="custom-file-label" for="uk_dokumen_konten">
                     Pilih file (PDF, ukuran maksimal 5MB)
                 </label>
                 <div class="invalid-feedback" id="uk_dokumen_konten_error"></div>
             </div>
             @if($ipUploadKonten->uk_dokumen_konten)
                 <div class="mt-2">
                     <p class="mb-1">File dokumen saat ini:</p>
                     <a href="{{ Storage::url($ipUploadKonten->uk_dokumen_konten) }}" target="_blank" class="btn btn-sm btn-info">
                         <i class="fas fa-file-pdf"></i> Lihat File
                     </a>
                 </div>
             @endif
             <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah file.</small>
         </div>
     </form>
 </div>
 
 <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
     <button type="button" class="btn btn-success" id="btnSubmitForm">
         <i class="fas fa-save mr-1"></i> Simpan Perubahan
     </button>
 </div>
 
 <script>
     $(document).ready(function() {
         // Update label file upload
         $('input[type="file"]').on('change', function() {
             var fileName = $(this).val().split('\\').pop();
             $(this).next('.custom-file-label').html(fileName || 'Pilih file (PDF, ukuran maksimal 5MB)');
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
 
             // Validasi File Dokumen (opsional pada saat update)
             const fileDokumen = $('#uk_dokumen_konten')[0].files[0];
             if (fileDokumen) {
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
                     $('#uk_dokumen_konten').next('.custom-file-label').html('Pilih file (PDF, ukuran maksimal 5MB)');
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
 
             const form = $('#formUpdateDetailUploadKonten');
             const formData = new FormData(form[0]);
             const button = $(this);
 
             button.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...</i>').attr('disabled', true);
 
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
                             text: response.message || 'Data berhasil diperbarui'
                         });
                     } else {
                         Swal.fire({
                             icon: 'error',
                             title: 'Gagal',
                             text: response.message || 'Terjadi kesalahan saat memperbarui data'
                         });
                     }
                 },
                 error: function(xhr) {
                     console.error('Error:', xhr);
                     
                     // Cek apakah ada error validasi dari server
                     if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                         const errors = xhr.responseJSON.errors;
                         
                         // Tampilkan error pada form
                         Object.keys(errors).forEach(function(key) {
                             // Cek apakah key adalah nested field
                             if (key.includes('.')) {
                                 const parts = key.split('.');
                                 if (parts[0] === 't_ip_upload_konten') {
                                     const fieldName = parts[1];
                                     const errorMsg = errors[key][0];
                                     
                                     // Tambahkan class error dan tampilkan pesan
                                     $(`#${fieldName}`).addClass('is-invalid');
                                     $(`#${fieldName}_error`).html(errorMsg);
                                 }
                             } else {
                                 // Untuk field non-nested
                                 $(`#${key}`).addClass('is-invalid');
                                 $(`#${key}_error`).html(errors[key][0]);
                             }
                         });
                         
                         Swal.fire({
                             icon: 'error',
                             title: 'Validasi Gagal',
                             text: 'Mohon periksa kembali input Anda.'
                         });
                     } else {
                         Swal.fire({
                             icon: 'error',
                             title: 'Gagal',
                             text: 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi.'
                         });
                     }
                 },
                 complete: function() {
                     button.html('<i class="fas fa-save mr-1"></i> Simpan Perubahan').attr('disabled', false);
                 }
             });
         });
     });
 </script>