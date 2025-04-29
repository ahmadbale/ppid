<div class="modal-header bg-primary text-white">
     <h5 class="modal-title">Detail IpDinamis Konten</h5>
     <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
     </button>
 </div>
 
 <div class="modal-body">
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
                     <th>Dibuat Pada</th>
                     <td>{{ $ipDinamisKonten->created_at ? date('d-m-Y H:i:s', strtotime($ipDinamisKonten->created_at)) : '-' }}</td>
                 </tr>
                 <tr>
                     <th>Diperbarui Oleh</th>
                     <td>{{ $ipDinamisKonten->updated_by ?? '-' }}</td>
                 </tr>
                 <tr>
                     <th>Terakhir Diperbarui</th>
                     <td>{{ $ipDinamisKonten->updated_at ? date('d-m-Y H:i:s', strtotime($ipDinamisKonten->updated_at)) : '-' }}</td>
                 </tr>
             </table>
         </div>
     </div>
 </div>
 
 <div class="modal-footer">
     <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
 </div>