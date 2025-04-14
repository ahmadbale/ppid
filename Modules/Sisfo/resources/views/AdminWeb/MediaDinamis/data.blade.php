<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $mediaDinamis->firstItem() }} to {{ $mediaDinamis->lastItem() }} of {{ $mediaDinamis->total() }} results
     </div>
 </div>

 <div class="table-responsive">
 <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
     <thead class="text-center">
         <tr>
             <th >Nomor</th>
             <th >Kategori Media</th>
             <th >Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($mediaDinamis as $key => $item)
         <tr>
             <td table-data-label="Nomor" class="text-center">{{ ($mediaDinamis->currentPage() - 1) * $mediaDinamis->perPage() + $key + 1 }}</td>
             <td table-data-label="Kategori Media" class="text-center">{{ $item->md_kategori_media }}</td>
             <td table-data-label="Aksi" class="text-center">
                 <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/media-dinamis/editData/{$item->media_dinamis_id}") }}')">
                     <i class="fas fa-edit"></i> Edit
                 </button>
                 <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/media-dinamis/detailData/{$item->media_dinamis_id}") }}')">
                     <i class="fas fa-eye"></i> Detail
                 </button>
                 <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/media-dinamis/deleteData/{$item->media_dinamis_id}") }}')">
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
     {{ $mediaDinamis->appends(['search' => $search])->links() }}
 </div>
