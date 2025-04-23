<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $kategoriRegulasi->firstItem() }} to {{ $kategoriRegulasi->lastItem() }} of {{ $kategoriRegulasi->total() }} results
     </div>
 </div>

 <div class="table-responsive">
 <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
     <thead class="text-center">
         <tr>
             <th >Nomor</th>
             <th >Regulasi Dinamis</th>
             <th >Kode Kategori</th>
             <th >Nama Kategori Regulasi</th>
             <th >Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($kategoriRegulasi as $key => $item)
         <tr>
            <td table-data-label="Nomor" class="text-center">{{ ($kategoriRegulasi->currentPage() - 1) * $kategoriRegulasi->perPage() + $key + 1 }}</td>
            <td table-data-label="Regulasi Dinamis" class="text-center">{{ $item->RegulasiDinamis->rd_judul_reg_dinamis ?? 'Tidak ada' }}</td>
            <td table-data-label="Kode Kategori" class="text-center">{{ $item->kr_kategori_reg_kode }}</td>
            <td table-data-label="Nama Kategori Regulasi" class="text-center">{{ $item->kr_nama_kategori }}</td>
            <td table-data-label="Aksi" class="text-center">
                 <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/informasipublik/kategori-regulasi/editData/{$item->kategori_reg_id}") }}')">
                     <i class="fas fa-edit"></i> Edit
                 </button>
                 <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/informasipublik/kategori-regulasi/detailData/{$item->kategori_reg_id}") }}')">
                     <i class="fas fa-eye"></i> Detail
                 </button>
                 <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/informasipublik/kategori-regulasi/deleteData/{$item->kategori_reg_id}") }}')">
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
     {{ $kategoriRegulasi->appends(['search' => $search])->links() }}
 </div>