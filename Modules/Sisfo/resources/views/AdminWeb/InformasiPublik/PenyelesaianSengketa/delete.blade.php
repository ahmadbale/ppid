@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $penyelesaianSengketaUrl = WebMenuModel::getDynamicMenuUrl('penyelesaian-sengketa');
@endphp
<div class="modal-header">
     <h5 class="modal-title">Konfirmasi Hapus Data Penyelesaian Sengketa</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 
 <div class="modal-body">    
     <div class="alert alert-danger">
         <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus data Penyelesaian Sengketa dengan detail berikut:
     </div>
     
     <div class="card">
        <div class="card-header">
            <h5 class="card-title">Informasi Penyelesaian Sengketa</h5>
        </div>
         <div class="card-body">
             <table class="table table-borderless">
                 <tr>
                     <th width="200">Kode</th>
                     <td>{{ $penyelesaianSengketa->ps_kode ?? '-' }}</td>
                 </tr>
                 <tr>
                     <th>Nama</th>
                     <td>{{ $penyelesaianSengketa->ps_nama ?? '-' }}</td>
                 </tr>
                 <tr>
                     <th>Tanggal Dibuat</th>
                     <td>{{ date('d-m-Y H:i:s', strtotime($penyelesaianSengketa->created_at)) }}</td>
                 </tr>
                 <tr>
                     <th>Dibuat Oleh</th>
                     <td>{{ $penyelesaianSengketa->created_by ?? '-' }}</td>
                 </tr>
                 @if($penyelesaianSengketa->updated_by ?? null)
                 <tr>
                     <th>Terakhir Diperbarui</th>
                     <td>{{ date('d-m-Y H:i:s', strtotime($penyelesaianSengketa->updated_at)) }}</td>
                 </tr>
                 <tr>
                     <th>Diperbarui Oleh</th>
                     <td>{{ $penyelesaianSengketa->updated_by }}</td>
                 </tr>
                 @endif
             </table>
         </div>
     </div>

     <div class="card mt-3">
        <div class="card-header">
            <h5 class="card-title">Deskripsi</h5>
        </div>
        <div class="card-body">
            {!! $penyelesaianSengketa->ps_deskripsi ?? '<div class="alert alert-info">Tidak ada deskripsi yang tersedia.</div>' !!}
        </div>
    </div>
 
     <div class="alert alert-warning mt-3">
         <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> 
         Menghapus data Penyelesaian Sengketa ini akan menghapus semua data terkait. 
         Tindakan ini tidak dapat dibatalkan.
     </div>
 </div>
 
 <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
     <button type="button" class="btn btn-danger" id="confirmDeleteButton" 
     onclick="confirmDelete('{{ url( $penyelesaianSengketaUrl . '/deleteData/' . $penyelesaianSengketa->penyelesaian_sengketa_id) }}')">
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