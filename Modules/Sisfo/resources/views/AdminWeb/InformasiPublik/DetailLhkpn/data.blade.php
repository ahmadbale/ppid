<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $detailLhkpn->firstItem() ?? 0 }} to {{ $detailLhkpn->lastItem() ?? 0 }} of {{ $detailLhkpn->total() ?? 0 }} results
     </div>
 </div>

 <div class="table-responsive">
 <table class="table table-responsive-stack table-bordered table-striped table-hover table-sm">
     <thead class="text-center">
         <tr>
             <th width="10%">No</th>
             <th width="20%">Tahun</th>
             <th width="40%">Nama Karyawan</th>
             <th width="30%">Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($detailLhkpn as $key => $item)
         <tr>
             <td table-data-label="Nomor" class="text-center">{{ ($detailLhkpn->currentPage() - 1) * $detailLhkpn->perPage() + $key + 1 }}</td>
             <td table-data-label="Tahun" class="text-center">{{ $item->lhkpn->lhkpn_tahun }}</td>
             <td table-data-label="Nama Karyawan" class="text-start">{{ $item->dl_nama_karyawan }}</td>
             {{-- <td table-data-label="Nomor" class="text-center">{{ $item->lhkpn->lhkpn_judul_informasi }}</td> --}}
             <td table-data-label="Aksi" class="text-center">
                 <button class="btn btn-sm btn-warning" onclick="modalAction('{{ url("adminweb/informasipublik/detail-lhkpn/editData/{$item->detail_lhkpn_id}") }}')">
                     <i class="fas fa-edit"></i> Edit
                 </button>
                 <button class="btn btn-sm btn-info" onclick="modalAction('{{ url("adminweb/informasipublik/detail-lhkpn/detailData/{$item->detail_lhkpn_id}") }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                 <button class="btn btn-sm btn-danger" onclick="modalAction('{{ url("adminweb/informasipublik/detail-lhkpn/deleteData/{$item->detail_lhkpn_id}") }}')">
                     <i class="fas fa-trash"></i> Hapus
                 </button>
             </td>
         </tr>
         @empty
         <tr>
             <td colspan="5" class="text-center">
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
{{ $detailLhkpn->appends(['search' => $search])->links() }}
 </div>
