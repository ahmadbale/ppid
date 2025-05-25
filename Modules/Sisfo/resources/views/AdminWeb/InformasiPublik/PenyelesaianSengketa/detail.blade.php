<div class= "modal-header">
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
</div>

<div class="modal-body">
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
     @if(isset($penyelesaianSengketa->file_attachment) && $penyelesaianSengketa->file_attachment)
         <div class="card mt-3">
             <div class="card-header">
                 <h5 class="card-title">Lampiran</h5>
             </div>
             <div class="card-body">
               <div class="mb-3">
                 <a href="{{ asset('storage/' . $penyelesaianSengketa->file_attachment) }}" target="_blank" class="btn btn-primary">
                     <i class="fas fa-download"></i> Lihat File
                 </a>
                 <span class="ml-2 text-muted">{{ basename($penyelesaianSengketa->file_attachment) }}</span>
             </div>
          </div>
         </div>
         @endif
     </div>
<div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>  
</div>
<style>
     .content-preview {
         max-height: 500px;
         overflow-y: auto;
         background-color: #fff;
     }
 
     .content-preview img {
         max-width: 100%;
         height: auto;
     }
 
     .table th {
         font-weight: 600;
         color: #555;
     }
 </style>
