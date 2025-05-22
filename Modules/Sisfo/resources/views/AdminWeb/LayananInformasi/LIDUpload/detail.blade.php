<div class="modal-header">
     <h5 class="modal-title">{{ $title }}</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
       <span aria-hidden="true">&times;</span>
     </button>
   </div>
   
   <div class="modal-body">
     <div class="card">
       <div class="card-body">
         <table class="table table-borderless">
           <tr>
             <th width="200">Kategori Layanan Informasi Dinamis</th>
             <td>{{ $liUpload->LiDinamis->li_dinamis_nama }}</td>
           </tr>
           <tr>
             <th width="200">Tipe Data Upload</th>
             <td>{{ $liUpload->lid_upload_type }}</td>
           </tr>
           <tr>
             <th width="200">List Data Upload</th>
             <td>
               @if($liUpload->lid_upload_type == 'link')
                   <a href="{{ $liUpload->lid_upload_value }}" target="_blank">
                       {{ $liUpload->lid_upload_value }}
                   </a>
               @elseif($liUpload->lid_upload_type == 'file')
                   <a href="{{ asset('storage/' . $liUpload->lid_upload_value) }}" target="_blank" class="btn btn-sm btn-success">
                       <i class="fas fa-file"></i> Lihat File
                   </a>
               @else
                   {{ $liUpload->lid_upload_value }}
               @endif
             </td>
           </tr>
           <tr>
             <th>Tanggal Dibuat</th>
             <td>{{ date('d-m-Y H:i:s', strtotime($liUpload->created_at)) }}</td>
           </tr>
           <tr>
             <th>Dibuat Oleh</th>
             <td>{{ $liUpload->created_by }}</td>
           </tr>
           @if($liUpload->updated_by)
           <tr>
             <th>Terakhir Diperbarui</th>
             <td>{{ date('d-m-Y H:i:s', strtotime($liUpload->updated_at)) }}</td>
           </tr>
           <tr>
             <th>Diperbarui Oleh</th>
             <td>{{ $liUpload->updated_by }}</td>
           </tr>
           @endif
         </table>
       </div>
     </div>
   </div>
   
   <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
   </div>