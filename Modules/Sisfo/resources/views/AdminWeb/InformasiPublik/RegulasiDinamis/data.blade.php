<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $RegulasiDinamis->firstItem() }} to {{ $RegulasiDinamis->lastItem() }} of {{ $RegulasiDinamis->total() }} results
     </div>
 </div>

 <div class="table-responsive">
 <table class="table table-responsive-stack table-bordered table-striped table-hover table-sm">
     <thead class="text-center">
         <tr>
             <th width="10%">Nomor</th>
             <th width="60%">Nama Regulasi Dinamis</th>
             <th width="30%">Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($RegulasiDinamis as $key => $item)
         <tr>
             <td table-data-label="Nomor" class="text-center">{{ ($RegulasiDinamis->currentPage() - 1) * $RegulasiDinamis->perPage() + $key + 1 }}</td>
             <td table-data-label="Nama Regulasi Dinamis" class="text-center">{{ $item->rd_judul_reg_dinamis }}</td>
             <td table-data-label="Aksi" class="text-center">
                 <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/informasipublik/regulasi-dinamis/editData/{$item->regulasi_dinamis_id}") }}')">
                     <i class="fas fa-edit"></i> Edit
                 </button>
                 <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/informasipublik/regulasi-dinamis/detailData/{$item->regulasi_dinamis_id}") }}')">
                     <i class="fas fa-eye"></i> Detail
                 </button>
                 <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/informasipublik/regulasi-dinamis/deleteData/{$item->regulasi_dinamis_id}") }}')">
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
     {{ $RegulasiDinamis->appends(['search' => $search])->links() }}
 </div>
