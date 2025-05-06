@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $IpUploadKontenUrl = WebMenuModel::getDynamicMenuUrl('upload-detail-konten');
@endphp
<div class="modal-header">
     <h5 class="modal-title">Konfirmasi Hapus Upload Konten</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 <div class="modal-body">
     <div class="alert alert-danger mt-3">
         <i class="fas fa-exclamation-triangle mr-2"></i> Menghapus Upload Konten ini akan menghapus data dan file terkait.
         Apakah Anda yakin ingin menghapus Upload Konten dengan detail berikut:
     </div>
     
     <div class="card">
         <div class="card-body">
             <table class="table table-borderless">
                 <tr>
                     <th width="200">ID Upload Konten</th>
                     <td>{{ $ipUploadKonten->ip_upload_konten_id }}</td>
                 </tr>
                 <tr>
                     <th>Kategori Konten Dinamis</th>
                     <td>{{ $ipUploadKonten->IpDinamisKonten->kd_nama_konten_dinamis }}</td>
                 </tr>
                 <tr>
                     <th>Judul Konten</th>
                     <td>{{ $ipUploadKonten->uk_judul_konten }}</td>
                 </tr>
                 <tr>
                     <th>Dokumen Konten</th>
                     <td>
                         @if($ipUploadKonten->uk_dokumen_konten)
                             <a href="{{ Storage::url($ipUploadKonten->uk_dokumen_konten) }}" 
                                target="_blank" 
                                class="btn btn-sm btn-info">
                                 <i class="fas fa-file-pdf mr-1"></i> Lihat Dokumen
                             </a>
                         @else
                             Tidak ada dokumen
                         @endif
                     </td>
                 </tr>
                 <tr>
                     <th>Dibuat Oleh</th>
                     <td>{{ $ipUploadKonten->created_by ?? '-' }}</td>
                 </tr>
                 <tr>
                     <th>Tanggal Dibuat</th>
                     <td>{{ $ipUploadKonten->created_at ? date('d-m-Y H:i:s', strtotime($ipUploadKonten->created_at)) : '-' }}</td>
                 </tr>
                 @if($ipUploadKonten->updated_by)
                 <tr>
                     <th>Terakhir Diperbarui</th>
                     <td>{{ $ipUploadKonten->updated_at ? date('d-m-Y H:i:s', strtotime($ipUploadKonten->updated_at)) : '-' }}</td>
                 </tr>
                 <tr>
                     <th>Diperbarui Oleh</th>
                     <td>{{ $ipUploadKonten->updated_by }}</td>
                 </tr>
                 @endif
             </table>
         </div>
     </div>
     <div class="alert alert-warning mt-3">
         <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> 
         Menghapus Data Detail Upload Konten ini akan menghapus file dokumen yang terkait.
     </div>
 </div>
 <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
     <button type="button" class="btn btn-danger" id="confirmDeleteButton" 
         onclick="confirmDelete('{{ url( $IpUploadKontenUrl . '/deleteData/'.$ipUploadKonten->ip_upload_konten_id) }}')">
         <i class="fas fa-trash mr-1"></i> Hapus
     </button>
 </div>
 
 <script>
     function confirmDelete(url) {
         const button = $('#confirmDeleteButton');
         
         button.html('<i class="fas fa-spinner fa-spin"></i> Menghapus...').prop('disabled', true);
         
         $.ajax({
             url: url,
             type: 'DELETE',
             headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
             success: function(response) {
                 $('#myModal').modal('hide');
                 
                 if (response.success) {
                     if (typeof reloadTable === 'function') {
                         reloadTable();
                     }
                     
                     Swal.fire({
                         icon: 'success',
                         title: 'Berhasil',
                         text: response.message
                     });
                 } else {
                     Swal.fire({
                         icon: 'error',
                         title: 'Gagal',
                         text: response.message
                     });
                 }
             },
             error: function(xhr) {
                 Swal.fire({
                     icon: 'error',
                     title: 'Gagal',
                     text: 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.'
                 });
                 
                 button.html('<i class="fas fa-trash mr-1"></i> Hapus').prop('disabled', false);
             }
         });
     }
 </script>