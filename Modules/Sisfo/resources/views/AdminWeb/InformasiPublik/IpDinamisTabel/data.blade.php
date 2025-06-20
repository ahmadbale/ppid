@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
  $IpdinamisTabelUrl = WebMenuModel::getDynamicMenuUrl('kategori-informasi-publik-dinamis-tabel');
@endphp
<div class="d-flex justify-content-between align-items-center mb-2">
     <div class="showing-text">
         Showing {{ $ipDinamisTabel->firstItem() }} to {{ $ipDinamisTabel->lastItem() }} of {{ $ipDinamisTabel->total() }} results
     </div>
 </div>
 
 <div class="table-responsive">
 <table class="table table-responsive-stack align-middle table-bordered table-striped table-hover table-sm">
     <thead class="text-center">
         <tr>
             <th>Nomor</th>
             <th>Nama Submenu</th>
             <th>Judul Informasi</th>
             <th>Aksi</th>
         </tr>
     </thead>
     <tbody>
         @forelse($ipDinamisTabel as $key => $item)
         <tr>
             <td table-data-label="Nomor" class="text-center">{{ ($ipDinamisTabel->currentPage() - 1) * $ipDinamisTabel->perPage() + $key + 1 }}</td>
             <td table-data-label="Nama Submenu" class="text-center">{{ $item->ip_nama_submenu }}</td>
             <td table-data-label="Judul" class="text-center">{{ $item->ip_judul }}</td>
             <td table-data-label="Aksi" class="text-center">
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $IpdinamisTabelUrl, 'update')
                )
                    <button class="btn btn-sm btn-warning"
                        onclick="modalAction('{{ url($IpdinamisTabelUrl . '/editData/' . $item->ip_dinamis_tabel_id) }}')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                <button class="btn btn-sm btn-info"
                    onclick="modalAction('{{ url($IpdinamisTabelUrl . '/detailData/' . $item->ip_dinamis_tabel_id) }}')">
                    <i class="fas fa-eye"></i> Detail
                </button>
                @if(
                    Auth::user()->level->hak_akses_kode === 'SAR' ||
                    SetHakAksesModel::cekHakAkses(Auth::user()->user_id, $IpdinamisTabelUrl, 'delete')
                )
                    <button class="btn btn-sm btn-danger"
                        onclick="modalAction('{{ url($IpdinamisTabelUrl . '/deleteData/' . $item->ip_dinamis_tabel_id) }}')">
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
     {{ $ipDinamisTabel->appends(['search' => $search])->links() }}
 </div>