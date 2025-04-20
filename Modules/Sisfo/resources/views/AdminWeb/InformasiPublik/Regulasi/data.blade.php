<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $regulasi->firstItem() }} to {{ $regulasi->lastItem() }} of {{ $regulasi->total() }} results
     </div>
 </div>
 
 <div class="table-responsive">
 <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">

     <thead class="text-center">
         <tr>
             <th >Nomor</th>
             <th >Kategori Regulasi</th>
             <th >Judul</th>
             <th >Tipe</th>
             <th >Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($regulasi as $key => $item)
         <tr>
             <td table-data-label="Nomor" class="text-center">{{ ($regulasi->currentPage() - 1) * $regulasi->perPage() + $key + 1 }}</td>
             <td table-data-label="Kategori" class="text-center">{{ $item->KategoriRegulasi->kr_nama_kategori }}</td>
             <td table-data-label="Judul" class="text-center">{{ $item->reg_judul }}</td>
             <td table-data-label="Tipe" class="text-center">{{ $item->tipe }}</td>
             <td table-data-label="Aksi" class="text-center">
                 @if($item->reg_tipe_dokumen == 'file')
                     <span class="badge badge-primary">File</span>
                 @else
                     <span class="badge badge-info">Link</span>
                 @endif
             </td>
             <td>
                 <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/informasipublik/regulasi/editData/{$item->regulasi_id}") }}')">
                     <i class="fas fa-edit"></i> Edit
                 </button>
                 <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/informasipublik/regulasi/detailData/{$item->regulasi_id}") }}')">
                     <i class="fas fa-eye"></i> Detail
                 </button>
                 <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/informasipublik/regulasi/deleteData/{$item->regulasi_id}") }}')">
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
     {{ $regulasi->appends(['search' => $search])->links() }}
 </div>

