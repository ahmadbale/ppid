@php 
    use Modules\Sisfo\App\Models\Website\WebMenuModel;
    use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
    $penyelesaianSengketaUrl = WebMenuModel::getDynamicMenuUrl('penyelesaian-sengketa');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $penyelesaianSengketa->firstItem() }} to {{ $penyelesaianSengketa->lastItem() }} of {{ $penyelesaianSengketa->total() }} results
     </div>
 </div>
 <div class="table-responsive">
    <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
        <thead>
             <tr>
                 <th width="5%">Nomor</th>
                 <th width="15%">Kode Penyelesaian Sengketa</th>
                 <th width="50%">Nama Penyelesaian Sengketa</th>
                 <th width="15%">Aksi</th>
             </tr>
         </thead>
         <tbody>
            @forelse($penyelesaianSengketa as $key => $item)
            <tr>
                <td>{{ ($penyelesaianSengketa->currentPage() - 1) * $penyelesaianSengketa->perPage() + $key + 1 }}</td>
                <td>{{ $item->ps_kode }}</td>
                <td>{{ $item->ps_nama }}</td>
                <td>
                   @if(
                       Auth::user()->level->hak_akses_kode === 'SAR' ||
                       SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $penyelesaianSengketaUrl, 'update')
                   )
                       <button class="btn btn-sm btn-warning"
                           onclick="modalAction('{{ url($penyelesaianSengketaUrl . '/editData/' . $item->penyelesaian_sengketa_id) }}')">
                           <i class="fas fa-edit"></i> Edit
                       </button>
                   @endif
                   <button class="btn btn-sm btn-info"
                       onclick="modalAction('{{ url($penyelesaianSengketaUrl . '/detailData/' . $item->penyelesaian_sengketa_id) }}')">
                       <i class="fas fa-eye"></i> Detail
                   </button>
                   @if(
                       Auth::user()->level->hak_akses_kode === 'SAR' ||
                       SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $penyelesaianSengketaUrl, 'delete')
                   )
                       <button class="btn btn-sm btn-danger"
                           onclick="modalAction('{{ url($penyelesaianSengketaUrl . '/deleteData/' . $item->penyelesaian_sengketa_id) }}')">
                           <i class="fas fa-trash"></i> Hapus
                       </button>
                   @endif
               </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">
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
        {{ $penyelesaianSengketa->appends(['search' => $search])->links() }}
    </div>