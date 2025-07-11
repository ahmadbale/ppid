<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\InformasiPublik\IpDinamisTabel\delete.blade.php -->
@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $IpdinamisTabelUrl = WebMenuModel::getDynamicMenuUrl('kategori-informasi-publik-dinamis-tabel');
@endphp
<div class="modal-header">
     <h5 class="modal-title">Konfirmasi Hapus IP Dinamis Tabel</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span>
     </button>
   </div>
   <div class="modal-body">    
     <div class="alert alert-danger mt-3">
       <i class="fas fa-exclamation-triangle mr-2"></i> Apakah Anda yakin ingin menghapus IP Dinamis Tabel dengan detail berikut:
     </div>
    
     <div class="card">
       <div class="card-body">
         <table class="table table-borderless">
           <tr>
             <th width="200">Nama Submenu</th>
             <td>{{ $IpDinamisTabel->ip_nama_submenu }}</td>
           </tr>
           <tr>
             <th>Judul</th>
             <td>{{ $IpDinamisTabel->ip_judul }}</td>
           </tr>
           <tr>
             <th>Deskripsi</th>
             <td>
               @if($IpDinamisTabel->ip_deskripsi)
                 <div class="border p-2 rounded bg-light">
                   {{ Str::limit($IpDinamisTabel->ip_deskripsi, 200) }}
                 </div>
               @else
                 <em class="text-muted">Tidak ada deskripsi</em>
               @endif
             </td>
           </tr>
           <tr>
             <th>Tanggal Dibuat</th>
             <td>{{ date('d-m-Y H:i:s', strtotime($IpDinamisTabel->created_at)) }}</td>
           </tr>
           <tr>
             <th>Dibuat Oleh</th>
             <td>{{ $IpDinamisTabel->created_by }}</td>
           </tr>
           @if($IpDinamisTabel->updated_by)
           <tr>
             <th>Terakhir Diperbarui</th>
             <td>{{ date('d-m-Y H:i:s', strtotime($IpDinamisTabel->updated_at)) }}</td>
           </tr>
           <tr>
             <th>Diperbarui Oleh</th>
             <td>{{ $IpDinamisTabel->updated_by }}</td>
           </tr>
           @endif
         </table>
       </div>
     </div>
     <div class="alert alert-warning mt-3">
       <i class="fas fa-info-circle mr-2"></i> <strong>Perhatian:</strong> Menghapus IP Dinamis Tabel ini mungkin akan memengaruhi data terkait. Pastikan tidak ada data yang masih bergantung pada IpDinamis Tabel ini.
     </div>
   </div>
   <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
     <button type="button" class="btn btn-danger" id="confirmDeleteButton"
       onclick="confirmDelete('{{ url( $IpdinamisTabelUrl . '/deleteData/'.$IpDinamisTabel->ip_dinamis_tabel_id) }}')">
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