<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $ipDinamisKonten->firstItem() }} to {{ $ipDinamisKonten->lastItem() }} of {{ $ipDinamisKonten->total() }} results
     </div>
 </div>
 
 <div class="table-responsive">
 <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
     <thead class="text-center">
         <tr>
             <th>Nomor</th>
             <th>Nama Konten Dinamis</th>
             <th>Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($ipDinamisKonten as $key => $item)
         <tr>
             <td table-data-label="Nomor" class="text-center">{{ ($ipDinamisKonten->currentPage() - 1) * $ipDinamisKonten->perPage() + $key + 1 }}</td>
             <td table-data-label="Nama Konten Dinamis" class="text-center">{{ $item->kd_nama_konten_dinamis }}</td>
             <td table-data-label="Aksi" class="text-center">
                 <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/informasipublik/ipdinamis-konten/editData/{$item->ip_dinamis_konten_id}") }}')">
                     <i class="fas fa-edit"></i> Edit
                 </button>
                 <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/informasipublik/ipdinamis-konten/detailData/{$item->ip_dinamis_konten_id}") }}')">
                     <i class="fas fa-eye"></i> Detail
                 </button>
                 <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/informasipublik/ipdinamis-konten/deleteData/{$item->ip_dinamis_konten_id}") }}')">
                     <i class="fas fa-trash"></i> Hapus
                 </button>
             </td>
         </tr>
         @empty
         <tr>
             <td colspan="3" class="text-center">
                 @if(!empty($search))
                     Tidak ada data yang cocok dengan pencarian "{{ $search }}"
                 @else
                     Tidak ada data
                 @endif
             </td>
         </tr>
         @endforelse
     </tbody>
 </table>
 </div>
 
 <div class="mt-3">
     {{ $ipDinamisKonten->appends(['search' => $search])->links() }}
 </div>