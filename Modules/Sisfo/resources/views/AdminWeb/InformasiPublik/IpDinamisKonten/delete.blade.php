@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $IpdinamisKontenUrl = WebMenuModel::getDynamicMenuUrl('dinamis-konten');
@endphp
<div class="modal-header">
     <h5 class="modal-title">Konfirmasi Hapus IpDinamis Konten</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 <div class="modal-body">
     <div class="alert alert-danger mt-3">
         <i class="fas fa-exclamation-triangle mr-2"></i> Menghapus IpDinamis Konten ini akan berpengaruh pada data terkait.
         Apakah Anda yakin ingin menghapus IpDinamis Konten dengan detail berikut:
     </div>
     
     <div class="card">
         <div class="card-body">
             <table class="table table-borderless">
                 <tr>
                     <th width="200">ID IpDinamis Konten</th>
                     <td>{{ $ipDinamisKonten->ip_dinamis_konten_id }}</td>
                 </tr>
                 <tr>
                     <th>Nama Konten Dinamis</th>
                     <td>{{ $ipDinamisKonten->kd_nama_konten_dinamis }}</td>
                 </tr>
                 <tr>
                     <th>Dibuat Oleh</th>
                     <td>{{ $ipDinamisKonten->created_by ?? '-' }}</td>
                 </tr>
                 <tr>
                     <th>Tanggal Dibuat</th>
                     <td>{{ $ipDinamisKonten->created_at ? date('d-m-Y H:i:s', strtotime($ipDinamisKonten->created_at)) : '-' }}</td>
                 </tr>
                 @if($ipDinamisKonten->updated_by)
                 <tr>
                     <th>Terakhir Diperbarui</th>
                     <td>{{ $ipDinamisKonten->updated_at ? date('d-m-Y H:i:s', strtotime($ipDinamisKonten->updated_at)) : '-' }}</td>
                 </tr>
                 <tr>
                     <th>Diperbarui Oleh</th>
                     <td>{{ $ipDinamisKonten->updated_by }}</td>
                 </tr>
                 @endif
             </table>
         </div>
     </div>
     <div class="alert alert-warning mt-3">
         <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> 
         Menghapus IpDinamis Konten ini mungkin akan memengaruhi data lain yang terkait dengannya. 
         Pastikan tidak ada data lain yang masih menggunakan IpDinamis Konten ini.
     </div>
 </div>
 <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
     <button type="button" class="btn btn-danger" id="confirmDeleteButton" 
         onclick="confirmDelete('{{ url( $IpdinamisKontenUrl . '/deleteData/'.$ipDinamisKonten->ip_dinamis_konten_id) }}')">
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